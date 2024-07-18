<?php
/**
 * This file is part of the AppImportBundle package.
 *
 */
namespace App\ImportBundle\Command;

use App\AppBundle\Component\SubdomainHelper;
use App\OfferBundle\Tools\OfferTitleBuilder;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use App\UserBundle\Entity\User;
use Symfony\Component\Filesystem\Filesystem;
/**
 * Class ImportFromInetCommand
 *
 * @author wojciech przygoda
 */
class ImportFromInetCommand extends ContainerAwareCommand
{
    CONST ARCHIV = 'noweoferty.zip';
    CONST FILE = 'oferta.xml';
    /**
     *
     * @var string log file
     */
    protected $logFile;

    /**
     *
     * @var string log file path
     */
    protected $path;
    protected $logger;
    protected $dbConnection;
    protected $output;

    protected static $actions = ['add','remove'];

    /**
     * Constructior
     */
    public function __construct() {
        parent::__construct();
        //$this->path = '/home/webmaster.wgnpl/www/inet/';
        //$this->path = 'E:\\Projekty\\';
        $this->path = __DIR__ .'/../../../../../inet/';

    }
    /**
     * Configure command
     */
    protected function configure()
    {
        $this
            ->setName('import:inet')
            ->setDescription('import offers form inet xml');
    }
    /**
     * Execute command
     *
     * @param InputInterface $input input
     * @param OutputInterface $output output
     * @return type
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        ini_set('max_execution_time', 60*60*3);
        ini_set('memory_limit', '2048M');

        $this->logger = $this->getContainer()->get('monolog.logger.inet');
        $this->output = $output;
        $this->fs = new Filesystem();
        $zip = new \ZipArchive;

        $startTime = microtime(true);
        $this->logger->info('Import start.');

        try{
            if ($zip->open($this->path.self::ARCHIV) !== TRUE) {
                throw new \Exception('plik nie został rozpakowany. ' . $this->path.self::ARCHIV);
            }

            $zip->extractTo($this->path);
            $zip->close();
            $output->writeln('unziped');

            $now = new \DateTime();
            $newName = $now->format('Y-m-d_H').self::ARCHIV;

            $this->fs->rename($this->path.self::ARCHIV, $this->path.$newName);
            $this->logger->info(sprintf('nowa nazwa pliku %s',$newName));

            if(!file_exists($this->path.self::FILE)){
                $output->writeln('file not exist');
                throw new \Exception('plik xml nie istnieje.');
            }

            $xml = simplexml_load_file($this->path.self::FILE);

            foreach($xml->insert_update->record as $record){
                $this->importAction($record);
            }

        }catch(\Exception $ex){
            $this->logger->error($ex->getMessage());
            $message = \Swift_Message::newInstance()
                ->setSubject('wgn import error')
                ->setFrom('powiadomienia@wgn.pl')
                ->setTo('wojtek.przygoda@gmail.com')
                ->setBody($ex->getMessage())
            ;
            $this->getContainer()->get('mailer')->send($message);
        }


        $this->fs->remove($this->path.self::FILE);
        $endTime = microtime(true);

        $this->logger->info(sprintf('Import end. Execution time: %s',($endTime - $startTime)/60));
    }
    /**
     * Remove offer
     * @param array $record offer
     */
    protected function importAction($record){
        $action = (string)$record->akcja;
        $this->output->writeln($action);
        if(in_array($action, self::$actions)){
            $this->{$action}($record);

        }
    }
    protected function remove($record){
        $em = $this->getContainer()->get('doctrine')->getManager();
        $conn = $em->getConnection();
        $importId = $record->numerbiura.'-'.$record->id;
        $this->output->writeln(sprintf('remove import id %s', $importId));
        $offer = $this->getOffer($importId);

        if(empty($offer)) {
            $this->output->writeln(sprintf('cant get offer from database by import id %s', $importId));
        }
        if(isset($offer['id'])){

            $offerSQLQuery = 'UPDATE offer SET isDelete = 0, isPublish = 0, deleteDate = NOW() WHERE importId = :id';
            $stmt = $conn->prepare($offerSQLQuery);
            $stmt->bindValue('id',$importId);
            $stmt->execute();
            $this->output->writeln('usuwam id: '.$offer['id'].' import id:'. $record->numerbiura.'-'.$record->id);
            $this->logger->info(sprintf("oferta id: %s (import id: %s) zostala usunieta.\n",$offer['id'],$importId));
        }else {
            $this->output->writeln('nie można usunać '. $record->numerbiura.'-'.$record->id);
            $this->logger->error(sprintf("oferta import id: %s nie zostala usunieta.\n",$importId));
        }

    }

    /**
     * Add offer
     * @param array $record offer
     */
    protected function add($record){
        $importId = $this->getImportId($record);
        $this->output->writeln(sprintf('add import id %s', $importId));
        if(strlen($importId)< 2) {
            $this->output->writeln(sprintf('import id %s is invalid', $importId));
            return ;
        }
        $offer = $this->getOffer($importId);
        $this->output->writeln('prepare data');
        $data = $this->prepareData($record);

        if($offer){
            $this->logger->info( sprintf("aktualizacja oferty id %s ( import id: %s).",$offer['id'],$this->getImportId($record)));
            $data['id'] = $offer['id'];
            $idOffer = $this->update($data);
        }else{
            $idOffer = $this->insert($data);
            $this->logger->info(sprintf("nowa oferta id: %s (import id: %s).\n",$idOffer,$this->getImportId($record)));
        }

        $this->clearMedia($idOffer);
        if((string)$record->woda == '1'){
            //woda
            $this->addMedia(1, $idOffer);
        }
        if((string)$record->prad == '1'){
            //prad
            $this->addMedia(2, $idOffer);
        }
        if((string)$record->gaz == '1'){
            //gaz
            $this->addMedia(4, $idOffer);
        }
        if((string)$record->kanalizacja =='1'){
            $this->addMedia(5, $idOffer);
        }
        if((string)$record->ogrzewanie !== ''){
            $this->addMedia(9, $idOffer);
        }

        $this->clearAdditionalInfo($idOffer);

        if((string)$record->garaz == '1' || (string)$record->parking !== ''){
            $this->addAdditionalInfo(7, $idOffer);
        }

        if((string)$record->windatow == '1' || (string)$record->windaosobowa== '1'){
            $this->addAdditionalInfo(8, $idOffer);
        }

        if((string)$record->balkon == '1'
            || (string)$record->taras == '1'
            || (string)$record->loggia == '1' ){
            $this->addAdditionalInfo(9, $idOffer);
        }

        if((string)$record->ogrodek == '1'){
            $this->addAdditionalInfo(10, $idOffer);
        }

        if((string)$record->kondygnacje == '2'){
            $this->addAdditionalInfo(11, $idOffer);
        }
        if((string)$record->monitoring == '1'){
            $this->addAdditionalInfo(12, $idOffer);
        }

        if((string)$record->anekskuchenny == '' && ($data['category_id'] ==1 || $data['category_id'] ==2)){
            $this->addAdditionalInfo(13, $idOffer);
        }

        if((string)$record->klimatyzacja == '1'){
            $this->addAdditionalInfo(14, $idOffer);
        }

        if((string)$record->piwnica == '1'){
            $this->addAdditionalInfo(16, $idOffer);
        }

        if((string)$record->inst_alarmowa == '1'){
            $this->addAdditionalInfo(20, $idOffer);
        }
        $this->addFotos($record, $idOffer);
    }
    /**
     * Add Photos to offer
     * @param array $record offer
     * @param int $idOffer id offer form database
     */
    protected function addFotos($record, $idOffer){
        $em = $this->getContainer()->get('doctrine')->getManager();
        $conn = $em->getConnection();
        $path = __DIR__ . '/../../../../web/uploads/offers/'.$idOffer;
        if(!file_exists($path)){
           $this->fs->mkdir($path);
        }
        $imageDeleteSQLQuery = 'DELETE FROM offer_image WHERE offer_id = :idOffer';
        $stmtD = $conn->prepare($imageDeleteSQLQuery);
        $stmtD->bindValue('idOffer',$idOffer);
        $stmtD->execute();
        $ordering = 1;
        foreach($record->fotos->foto as $foto){
            $this->addPhoto($foto,$record->fotopath,$ordering,$idOffer,$path);
            $ordering++;
        }

        if(count($record->fotos->foto)==0){
            $this->clearMainPhoto($idOffer);
        }

    }
    /**
     * Add single offer
     *
     * @param \SimpleXMLElement $foto foto from xml
     * @param string $fotoPath foto path
     * @param int $ordering ordering
     * @param int $idOffer offer id
     * @param string $path offer path
     */
    protected function addPhoto($foto,$fotoPath,$ordering,$idOffer,$path){
        $em = $this->getContainer()->get('doctrine')->getManager();
        $conn = $em->getConnection();

        $fileName = (string)$foto->plik;
        $imageSQLQuery = 'SELECT id, modifyDate FROM offer_image WHERE name = :name AND offer_id = :idOffer';
        $stmt = $conn->prepare($imageSQLQuery);
        $stmt->bindValue('name',$fileName);
        $stmt->bindValue('idOffer',$idOffer);

        $image = $stmt->fetch();

        $modifyDate = new \DateTime();
        if(!$image){
            $modifyDate->setTimestamp((int)$foto->data);

            $insertImageSQL = "INSERT INTO offer_image (name,ordering,modifyDate,offer_id) VALUES(:name,:ordering,NOW(),:idOffer)";
            $stmt = $conn->prepare($insertImageSQL);
            $stmt->bindValue('name',$fileName);
            $stmt->bindValue('ordering',(int)$ordering);
            $stmt->bindValue('idOffer',(int)$idOffer);
            $stmt->execute();

            $modifyDate = date('Y-m-d');
            @copy($fotoPath.$fileName,$path.'/'.$fileName);

        }else {
            $time = strtotime($image['modifyDate']);
            $modifyDate->setTimestamp((int)$foto->data);
            $updateImageSQL = "UPDATE offer_image SET modifyDate = NOW() ,ordering = :ordering, name = :name WHERE id = :id";
            $stmt4 = $conn->prepare($updateImageSQL);
            $stmt4->bindValue('name',$fileName);
            $stmt4->bindValue('ordering',(int)$ordering);
            $stmt4->bindValue('id',(int)$image['id']);
            $stmt4->execute();

            if($time!=(int)$foto->data){
                @copy($fotoPath.$fileName,$path.'/'.$fileName);
            }
        }
        if($ordering ==1){
            $this->setAsMainPhoto($fileName, $idOffer);
        }
        $ordering++;
    }
    /**
     * Set as main photo
     *
     * @param string $fileName file name
     * @param int $idOffer offer id
     */
    protected function setAsMainPhoto($fileName,$idOffer){
        $em = $this->getContainer()->get('doctrine')->getManager();
        $conn = $em->getConnection();

        $updateOfferSQL = "UPDATE offer SET mainPhoto = :mainPhoto WHERE id = :id";
        $stmt = $conn->prepare($updateOfferSQL);
        $stmt->bindValue('mainPhoto',$fileName);
        $stmt->bindValue('id',$idOffer);
        $stmt->execute();
    }
    /**
     * Clear main photo
     * @param int $idOffer offer id
     */
    protected function clearMainPhoto($idOffer){
        $em = $this->getContainer()->get('doctrine')->getManager();
        $conn = $em->getConnection();

        $updateOfferSQL = "UPDATE offer SET mainPhoto = '' WHERE id = :id";
        $stmt = $conn->prepare($updateOfferSQL);
        $stmt->bindValue('id',$idOffer);
        $stmt->execute();
    }
    /**
     * Get offer form database
     *
     * @param int $id  offer id
     * @return array
     */
    protected function getOffer($id){
        $em = $this->getContainer()->get('doctrine')->getManager();
        $conn = $em->getConnection();

        $offerSQLQuery = 'SELECT id FROM offer WHERE importId = :id ORDER BY id DESC';
        $stmt = $conn->prepare($offerSQLQuery);
        $stmt->bindValue('id',$id);
        $stmt->execute();
        return $stmt->fetch();
    }
    /**
     * Get office id from database
     *
     * @param int $id office import id
     * @return array
     */
    protected function getIdOffice($id){
        $em = $this->getContainer()->get('doctrine')->getManager();
        $conn = $em->getConnection();

        $officeSQLQuery = 'SELECT id FROM office WHERE importId = :id';
        $stmt = $conn->prepare($officeSQLQuery);
        $stmt->bindValue('id',$id);
        $stmt->execute();
        $office = $stmt->fetch();

        if($office){
            $this->logger->info(sprintf("znaleziono biuro id: %s (import id: %s)", $office['id'],$id));
            return $office['id'];
        }
        $this->logger->error(sprintf("znaleziono biuro import id: %s", $id));
        return null;
    }
    /**
     * Get agent id
     *
     * @param int $id import id
     * @return array
     */
    protected function getIdAgent($id){
        $em = $this->getContainer()->get('doctrine')->getManager();
        $conn = $em->getConnection();

        $userSQLQuery = 'SELECT id FROM user WHERE importId = :id';
        $stmt = $conn->prepare($userSQLQuery);
        $stmt->bindValue('id',(int)$id);
        $stmt->execute();
        $user = $stmt->fetch();

        if($user){
            $this->logger->info(sprintf("znaleziono użytkownika id: %s (import id: %s)", $user['id'],$id));
            return $user['id'];
        }

        $this->logger->error(sprintf("nie znaleziono użytkownika import id:  %s", $id));
        return null;
    }
    protected function prepareSubdomain($data)
    {
        $searchManager = $this->getContainer()->get('search.manager');
        $query = $data['region'];

        if(empty($data['region']) || empty($data['city']))
        {
            return '';
        }
        if($data['district']!='' && strpos($data['district'], 'grodzki') === false)
        {
            $query .= ', ' .  $data['district'];
        }

        $query .= ', ' . $data['city'];

        $location = $searchManager->findClosestLocation($query);

        if(!$location)
        {
            return '';
        }

        if(strtolower($location['city']) != strtolower($data['city']))
        {
            return '';
        }


        return SubdomainHelper::prepareSubdomainFromParameters($data['category_id'],$data['transaction_type_id'],$location['uniqueKey']);
    }
    /**
     * Prepere data to insert
     *
     * @param \SimpleXMLElement $record offer from xml
     * @return array
     */
    protected function prepareData($record){
        $cat = (string)$record->kategoria;

        $idCategory = $this->getCategory($cat);
        $idTransactionType = $this->getTransaction($cat);

        $dataType = $this->getType($idCategory,
                                   (string)$record->kategoriaszcz,
                                   (string)$record->typ_mieszkania,
                                   (string)$record->umiejscowienie
                                    );
        $idOffice = $this->getIdOffice((int)$record->numerbiura);
        $idAgent = $this->getIdAgent((int)$record->idagent);

        $idType = $dataType['type'];
        $description = (string)$record->opis;
        $region = (string)$record->woj;
        $powiat = (string)$record->powiat;

        $city = (string)$record->miasto;

        $subdomain = $this->getContainer()->get('offer.manager')->prepareSubdomain([
            'category_id'=>$idCategory,
            'transaction_type_id'=>$idTransactionType,
            'region'=>$region,
            'district'=>$powiat,
            'city'=>$city,
        ]);



        $street = '';
        if($record->dzielnadm!=''){
          $street  .= (string)$record->dzielnadm;
        }
        //$street .= (string)$record->ulica;
        $squere = (float)$record->pow;
        if(!$squere){
            $squere = (float)$record->dzialka;
        }
        $squerePlot = (float)$record->dzialka;

        if($idTransactionType==1){
            $price = (float)$record->cenas;
        }else{
            $price = (float)$record->cenaw;
        }
        if($price>0 && $squere > 0){
            $pricem2 = $price / $squere;
        }else {
            $pricem2 = 0;
        }
        $floor = (int)$record->pietro;
        $rooms=(int)$record->pokoje;
        $storeys=(int)$record->kondygnacje;
        $lat = (string)$record->wspbn;
        $lng = (string)$record->wspln;
        $yearOfBuilding = (string)$record->rokbudowy;
        $idMarket = ((int)$record->pierwotny)? 1 : 2;
        $video= (string)$record->youtube;
        $name = trim($record->tytul);
        if($name=='')$name = $this->prepareName(array('idCategory'=>$idCategory,
                                         'idTransaction'=>$idTransactionType,
                                         'idType'=>$idType,
                                         'city'=>$city,
                                         'dzielnica'=>$record->dzielnzw,
                                         'osiedle'=>$record->rejon))."";//...

        $idCountry = $this->getCountryId($record->woj);
        if(!$idCountry){
            $idCountry = 1;
        }
        $now = new \DateTime();
        $isDirect = ((int)$record->bezposrednia)? 1: 0;
        $isExclusive = ((int)$record->wylacznosc)? 1: 0;
        $importId = $this->getImportId($record);
        $expire = clone $now;
        $updateDate = clone $now;
        $days = 7300;
        $expire->modify('+ 7300 days');

        if(isset($record->data_waznosci)){
            $expireDate = (string)$record->data_waznosci;
        }else {
            $expireDate = $expire->format('Y-m-d H:i:s');
        }

        $isSpecial = ((int)$record->wyroznienie)? 1: 0;
        $promoDays = 90;
        $promoExpire = $expire;

        $locationIndex = $region.', '.$powiat.', '.$city.', '.$street;
        $biuro = (string)$record->numerbiura;

        if(strlen($biuro)==2){
            $biuro = '0'.$record->numerbiura;
        }else if(strlen($biuro)==1){
            $biuro = '00'.$record->numerbiura;
        }

        $signature = $cat.$biuro.'-'.$record->identyfikator;
        $params = array('category_id'=>$idCategory,
                        'transaction_type_id'=>$idTransactionType,
                        'type_id'=>$idType,
                        'subdomain'=>$subdomain,
                        'importId'=>$importId,
                        'locationIndex'=>$locationIndex,
                        'description'=>nl2br($description),
                        'signature'=>$signature,
                        'region'=>$region,
                        'city'=>$city,
                        'district'=>$powiat,
                        'street'=>$street,
                        'section'=>$record->dzielnzw,
                        'sub_section'=>$record->rejon,
                        'squere'=>$squere,
                        'squerePlot'=>$squerePlot,
                        'price'=>$price,
                        'pricem2'=>$pricem2,
                        'floor'=>$floor,
                        'rooms'=>$rooms,
                        'storeys'=>$storeys,
                        'lat'=>$lat,
                        'lng'=>$lng,
                        'yearOfBuilding'=>$yearOfBuilding,
                        'market_id'=>$idMarket,
                        'video'=>$video,
                        'name'=>$name,
                        'days'=>$days,
                        'expireDate'=>$expireDate,
                        'isDirect'=>$isDirect,
                        'isExclusive'=>$isExclusive,
                        'user_id'=>$idAgent,
                        'agentImportId'=>(int)$record->idagent,
                        'office_id'=>$idOffice,
                        'isSpecial'=>$isSpecial,
                        'country_id'=>$idCountry,
                        'currency_id'=>1,
                        'isPromo'=>0,
                        'isPublish'=> 1,
                        'virtualWalkUrl'=> (string)$record->spacer,
                        'promoDays'=>$promoDays,
                        'roof_id'=>$this->getRoof((string)$record->rodzaj_dachu),
                        'roof_cover_id'=>$this->getRoofCover((string)$record->pokrycie_dachu),
                        'legal_status_id'=>$this->legalStatus((string)$record->wlasnosc),#
                        'technical_condition_id'=>  $this->technicalCondition((string)$record->stan),#
                        'access_road_id' => $this->accessRoad((string)$record->dojazd),#
                        'exhibition_windows_id' =>$this->exhibitionWindows((string)$record->wystawa_okien),
                        'promoExpire'=>$promoExpire->format('Y-m-d H:i:s'),
                        'createDate'=>(string)$record->data_zalozenia,
                        'modifyDate'=>(string)$record->data_modyfikacji,
                        'updateDate'=>(string)$updateDate->format('Y-m-d H:i:s'),
                    );

        return $params;
    }
    /**
     * Get import id
     * @param \SimpleXMLElement $record offer from xml
     * @return int
     */
    protected function getImportId($record){
        return $record->numerbiura.'-'.$record->identyfikator;
    }
    /**
     * Insert offer to database
     *
     * @param array $data offer data
     * @return int
     */
    protected function insert($data) {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $conn = $em->getConnection();
        $offerInsertSQL = 'INSERT INTO offer (';
        $offerInsertSQL .= implode(',',array_keys($data));
        $offerInsertSQL .= ',isDelete) VALUES(';
        $offerInsertSQL .= implode(',', array_map(function($v){return ':'.$v;},array_keys($data)));
        $offerInsertSQL .= ',0)';

        $stmt = $conn->prepare($offerInsertSQL);
        foreach($data as $key=>$val){
            $stmt->bindValue($key,$val);
        }
        $stmt->execute();
        return $conn->lastInsertId();
    }
    /**
     * Update offer
     * @param array $data offer data
     * @return int
     */
    protected function update($data) {

        $em = $this->getContainer()->get('doctrine')->getManager();
        $conn = $em->getConnection();

        unset($data['importId']);
        $offerUpdateSQL = 'UPDATE offer SET '
                            . 'category_id =:category_id , '
                            . 'transaction_type_id = :transaction_type_id,'
                            . 'description = :description,'
                            . 'region = :region, '
                            . 'city = :city, '
                            . 'subdomain = :subdomain,'
                            . 'district = :district, '
                            . 'street = :street, '
                            . 'section = :section, '
                            . 'sub_section = :sub_section, '
                            . 'squere = :squere, '
                            . 'squerePlot = :squerePlot, '
                            . 'price = :price, '
                            . 'pricem2 = :pricem2,'
                            . 'isPromo = :isPromo, '
                            . 'promoDays = :promoDays,'
                            . 'promoExpire = :promoExpire,'
                            . 'floor = :floor, '
                            . 'rooms = :rooms, '
                            . 'storeys = :storeys, '
                            . 'lat = :lat, '
                            . 'lng = :lng,'
                            . 'type_id = :type_id,'
                            . 'yearOfBuilding = :yearOfBuilding, '
                            . 'market_id = :market_id, '
                            . 'video = :video,'
                            . 'name = :name,'
                            . 'locationIndex = :locationIndex,'
                            . 'signature = :signature, '
                            . 'isSpecial = :isSpecial, '
                            . 'days = :days, '
                            . 'expireDate = :expireDate, '
                            . 'isDirect = :isDirect, '
                            . 'isExclusive = :isExclusive,'
                            . 'user_id = :user_id,'
                            . 'agentImportId = :agentImportId,'
                            . 'office_id = :office_id,'
                            . 'modifyDate =:modifyDate, '
                            . 'createDate =:createDate, '
                            . 'updateDate =:updateDate, '
                            . 'roof_id = :roof_id, '
                            . 'roof_cover_id = :roof_cover_id, '
                            . 'exhibition_windows_id = :exhibition_windows_id, '
                            . 'access_road_id = :access_road_id, '
                            . 'technical_condition_id = :technical_condition_id, '
                            . 'legal_status_id = :legal_status_id, '
                            . 'country_id = :country_id, '
                            . 'currency_id = :currency_id, '
                            . 'isPublish = :isPublish, '
                            . 'virtualWalkUrl = :virtualWalkUrl '
                            . ' WHERE id = :id';

        $stmt = $conn->prepare($offerUpdateSQL);

        foreach($data as $key=>$val){

            $stmt->bindValue($key,$val);
        }
        $stmt->execute();

        return $data['id'];
    }
    /**
     * Clear media
     * @param int $idOffer offer id
     */
    protected function clearMedia($idOffer){
        $em = $this->getContainer()->get('doctrine')->getManager();
        $conn = $em->getConnection();
        $offerCoSQLQuery = 'DELETE FROM offer_media WHERE offer_id = :idOffer';
        $stmt22 = $conn->prepare($offerCoSQLQuery);
        $stmt22->bindValue('idOffer',$idOffer);
        $stmt22->execute();
    }
    /**
     * Add media
     * @param int $id media id
     * @param int $idOffer offer id
     */
    protected function addMedia($id,$idOffer){
        $em = $this->getContainer()->get('doctrine')->getManager();
        $conn = $em->getConnection();
        $offerCoSQLQuery = 'SELECT offer_id FROM offer_media WHERE offer_id = :idOffer AND media_id = :idMedia';
        $stmt22 = $conn->prepare($offerCoSQLQuery);
        $stmt22->bindValue('idOffer',$idOffer);
        $stmt22->bindValue('idMedia',$id);
        $stmt22->execute();
        $offerResult2 = $stmt22->fetch();

        if(!$offerResult2){
            $insertCoSQL = "INSERT INTO offer_media (offer_id,media_id) VALUES(:idOffer,:idMedia)";
            $stmt3 = $conn->prepare($insertCoSQL);
            $stmt3->bindValue('idOffer',$idOffer);
            $stmt3->bindValue('idMedia',$id);
            $stmt3->execute();
        }
    }
    /**
     * Add additional info
     *
     * @param int $id additional info id
     * @param int $idOffer offer id
     */
    protected function addAdditionalInfo($id,$idOffer){
        $em = $this->getContainer()->get('doctrine')->getManager();
        $conn = $em->getConnection();
        $offerCoSQLQuery = 'SELECT offer_id FROM offer_additional_info WHERE offer_id = :idOffer AND additional_info_id = :additionalInfoId';
        $stmt22 = $conn->prepare($offerCoSQLQuery);
        $stmt22->bindValue('idOffer',$idOffer);
        $stmt22->bindValue('additionalInfoId',$id);
        $stmt22->execute();
        $offerResult2 = $stmt22->fetch();

        if(!$offerResult2){
            $insertCoSQL = "INSERT INTO offer_additional_info (offer_id,additional_info_id) VALUES(:idOffer,:additionalInfoId)";
            $stmt3 = $conn->prepare($insertCoSQL);
            $stmt3->bindValue('idOffer',$idOffer);
            $stmt3->bindValue('additionalInfoId',$id);
            $stmt3->execute();
        }
    }
    /**
     * Clear additional info
     *
     * @param int $idOffer offer id
     */
    protected function clearAdditionalInfo($idOffer){
        $em = $this->getContainer()->get('doctrine')->getManager();
        $conn = $em->getConnection();
        $infoSQLQuery = 'DELETE FROM offer_additional_info WHERE offer_id = :idOffer';
        $stmt22 = $conn->prepare($infoSQLQuery);
        $stmt22->bindValue('idOffer',$idOffer);
        $stmt22->execute();
    }
    /**
     * Get legal status
     *
     * @param string $status status
     * @return int
     */
    private function legalStatus($status){

        $s = array( 'dzierżawa'=>2,
                    'P.O.D.'=>3,
                    'pełna własność'=>1,
                    'spółdzielcze własn. bez KW'=>4,
                    'spółdzielcze własn. z KW'=>5,
                    'spółdzielcze, własne z KW'=>5,
                    'udział we własności'=>3,
                    'uregulowany'=>3,
                    'użytkowanie wieczyste'=>6,
                    'wlasnosc prywatna'=>1,
                    'własnosc prywatna'=>1,
                    'własnosć'=>1,
                    'własnościowe'=>1,
                    'własność'=>1,
                    'właściciel'=>1,
                    'Właściciel - Skarb Państwa AMW'=>3,
                    'wolny'=>3
            );

        if(isset($s[$status])){
            return $s[$status];
        }

        return null;
    }
    /**
     * Get technical condition
     *
     * @param string $condition condition
     * @return int
     */
    private function technicalCondition($condition){
        $c = array( 'bardzo dobry'=>1,
                    'deweloperski'=>4,
                    'do dokończenia'=>2,
                    'do odświeżenia'=>3,
                    'do remontu'=>3,
                    'do wykończenia'=>2,
                    'dobry'=>1,
                    'idealny'=>1,
                    'komfort'=>1,
                    'nienaganny'=>1,
                    'po generalnym remoncie/nowy'=>1,
                    'po remoncie'=>1,
                    'stan surowy'=>4,
                    'standard'=>1,
                    'surowy otwarty'=>2,
                    'surowy zamkniety'=>2,
                    'surowy zamknięty'=>2,
                    'w budowie / deweloperski'=>4,
                    'wykończony pod klucz'=>1,
                    'wysoki standard'=>1,
                    'zadowalajacy'=>1,
                    'zadowalający'=>1,
                    'zadowaląjacy'=>1,
                    'zły'=>3,
                    'zróżnicowany'=>3,
            );

        if(isset($c[$condition])){
            return $c[$condition];
        }

        return null;
    }

    /**
     * Get access road
     *
     * @param string $value value
     * @return int
     */
    private function accessRoad($value){

        $c = array( 'asfalt'=>1,
                    'asfaltowy'=>1,
                    'bardzo dobry'=>4,
                    'dobry'=>4,
                    'droga asfaltowa'=>1,
                    'droga utwardzona'=>2,
                    'kostka brukowa'=>2,
                    'od ul. Surjana - kierunek na południe'=>4,
                    'polbruk'=>2,
                    'polny'=>3,
                    'polny/leśny'=>3,
                    'utwardzony'=>2,
            );


        if(isset($c[$value])){
            return $c[$value];
        }

        return null;
    }
    /**
     * Get exhibition windows
     *
     * @param type $value value
     * @return int
     */
    private function exhibitionWindows($value){

        $c = array('na północ'=>1,
            'na południe'=>6,
            'na wschód'=>8,
            'na zachód'=>10,
            'półn-poł-wsch'=>11,
            'półn-poł-wsch-zach'=>15,
            'półn-poł-zach'=>12,
            'półn-wsch'=>2,
            'półn-wsch-zach'=>13,
            'półn-zach'=>3,
            'północ-południe'=>7,
            'połud-wsch'=>5,
            'połud-wsch-zach'=>14,
            'połud-zach'=>4,
            'wchód-zachód'=>9,
            );

        if(isset($c[$value])){
            return $c[$value];
        }

        return null;
    }

    /**
     * Get country id
     *
     * @param string $country country
     * @return int
     */
    private function getCountryId($country){
        $em = $this->getContainer()->get('doctrine')->getManager();
        $conn = $em->getConnection();
        $query = 'SELECT * FROM country WHERE name LIKE :country LIMIT 1';
        $stmt= $conn->prepare($query);
        $stmt->bindValue('country','%'.$country.'%');
        $stmt->execute();
        $result = $stmt->fetch();
        if(!$result){
            return null;
        }

        return $result['id'];

    }
    /**
     * Get transaction
     *
     * @param string $key key
     * @return int
     */
    private function getTransaction($key){

        $wynajem = array('WM','WO','WG','WL','WD','WP');
        $sprzedaz = array('SM','SP','SL','SD','SG','SO');
        $parts = explode(',',$key);

        if(in_array(trim($parts[0]),$sprzedaz)){
            return  1;
        }

        if(in_array($parts[0],$wynajem)){
            return 3;
        }
        return 1;
    }

    /**
     * Prepare offer name
     *
     * @param array $params params
     * @return string
     */
    private function prepareName($params){
//        $transaction = ($params['idTransaction']==1)? 'Na sprzedaż' : 'Na wynajem';
//        $name = '';
//        switch($params['idCategory']){
//            case 1:
//                $category = 'Mieszkanie';
//            break;
//            case 2:
//                $category = 'Dom';
//            break;
//            case 3:
//                $category = 'Działka';
//            break;
//            case 4:
//                $category = 'Lokal';
//            break;
//            case 5:
//                $category = 'Nieruchomość komercyjna';
//            break;
//            case 6:
//                $category = 'Garaż';
//            break;
//            default:
//                $category = '';
//                break;
//        }
//        $type = $this->findTypeParts($params['idType']);
//
//        if($type){
//            $name = $type [0] . ' ' . $transaction;
//            if(!empty($type[1]))
//            {
//                $name .= ' ' .$type[1];
//            }
//        }else {
//            $name = $transaction .' ' .$category;
//        }
//
//        if($params['city']!=''){
//            $name .= ' '.$params['city'];
//        }
//        if($params['dzielnica']!='' && $params['dzielnica']!=$params['city']){
//            $name .= ', '.$params['dzielnica'];
//        }
//        if($params['osiedle']!='' && $params['osiedle']!=$params['city'] && $params['osiedle']!=$params['dzielnica']){
//            $name .= ', '.$params['osiedle'];
//        }
//        return $name;
        return OfferTitleBuilder::prepareTitle($params);
    }
    /**
     * Get category
     *
     * @param array $key offer key
     * @return int
     */
    protected function getCategory($key){
        $parts = explode(',',$key);

        $shortKey = substr(trim($parts[0]), 1, 1);

        $cats = array('M'=> 1,
                      'D'=> 2,
                      'L'=> 4,
                      'P'=> 6,
                      'G'=> 3,
                      'O'=> 5);

        if($cats[$shortKey]){
            return $cats[$shortKey];
        }
        return 1;
    }
    /**
     * Find offer type
     *
     * @param string $id type id
     * @return string
     */
    protected function findType($id){
        $type = array(
            '1'=>'mieszkanie w bloku',
            '2'=>'mieszkanie w nowym budynku',
            '3'=>'mieszkanie w kamienicy',
            '4'=>'mieszkanie willowe',
            '5'=>'adaptacja strychu',
            '6'=>'apartament',
            '7'=>'loft',
            '8'=>'pokój',
            '9'=>'dom wolnostojący',
            '10'=>'dom bliźniak',
            '11'=>'dom w zabudowie szeregowej',
            '12'=>'dom z warsztatem',
            '13'=>'dom gospodarczy',
            '14'=>'dom letniskowy',
            '15'=>'willa',
            '16'=>'rezydencja',
            '17'=>'dwór',
            '18'=>'pałac',
            '19'=>'zamek',
            '20'=>'gospodarstwo rolne',
            '21'=>'działka budowlana',
            '22'=>'działka rekreacyjna',
            '23'=>'grunt rolny',
            '24'=>'grunt leśny',
            '25'=>'grunt inwestycyjny',
            '26'=>'lokal biurowy	',
            '27'=>'lokal usługowy',
            '28'=>'lokal gastronomiczny',
            '29'=>'lokal handlowy',
            '30'=>'biurowiec',
            '31'=>'kamienica',
            '32'=>'budynek wielorodzinny',
            '33'=>'budynek handlowy',
            '34'=>'budynek usługowy',
            '35'=>'hotel',
            '36'=>'motel',
            '37'=>'hostel',
            '38'=>'pensjonat',
            '39'=>'ośrodek wypoczynkowy',
            '40'=>'restauracja / pub',
            '41'=>'stadnina',
            '42'=>'magazyn',
            '43'=>'hala produkcyjna',
            '44'=>'hala przemysłowa',
            '45'=>'warsztat',
            '46'=>'inny komercyjny',
            '47'=>'garaż na placu',
            '48'=>'garaż w budynku',
            '49'=>'garaż podziemny',
            '50'=>'garaż wolnostojący',
            '51'=>'miejsce parkingowe',
            '52'=>'nierychomość komercyhna',
            '55'=>'mieszkanie w budynku mieszkalnym',
          );
        if(isset($type[$id])){
            return $type[$id];
        }

        return null;
    }

    /**
     * Get type
     *
     * @param string $kind kind offer
     * @param string $type type offer
     * @return int
     */
    private function getType($kind,$type, $propertyType, $location){
            $types = [];

        $types[5]['budynek'] = array('category'=>5,'type'=>34);
        $types[2]['bliźniak'] = array('category'=>2,'type'=>10);
        $types[2]['budowlany jednorodzinny'] = array('category'=>2,'type'=>9);
        $types[2]['dom z warsztatem'] = array('category'=>2,'type'=>12);
        $types[2]['dworek/pałac'] = array('category'=>2,'type'=>17);
        $types[2]['dworek/posiadłość'] = array('category'=>2,'type'=>17);
        $types[2]['gospodarczy'] = array('category'=>2,'type'=>13);
        $types[5]['gospodarstwo rolne'] = array('category'=>5,'type'=>20);
        $types[5]['inny komercyjny'] = array('category'=>5,'type'=>46);
        $types[5]['kamienica'] = array('category'=>5,'type'=>31);
        $types[2]['letniskowy'] = array('category'=>2,'type'=>14);
        $types[2]['pałac/zamek'] = array('category'=>2,'type'=>18);
        $types[5]['pensjonat'] = array('category'=>5,'type'=>38);
        $types[5]['rekreacyjny/p.o.d.'] = array('category'=>5,'type'=>38);
        $types[2]['siedliskowy'] = array('category'=>2,'type'=>20);
        $types[2]['szeregowiec'] = array('category'=>2,'type'=>11);
        $types[5]['usługowy'] = array('category'=>5,'type'=>34);
        $types[5]['w bloku'] = array('category'=>5,'type'=>32);
        $types[2]['willa/rezydencja'] = array('category'=>2,'type'=>15);
        $types[2]['wolno stojący'] = array('category'=>2,'type'=>9);
        $types[2]['wolnostojący'] = array('category'=>2,'type'=>9);
        $types[2]['wolno stojący'] = array('category'=>2,'type'=>9);
        $types[4]['lokal'] = array('category'=>4,'type'=>27);
        $types[1]['apartament'] = array('category'=>1,'type'=>6);
        $types[4]['biurowy'] = array('category'=>4,'type'=>26);
        $types[2]['bliźniak'] = array('category'=>2,'type'=>10);
        $types[5]['dom z warsztatem'] = array('category'=>2,'type'=>12);
        $types[2]['dworek/posiadłość'] = array('category'=>2,'type'=>17);
        $types[4]['gastronomia'] = array('category'=>4,'type'=>28);
        $types[4]['handlowy'] = array('category'=>4,'type'=>29);
        $types[5]['hotelowy'] = array('category'=>5,'type'=>35);
        $types[5]['inny komercyjny'] = array('category'=>5,'type'=>46);
        $types[5]['kamienica'] = array('category'=>5,'type'=>31);
        $types[5]['magazynowy'] = array('category'=>5,'type'=>42);
        $types[1]['mieszkanie'] = array('category'=>1,'type'=>1);
        $types[2]['pałac/zamek'] = array('category'=>2,'type'=>18);
        $types[5]['pensjonat'] = array('category'=>5,'type'=>38);
        $types[5]['produkcja'] = array('category'=>5,'type'=>43);
        $types[5]['przemysłowy'] = array('category'=>5,'type'=>44);
        $types[3]['rezydencjalny'] = array('category'=>3,'type'=>21);
        $types[5]['rezydencjalny'] = array('category'=>3,'type'=>21);
        $types[4]['usługowy'] = array('category'=>4,'type'=>27);
        $types[4]['hotelowy'] = array('category'=>4,'type'=>27);
        $types[4]['warsztatowy'] = array('category'=>4,'type'=>27);
        $types[4]['przemysłowy'] = array('category'=>4,'type'=>27);
        $types[1]['w bloku'] = array('category'=>1,'type'=>1);
        $types[1]['w kamienicy'] = array('category'=>1,'type'=>3);
        $types[5]['warsztatowy'] = array('category'=>5,'type'=>45);
        $types[1]['willowe'] = array('category'=>1,'type'=>4);
        $types[2]['wolnostojący'] = array('category'=>2,'type'=>9);
        $types[1]['mieszkanie'] = array('category'=>1,'type'=>1);
        $types[1]['adaptacja strychu'] = array('category'=>1,'type'=>5);
        $types[1]['apartament'] = array('category'=>1,'type'=>6);
        $types[4]['biurowy'] = array('category'=>4,'type'=>26);
        $types[5]['inny komercyjny'] = array('category'=>5,'type'=>46);
        $types[1]['mieszkanie'] = array('category'=>1,'type'=>1);
        $types[1]['pokój'] = array('category'=>1,'type'=>8);
        $types[4]['usługowy'] = array('category'=>4,'type'=>27);
        $types[1]['w bloku'] = array('category'=>1,'type'=>1);
        $types[1]['w nowym budynku'] = array('category'=>1,'type'=>2);
        $types[1]['willowe'] = array('category'=>1,'type'=>4);
        $types[2]['wolnostojący'] = array('category'=>2,'type'=>9);
        $types[1]['apartament'] = array('category'=>1,'type'=>6);
        $types[5]['obiekt, budynek '] = array('category'=>5,'type'=>34);
        $types[5]['aktywizacja gospodarcza'] = array('category'=>5,'type'=>46);
        $types[5]['biurowy'] = array('category'=>5,'type'=>30);
        $types[2]['budowlany jednorodzinny'] = array('category'=>2,'type'=>9);
        $types[5]['budowlany wielorodzinny'] = array('category'=>5,'type'=>32);
        $types[5]['budynek wielorodzinny/plomba'] = array('category'=>5,'type'=>32);
        $types[2]['dworek/posiadłość'] = array('category'=>2,'type'=>17);
        $types[5]['gastronomia'] = array('category'=>5,'type'=>40);
        $types[2]['gospodarstwo rolne'] = array('category'=>5,'type'=>20);
        $types[5]['handlowy'] = array('category'=>5,'type'=>33);
        $types[5]['hotelowy'] = array('category'=>5,'type'=>35);
        $types[5]['inny komercyjny'] = array('category'=>5,'type'=>46);
        $types[5]['inny niekomercyjny'] = array('category'=>5,'type'=>52);
        $types[5]['kamienica'] = array('category'=>5,'type'=>31);
        $types[5]['magazynowy'] = array('category'=>5,'type'=>42);
        $types[5]['pensjonat'] = array('category'=>5,'type'=>38);
        $types[5]['produkcja'] = array('category'=>5,'type'=>43);
        $types[5]['rekreacyjny'] = array('category'=>5,'type'=>39);
        $types[5]['restauracja/pub'] = array('category'=>5,'type'=>40);
        $types[2]['rezydencjalny'] = array('category'=>2,'type'=>16);
        $types[5]['rolny'] = array('category'=>5,'type'=>46);
        $types[5]['rezydencjalny'] = array('category'=>5,'type'=>46);
        $types[5]['parking'] = array('category'=>5,'type'=>46);
        $types[2]['siedlisko/gospodarstwo rolne'] = array('category'=>5,'type'=>20);
        $types[2]['siedliskowy'] = array('category'=>2,'type'=>20);
        $types[5]['stadnina'] = array('category'=>5,'type'=>41);
        $types[2]['szeregowiec'] = array('category'=>2,'type'=>11);
        $types[5]['usługowy'] = array('category'=>5,'type'=>34);
        $types[5]['wolnostojący'] = array('category'=>5,'type'=>34);
        $types[6]['apartament'] = array('category'=>6,'type'=>49);
        $types[6]['miejsce parkingowe'] = array('category'=>6,'type'=>51);
        $types[6]['w garażu podziemnym'] = array('category'=>6,'type'=>49);
        $types[6]['miejsce w garazu podziemnym'] = array('category'=>6,'type'=>49);
        $types[6]['na placu z garażami'] = array('category'=>6,'type'=>47);
        $types[6]['w budynku'] = array('category'=>6,'type'=>48);
        $types[6]['wolnostojący'] = array('category'=>6,'type'=>50);
        $types[3]['grunt'] = array('category'=>3,'type'=>21);
        $types[3]['aktywizacja gospodarcza'] = array('category'=>3,'type'=>25);
        $types[3]['bez warunków zabudowy'] = array('category'=>3,'type'=>23);
        $types[3]['bliźniak'] = array('category'=>3,'type'=>21);
        $types[3]['budowlano rzemieślniczy'] = array('category'=>3,'type'=>25);
        $types[3]['budowlany jednorodzinny'] = array('category'=>3,'type'=>21);
        $types[3]['budowlany pod bliźniak'] = array('category'=>3,'type'=>21);
        $types[3]['budowlany pod szeregowce'] = array('category'=>3,'type'=>21);
        $types[3]['budowlany wielorodzinny'] = array('category'=>3,'type'=>25);
        $types[2]['dworek/posiadłość'] = array('category'=>2,'type'=>17);
        $types[3]['gospodarczy'] = array('category'=>3,'type'=>25);
        $types[5]['gospodarstwo rolne'] = array('category'=>5,'type'=>20);
        $types[3]['leśny'] = array('category'=>3,'type'=>24);
        $types[3]['letniskowy'] = array('category'=>3,'type'=>22);
        $types[3]['przemysłowy'] = array('category'=>3,'type'=>25);
        $types[3]['rekreacyjny'] = array('category'=>3,'type'=>22);
        $types[3]['rekreacyjny/p.o.d.'] = array('category'=>3,'type'=>22);
        $types[3]['aktywizacja_gospodarcza'] = array('category'=>3,'type'=>25);
        $types[3]['budowlano rzemieslnicza'] = array('category'=>3,'type'=>25);
        $types[5]['restauracja/pub'] = array('category'=>5,'type'=>40);
        $types[3]['rezydencjalny'] = array('category'=>3,'type'=>21);
        $types[3]['rolny'] = array('category'=>3,'type'=>23);
        $types[3]['rolny do przekształcenia'] = array('category'=>3,'type'=>23);
        $types[3]['siedlisko'] = array('category'=>3,'type'=>23);
        $types[3]['siedliskowy'] = array('category'=>3,'type'=>23);
        $types[3]['wolno stojący'] = array('category'=>3,'type'=>21);
        $types[3]['wolnostojący'] = array('category'=>3,'type'=>21);
        $types[3]['bez_warunkow_zabudowy'] = array('category'=>3,'type'=>56);
        $types[3]['siedliskowy'] = array('category'=>3,'type'=>57);


        if(strtolower(trim($propertyType))=='mieszkanie' || strtolower(trim($propertyType))=='zwykłe' || strtolower(trim($propertyType))==''){
            $newType = 'mieszkanie';
        }else {
            $newType = strtolower(trim($propertyType));
        }

        if(strtolower(trim($kind))==1
                && strtolower(trim($type))=='mieszkanie'
                && $newType=='mieszkanie'
                && strtolower(trim($location))=='w nowym budynku'){

            return array('category'=>1,'type'=>2);

        }elseif(strtolower(trim($kind))==1
                && strtolower(trim($type))=='mieszkanie'
                && $newType=='mieszkanie'
                && (strtolower(trim($location))=='w budynku mieszkalnym' || strtolower(trim($location))=='budynek mieszkalny')){

            return array('category'=>1,'type'=>55);

        }elseif(strtolower(trim($kind))==1
                && strtolower(trim($type))=='mieszkanie'
                && $newType=='mieszkanie'
                && strtolower(trim($location))=='kamienica'){

            return array('category'=>1,'type'=>3);
        }elseif(strtolower(trim($kind))==1
                && strtolower(trim($type))=='mieszkanie'
                && $newType=='mieszkanie'
                && strtolower(trim($location))=='w willi'){

            return array('category'=>1,'type'=>4);

        }elseif(strtolower(trim($kind))==1
                && strtolower(trim($type))=='mieszkanie'
                && $newType=='mieszkanie'
                && strtolower(trim($location))=='adaptacja strychu'){

            return array('category'=>1,'type'=>5);

        }elseif(strtolower(trim($kind))==1
                && strtolower(trim($type))=='mieszkanie'
                && $newType =='apartament'){

            return array('category'=>1,'type'=>6);

        }elseif(strtolower(trim($kind))==1
                && strtolower(trim($type))=='mieszkanie'
                && $newType=='loft'){

            return array('category'=>1,'type'=>7);
        }

        if(isset($types[strtolower(trim($kind))][strtolower(trim($type))])){
            return $types[strtolower(trim($kind))][strtolower(trim($type))];
        }
        return array('type'=>null,'category'=>null);
    }
    /**
     * Get roof
     *
     * @param string $roof roof
     * @return int
     */
    protected function getRoof($roof) {
        $roof = trim(strtolower($roof));

        $roofType = array(
            'kopertowy'=>1,
            'dwuspadowy'=>2,
            'płaski'=>3,
            'inne'=>4,
          );
        if(isset($roofType[$roof])){
            return $roofType[$roof];
        }
    }
    /**
     * Get roof cover
     *
     * @param string $roofCover
     * @return int
     */
    protected function getRoofCover($roofCover){
        $roofCover = trim(strtolower($roofCover));

        $roofCoverType = array(
            'dachówka'=>1,
            'papa'=>3,
            'blacha'=>2,
            'gonty'=>5,
          );
        if(isset($roofCoverType[$roofCover])){
            return $roofCoverType[$roofCover];
        }
    }
}

