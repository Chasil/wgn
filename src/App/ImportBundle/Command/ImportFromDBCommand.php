<?php
namespace App\ImportBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use App\UserBundle\Entity\User;
use App\UserBundle\Entity\Address;
use App\UserBundle\Entity\CompanyData;
class ImportFromDBCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('import:db:offers')
            ->setDescription('import offers from db');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userManager = $this->getContainer()->get('user.manager');

        ini_set('max_execution_time', 60*60*2);


        //$officeManager = $this->get('office.manager');
        //$userManager = $this->get('user.manager');
//        if(!file_exists($file)){
//            echo $file;
//            echo 'nie ma takiego pliku';
//        }


        $em = $this->getContainer()->get('doctrine')->getManager();
        $conn = $em->getConnection();
        $queries = 0;
        $em2 = $this->getContainer()->get('doctrine')->getManager('test');
        $conn2 = $em2->getConnection();
        //$offersSQLQuery = 'SELECT * FROM wgn_oferty where sygnatura = :syg';
        $offersSQLQuery = 'SELECT * FROM wgn_oferty';
                $stmt_1 = $conn2->prepare($offersSQLQuery);
                //$stmt_1->bindValue('syg','KM027-11330');
                $stmt_1->execute();
                $i = 0;
        while ($row = $stmt_1->fetch()) {

                $i++;
                $offerSQLQuery = 'SELECT id FROM offer WHERE legacyId = :legacyId';
                $stmt22 = $conn->prepare($offerSQLQuery);
                $stmt22->bindValue('legacyId',$row['sygnatura']);
                $stmt22->execute();
                $offerResult = $stmt22->fetch();
                $queries++;


                $offerHitsSQLQuery = "select odslon as ile from wgn_oferty_views where sygnatura=:sygnatura";
                $stmtHits = $conn2->prepare($offerHitsSQLQuery);
                $stmtHits->bindValue('sygnatura',$row['sygnatura']);
                $stmtHits->execute();
                $offerHits = $stmtHits->fetch();

                $idTransactionType = $this->getTransaction((string)$row['rodzaj_tranzakcji']);
                $description = (string)$row['opis'];
                $region = (string)$row['lok_wojewodztwo'];
                if($row['lok_powiat'] !=''){
                    $region .= ' ' . $row['lok_powiat'];
                }
//                if($record->region !=''){
//                    $region .= ' ' .$record->region;
//                }
                $city = (string)$row['lok_miasto'];
                $street = '';
                if($row['lok_dzielnica']!=''){
                  $street  .= (string)$row['lok_dzielnica'];
                }
                if($row['lok_osiedle']!=''){
                  $street  .= (string)$row['lok_osiedle'];
                }

                $squere = (float)$row['pow_calkowita'];
                if(!$squere){
                    $squere = (float)$row['pow_dzialki'];
                }
                $squerePlot = (float)$row['pow_dzialki'];
                $price = (float)$row['cena'];

                if($price>0 && $squere > 0){
                    $pricem2 = $price / $squere;
                }else {
                    $pricem2 = 0;
                }
                $floor = (int)$row['pietro'];
                $rooms=(int)$row['liczba_pokoi'];
                $storeys=(int)$row['liczba_kondygnacji'];
                $lat = (string)$row['wspbn'];
                $lng = (string)$row['wspln'];
                $yearOfBuilding = (string)$row['rok_budowy'];
                $idMarket = ($row['rynek']=='Rynek pierwotny')? 1 : 2;
                $video= (string)$row['youtube'];


                $agent = $row['agent'];
                $idUser = null;
                if($agent !=''){
                    $aParts = explode('_',$agent);

                    $agentSQLQuery = 'SELECT id FROM user WHERE importId = :id LIMIT 1';
                    $stmt22 = $conn->prepare($agentSQLQuery);
                    $stmt22->bindValue('id',(int)$aParts[1]);
                    $stmt22->execute();
                    $userResult = $stmt22->fetch();
                    $idUser = $userResult['id'];
                }
                $days = 90;

                $isDirect = ((int)$row['bezposrednia'])? 1: 0;
                $isExclusive = ((int)$row['wylacznosc'])? 1: 0;

                if($row['data_przyjecia']!=''){
                    $createDate = \DateTime::createFromFormat('Y-m-d H:i:s', $row['data_przyjecia']);
                }
                if($row['ostatnia_modyfikacja']!=''){
                    $createDate = \DateTime::createFromFormat('Y-m-d H:i:s', $row['ostatnia_modyfikacja']);
                    $modifyDate = \DateTime::createFromFormat('Y-m-d H:i:s', $row['ostatnia_modyfikacja']);
                }else {
                    $createDate = new \DateTime();
                    $modifyDate = new \DateTime();
                }
                $isPromo = ((int)$row['specjalna'])? 1: 0;
                $promoDays = 90;
                $expire = clone $modifyDate;
                $expire->modify('+ 90 days');
                $promoExpire = $expire;
                $data = $this->getType((string)$row['typ_obiektu']);
                $locationIndex = $region.' '.$city.' '.$street;
                if($data){
                    $idCategory = $data['category'];
                    $idType = $data['type'];
                }else {
                    $idType = null;
                }


                if (substr($row['oferta_agencja'], 0, 3) == "SMS") {

                    $userSQLQuery = "SELECT uid AS sygnatura, nazwa, miasto AS poczta, kod, ulica, miejscowosc, kraj, email AS email1, concat( telkier, ' ', telnumer ) AS tel1_praca, telkomorka AS tel1_komorkowy FROM `wgn_smsklient` where  uid=:sygnatura";
                    $stmtUser = $conn2->prepare($userSQLQuery);
                    $stmtUser->bindValue('sygnatura',$row['oferta_agencja']);
                    $stmtUser->execute();
                    $user = $stmtUser->fetch();

                }else if (substr($row['oferta_agencja'], 0, 3) == "ABO") {
                    $userBuissnes = $userManager->findBySignature($row['oferta_agencja']);
                    if(is_object($userBuissnes)){
                        $idUser = $userBuissnes->getId();

                    }else {
                        $output->writeln('buisness user not found '.$row['oferta_agencja']);
                    }
                }
                if($row['oferta']>-1){
                    $isPublish = 1;
                }else {
                    $isPublish = 0;
                }
                $output->writeln('is publish '.$isPublish);
                $name = $this->prepareName(array('idCat'=>$idCategory,'idTransaction'=>$idTransactionType,'squere'=>$squere));
                $name .= ' '.$city;

                $output->writeln($i);
                $params = array('idCategory'=>$idCategory, #
                                'idTransaction'=>$idTransactionType, #
                                'type_id'=>$idTransactionType, #
                                'locationIndex'=>$locationIndex,#
                                'description'=>nl2br($description), #
                                'signature'=>$row['sygnatura'],#
                                'legacyId'=>$row['sygnatura'],#
                                'region'=>$region,#
                                'city'=>$city,#
                                'street'=>$street,#
                                'squere'=>$squere,#
                                'squerePlot'=>$squerePlot,#
                                'price'=>$price,#
                                'pricem2'=>$pricem2,#
                                'floor'=>$floor,#
                                'rooms'=>$rooms,#
                                'storeys'=>$storeys,#
                                'lat'=>$lat,#
                                'lng'=>$lng,#
                                'yearOfBuilding'=>$yearOfBuilding,#
                                'idMarket'=>$idMarket,#
                                'video'=>$video,#
                                'name'=>$name,#
                                'days'=>$days,#
                                'expireDate'=>$expire->format('Y-m-d H:i:s'),#
                                'isDirect'=>$isDirect,#
                                'isExclusive'=>$isExclusive,#
                                'user_id'=>$idUser,#
                                'country_id'=>1,#
                                'currency_id'=>1,#
                                'isPromo'=>$isPromo,#
                                'isPublish'=> $isPublish,
                                'promoDays'=>$promoDays,#
                                'hits'=>(int)$offerHits['ile'],#
                                'legal_status_id'=>$this->legalStatus($row['stan_prawny']),#
                                'technical_condition_id'=>  $this->technicalCondition($row['stan_tech']),#
                                'roof_cover_id' => $this->roofCover($row['pokrycie_dachu']),#
                                'access_road_id' => $this->accessRoad($row['dojazd']),#
                                'abilityToWath' =>$row['mozliwosc_ogladania'],#
                                'exhibition_windows_id' =>$this->exhibitionWindows($row['wystawa_okien']),
                                'promoExpire'=>$promoExpire->format('Y-m-d H:i:s'));
                $params['createDate'] = $createDate->format('Y-m-d H:i:s');
                $params['modifyDate'] = $modifyDate->format('Y-m-d H:i:s');

                if(isset($user)){

                    $params['email'] = ($user['email1']!=='') ? $user['email1'] : 'wgn@wgn.pl';
                    $params['phone'] = ($user['tel1_komorkowy']!=='') ? $user['tel1_komorkowy'] : '';
                    $params['contactPerson'] =  ($user['nazwa']!=='') ? $user['nazwa'] : '';
                }else{
                    $params['email'] = '';
                    $params['phone'] = '';
                    $params['contactPerson'] = '';
                }

                if($offerResult){
                    $idOffer = $offerResult['id'];
                    $params['id'] = $idOffer;

                    $offerUpdateSQL = 'UPDATE offer SET '
                            . ' category_id =:idCategory , '
                            . 'transaction_type_id = :idTransaction, '
                            . 'hits = :hits, '
                            . 'name = :name, '
                            . 'modifyDate = :modifyDate, '
                            . 'phone = :phone, '
                            . 'email = :email, '
                            . 'description = :description,'
                            . 'region = :region, '
                            . 'city = :city, '
                            . 'street = :street,'
                            . 'legacyId = :legacyId, '
                            . 'legal_status_id = :legal_status_id, '
                            . 'technical_condition_id = :technical_condition_id, '
                            . 'squere = :squere, '
                            . 'squerePlot = :squerePlot, '
                            . 'price = :price, '
                            . 'pricem2 = :pricem2,'
                            . 'isPromo = :isPromo, '
                            . 'isPublish = :isPublish, '
                            . 'promoDays = :promoDays,'
                            . 'promoExpire = :promoExpire,'
                            . 'floor = :floor, '
                            . 'rooms = :rooms, '
                            . 'storeys = :storeys, '
                            . 'lat = :lat, '
                            . 'lng = :lng,'
                            . 'type_id = :type_id, '
                            . 'roof_cover_id = :roof_cover_id, '
                            . 'access_road_id = :access_road_id, '
                            . 'yearOfBuilding = :yearOfBuilding, '
                            . 'market_id = :idMarket, '
                            . 'video = :video,'
                            . 'locationIndex = :locationIndex,'
                            . 'abilityToWath = :abilityToWath, '
                            .'signature = :signature, '
                            . 'days = :days, '
                            . 'expireDate = :expireDate, '
                            . 'isDirect = :isDirect, '
                            . 'isExclusive = :isExclusive,'
                            . 'exhibition_windows_id = :exhibition_windows_id, '
                            . 'user_id = :user_id,'
                            . 'createDate =:createDate, '
                            . 'modifyDate =:modifyDate, '
                            . 'country_id = :country_id, '
                            . 'currency_id = :currency_id '
                            . ' WHERE id = :id';
                    $stmt33 = $conn->prepare($offerUpdateSQL);

                    foreach($params as $key=>$val){

                        $stmt33->bindValue($key,$val);
                    }
                    $stmt33->execute();
                    $output->writeln('update');
                }else {
                    unset($params['idCategory']);
                    unset($params['idTransaction']);
                    unset($params['idMarket']);
                    $params['category_id']=$idCategory;
                    $params['transaction_type_id']= $idTransactionType;
                    $params['market_id']= $idMarket;
                    $params['importId'] = null;

                    $offerInsertSQL = 'INSERT INTO offer (';

                    $offerInsertSQL .= implode(',',array_keys($params));
                    $offerInsertSQL .= ',isDelete) VALUES(';
                    $offerInsertSQL .= implode(',', array_map(function($v){return ':'.$v;},array_keys($params)));
                    $offerInsertSQL .= ',0)';

                    $stmt44 = $conn->prepare($offerInsertSQL);
                    foreach($params as $key=>$val){
                        $stmt44->bindValue($key,$val);
                    }
                    $stmt44->execute();
                    $idOffer = $conn->lastInsertId();
                }

                if($this->ogrzewanie($row['ogrzewanie'])){
                    $offerCoSQLQuery = 'SELECT offer_id FROM offer_media WHERE offer_id = :idOffer AND media_id = :idMedia';
                    $stmt22 = $conn->prepare($offerCoSQLQuery);
                    $stmt22->bindValue('idOffer',$idOffer);
                    $stmt22->bindValue('idMedia',9);
                    $stmt22->execute();
                    $offerResult2 = $stmt22->fetch();

                    if(!$offerResult2){
                        $insertCoSQL = "INSERT INTO offer_media (offer_id,media_id) VALUES(:idOffer,:idMedia)";
                        $stmt3 = $conn->prepare($insertCoSQL);
                        $stmt3->bindValue('idOffer',$idOffer);
                        $stmt3->bindValue('idMedia',9);
                        $stmt3->execute();
                    }
                }

                $media = $this->media($row['media']);

                foreach ($media as $m){
                    $offerMediaSQLQuery = 'SELECT offer_id FROM offer_media WHERE offer_id = :idOffer AND media_id = :idMedia';
                    $stmt22 = $conn->prepare($offerMediaSQLQuery);
                    $stmt22->bindValue('idOffer',$idOffer);
                    $stmt22->bindValue('idMedia',$m);
                    $stmt22->execute();
                    $offerResult3 = $stmt22->fetch();

                    if(!$offerResult3){
                        $insertCoSQL = "INSERT INTO offer_media (offer_id,media_id) VALUES(:idOffer,:idMedia)";
                        $stmt3 = $conn->prepare($insertCoSQL);
                        $stmt3->bindValue('idOffer',$idOffer);
                        $stmt3->bindValue('idMedia',$m);
                        $stmt3->execute();
                    }
                }

                $path = __DIR__ . '/../../../../web/uploads/offers/'.$idOffer;
                if(!file_exists($path)){
                   mkdir($path);
                }
                $imagesSQLQuery = 'SELECT * FROM wgn_zdjecia WHERE sygnatura = :sygnatura';
                $stmt_222 = $conn2->prepare($imagesSQLQuery);
                $stmt_222->bindValue('sygnatura',$row['sygnatura']);
                $stmt_222->execute();

                $ordering = 1;
                while ($row = $stmt_222->fetch()) {
                    $fileName = (string)$row['plik'];
                    $imageSQLQuery = 'SELECT id, modifyDate FROM offer_image WHERE name = :name AND offer_id = :idOffer';
                    $stmt2 = $conn->prepare($imageSQLQuery);
                    $stmt2->bindValue('name',$fileName);
                    $stmt2->bindValue('idOffer',$idOffer);

                    $stmt2->execute();
                    $queries++;
                    $imageResult = $stmt2->fetch();
//
                    if(!$imageResult){
                        $insertImageSQL = "INSERT INTO offer_image (name,ordering,modifyDate,offer_id) VALUES(:name,:ordering,NOW(),:idOffer)";
                        $stmt3 = $conn->prepare($insertImageSQL);
                        $stmt3->bindValue('name',$fileName);
                        $stmt3->bindValue('ordering',(int)$ordering);
                        $stmt3->bindValue('idOffer',(int)$idOffer);
                        $stmt3->execute();
                        //$conn->lastInsertId();
                        $modifyDate = date('Y-m-d');

                    }else {
                        $modifyDate = $imageResult['modifyDate'];
                        $updateImageSQL = "UPDATE offer_image SET modifyDate = NOW() ,ordering = :ordering, name = :name WHERE id = :id";
                        $stmt4 = $conn->prepare($updateImageSQL);
                        $stmt4->bindValue('name',$fileName);
                        $stmt4->bindValue('ordering',(int)$ordering);
                        $stmt4->bindValue('id',(int)$imageResult['id']);
                        $stmt4->execute();
                    }


                    if($ordering ==1){
                        $updateOfferSQL = "UPDATE offer SET mainPhoto = :mainPhoto WHERE id = :id";
                        $stmt5 = $conn->prepare($updateOfferSQL);
                        $stmt5->bindValue('mainPhoto',$fileName);
                        $stmt5->bindValue('id',$idOffer);
                        $stmt5->execute();
                    }
                    $ordering++;
                    if(!file_exists($path.'/'.$fileName)){
                        @copy('https://wgn.pl/pictures/'.$fileName,$path.'/'.$fileName);
                    }
                }

    }


        $output->writeln('user has been added');
    }
    private function prepareName($params){
        $transaction = ($params['idTransaction']==1)? 'Sprzedam' : 'Wynajmę';

        switch($params['idCat']){
            case 1:
                $category = 'mieszkanie';
            break;
            case 2:
                $category = 'dom';
            break;
            case 3:
                $category = 'działkę';
            break;
            case 4:
                $category = 'lokal';
            break;
            case 5:
                $category = 'objekt';
            break;
            case 6:
                $category = 'garaż';
            break;
            default:
                $category = '';
                break;
        }
        $squere = '';
        if($params['squere']>0){
            $squere = ' - ' . $params['squere'] .' m2';
        }
        return $transaction .' ' .$category . $squere;
    }
    private function getTransaction($name){

        $id = null;

        switch($name){
            case 'sprzedaż':
                $id = 1;
            break;
            case 'kupno':
                $id = 2;
            break;
            case 'wynajem':
            case 'wynajm':
                $id = 3;
            break;
            case 'najem':
                $id = 4;
            break;
        }

        return $id;
    }
    private function getCategory($key){
        $parts = explode(',',$key);

        $shortKey = substr(trim($parts[0]), 1, 1);

        $cats = array('M'=> 1,
                      'D'=> 2,
                      'L'=> 4,
                      'P'=> 3,
                      'G'=> 6,
                      'O'=> 5);

        if($cats[$shortKey]){
            return $cats[$shortKey];
        }
        return null;
    }
    private function getType($type){
        $types = array('mieszkanie'=>array('type'=>1,'category'=>1),
              'szeregowiec'=>array('type'=>11,'category'=>2),
            'willa/rezydencja'=>array('type'=>15,'category'=>2),
            'dom z warsztatem'=>array('type'=>12,'category'=>2),
            'bliźniak'=>array('type'=>10,'category'=>2),
            'dworek/posiadłość'=>array('type'=>17,'category'=>2),
            'wolnostojący'=>array('type'=>9,'category'=>2),
            'wolno stojący'=>array('type'=>9,'category'=>2),
            'usługowy'=>array('type'=>27,'category'=>4),
            'handlowy'=>array('type'=>29,'category'=>4),
            'biurowy'=>array('type'=>26,'category'=>4),
            'gastronomia'=>array('type'=>28,'category'=>4),
            'hotelowy'=>array('type'=>35,'category'=>5),
            'przemysłowy'=>array('type'=>44,'category'=>5),
            'kamienica'=>array('type'=>31,'category'=>5),
            'warsztatowy'=>array('type'=>45,'category'=>5),
            'budynek wielorodzinny/plomba'=>array('type'=>32,'category'=>5),
            'inny komercyjny'=>array('type'=>46,'category'=>5),
            'handlowy'=>array('type'=>33,'category'=>5),
            'rekreacyjny'=>array('type'=>39,'category'=>5),
            'rezydencjalny'=>array('type'=>16,'category'=>2),
            'restauracja/pub'=>array('type'=>40,'category'=>5),
            'obiekt, budynek Hotelowy'=>array('type'=>35,'category'=>5),
            'biurowy'=>array('type'=>30,'category'=>5),
            'gospodarstwo rolne'=>array('type'=>13,'category'=>2),
            'budowlany jednorodzinny'=>array('type'=>21,'category'=>3),
            'budowlany wielorodzinny'=>array('type'=>21,'category'=>3),
            'budowlany pod szeregowce'=>array('type'=>21,'category'=>3),
            'rolny'=>array('type'=>23,'category'=>3),
            'aktywizacja gospodarcza'=>array('type'=>25,'category'=>3),
            'rekreacyjny/p.o.d.'=>array('type'=>22,'category'=>3),
            'siedliskowy'=>array('type'=>22,'category'=>3),
            'leśny'=>array('type'=>24,'category'=>3),
            'rolny do przekształcenia'=>array('type'=>23,'category'=>3),
            'budowlano rzemieślniczy'=>array('type'=>21,'category'=>3),
            'na placu z garażami'=>array('type'=>47,'category'=>6),
            'miejsce w garażu podziemnym'=>array('type'=>49,'category'=>6),
            ###
            'budynek'=>array('type'=>34,'category'=>5),
            'budynek apartament'=>array('type'=>6,'category'=>1),
            'budynek bliźniak'=>array('type'=>10,'category'=>2),
            'budynek budowlany jednorodzinny'=>array('type'=>9,'category'=>2),
            'budynek dom z warsztatem'=>array('type'=>12,'category'=>2),
            'budynek Dworek/pałac'=>array('type'=>18,'category'=>2),
            'budynek dworek/posiadłość'=>array('type'=>16,'category'=>2),
            'budynek gospodarczy'=>array('type'=>13,'category'=>2),
            'budynek gospodarstwo rolne'=>array('type'=>20,'category'=>2),
            'budynek inny komercyjny'=>array('type'=>46,'category'=>5),
            'budynek Kamienica'=>array('type'=>31,'category'=>5),
            'budynek Letniskowy'=>array('type'=>14,'category'=>2),
            'budynek pałac/zamek'=>array('type'=>18,'category'=>2),
            'budynek pensjonat'=>array('type'=>38,'category'=>5),
            'budynek rekreacyjny/p.o.d.'=>array('type'=>22,'category'=>3),
            'budynek rolny'=>array('type'=>23,'category'=>3),
            'budynek siedliskowy'=>array('type'=>34,'category'=>5),
            'budynek szeregowiec'=>array('type'=>32,'category'=>5),
            'budynek usługowy'=>array('type'=>27,'category'=>4),
            'budynek w bloku'=>array('type'=>1,'category'=>1),
            'budynek willa/rezydencja'=>array('type'=>15,'category'=>2),
            'budynek Wolno stojący'=>array('type'=>9,'category'=>2),
            'budynek wolnostojący'=>array('type'=>9,'category'=>2),
            'lokal'=>array('type'=>27,'category'=>4),
            'lokal apartament'=>array('type'=>6,'category'=>1),
            'lokal biurowy'=>array('type'=>26,'category'=>4),
            'lokal bliźniak'=>array('type'=>10,'category'=>2),
            'lokal dom z warsztatem'=>array('type'=>12,'category'=>2),
            'lokal dworek/posiadłość'=>array('type'=>17,'category'=>2),
            'lokal gastronomia'=>array('type'=>28,'category'=>4),
            'lokal handlowy'=>array('type'=>29,'category'=>4),
            'lokal hotelowy'=>array('type'=>35,'category'=>5),
            'lokal inny komercyjny'=>array('type'=>46,'category'=>5),
            'lokal kamienica'=>array('type'=>31,'category'=>5),
            'lokal magazynowy'=>array('type'=>42,'category'=>5),
            'lokal mieszkanie'=>array('type'=>1,'category'=>1),
            'lokal pałac/zamek'=>array('type'=>18,'category'=>2),
            'lokal pensjonat'=>array('type'=>38,'category'=>5),
            'lokal przemysłowy'=>array('type'=>44,'category'=>5),
            'lokal rezydencjalny'=>array('type'=>16,'category'=>2),
            'lokal usługowy'=>array('type'=>27,'category'=>4),
            'lokal w bloku'=>array('type'=>27,'category'=>4),
            'lokal w kamienicy'=>array('type'=>27,'category'=>4),
            'lokal warsztatowy'=>array('type'=>45,'category'=>5),
            'lokal willowe'=>array('type'=>15,'category'=>2),
            'lokal wolnostojący'=>array('type'=>9,'category'=>2),
            'mieszkanie'=>array('type'=>1,'category'=>1),
            'mieszkanie adaptacja strychu'=>array('type'=>5,'category'=>1),
            'mieszkanie apartament'=>array('type'=>6,'category'=>1),
            'mieszkanie inny komercyjny'=>array('type'=>46,'category'=>5),
            'mieszkanie mieszkanie'=>array('type'=>1,'category'=>1),
            'mieszkanie pokój'=>array('type'=>8,'category'=>1),
            'mieszkanie usługowy'=>array('type'=>27,'category'=>4),
            'mieszkanie w bloku'=>array('type'=>1,'category'=>1),
            'mieszkanie w kamienicy'=>array('type'=>3,'category'=>1),
            'mieszkanie w nowym budynku'=>array('type'=>2,'category'=>1),
            'mieszkanie willowe'=>array('type'=>15,'category'=>2),
            'obiekt, budynek'=>array('type'=>33,'category'=>5),
            'obiekt, budynek aktywizacja gospodarcza'=>array('type'=>25,'category'=>3),
            'obiekt, budynek biurowy'=>array('type'=>34,'category'=>5),
            'obiekt, budynek budowlany jednorodzinny'=>array('type'=>9,'category'=>2),
            'obiekt, budynek Budowlany wielorodzinny'=>array('type'=>32,'category'=>5),
            'obiekt, budynek budynek wielorodzinny/plomba'=>array('type'=>32,'category'=>5),
            'obiekt, budynek dworek/posiadłość'=>array('type'=>17,'category'=>2),
            'obiekt, budynek gospodarstwo rolne'=>array('type'=>20,'category'=>2),
            'obiekt, budynek handlowy'=>array('type'=>33,'category'=>5),
            'obiekt, budynek Hotelowy'=>array('type'=>35,'category'=>5),
            'obiekt, budynek inny komercyjny'=>array('type'=>46,'category'=>5),
            'obiekt, budynek inny niekomercyjny'=>array('type'=>46,'category'=>5),
            'obiekt, budynek kamienica'=>array('type'=>31,'category'=>5),
            'obiekt, budynek magazynowy'=>array('type'=>42,'category'=>5),
            'obiekt, budynek pałac/zamek'=>array('type'=>19,'category'=>2),
            'obiekt, budynek pensjonat'=>array('type'=>38,'category'=>5),
            'obiekt, budynek produkcja'=>array('type'=>43,'category'=>5),
            'obiekt, budynek Przemysłowy'=>array('type'=>44,'category'=>5),
            'obiekt, budynek rekreacyjny'=>array('type'=>22,'category'=>3),
            'obiekt, budynek restauracja/pub'=>array('type'=>40,'category'=>5),
            'obiekt, budynek rezydencjalny'=>array('type'=>16,'category'=>2),
            'obiekt, budynek rolny'=>array('type'=>23,'category'=>3),
            'obiekt, budynek Siedlisko/gospodarstwo rolne'=>array('type'=>20,'category'=>2),
            'obiekt, budynek siedliskowy'=>array('type'=>20,'category'=>2),
            'obiekt, budynek stadnina'=>array('type'=>22,'category'=>3),
            'obiekt, budynek szeregowiec'=>array('type'=>11,'category'=>2),
            'obiekt, budynek usługowy'=>array('type'=>34,'category'=>5),
            'obiekt, budynek Warsztatowy'=>array('type'=>34,'category'=>5),
            'obiekt, budynek willa/rezydencja'=>array('type'=>15,'category'=>2),
            'obiekt, budynek wolnostojący'=>array('type'=>9,'category'=>2),
            'teren, garaż apartament'=>array('type'=>6,'category'=>1),
            'teren, garaż miejsce parkingowe'=>array('type'=>51,'category'=>6),
            'teren, garaż miejsce w garażu podziemnym'=>array('type'=>49,'category'=>6),
            'teren, garaż na placu z garażami'=>array('type'=>47,'category'=>6),
            'teren, garaż w bloku'=>array('type'=>48,'category'=>6),
            'teren, garaż w budynku'=>array('type'=>48,'category'=>6),
            'teren, garaż warsztatowy'=>array('type'=>48,'category'=>6),
            'teren, garaż wolnostojący'=>array('type'=>50,'category'=>6),
            'teren, grunt'=>array('type'=>25,'category'=>3),
            'teren, grunt aktywizacja gospodarcza'=>array('type'=>25,'category'=>3),
            'teren, grunt bez warunków zabudowy'=>array('type'=>25,'category'=>3),
            'teren, grunt bliźniak'=>array('type'=>10,'category'=>2),
            'teren, grunt budowlano rzemieślniczy'=>array('type'=>25,'category'=>3),
            'teren, grunt budowlany jednorodzinny'=>array('type'=>25,'category'=>3),
            'teren, grunt budowlany pod bliźniak'=>array('type'=>25,'category'=>3),
            'teren, grunt budowlany pod szeregowce'=>array('type'=>25,'category'=>3),
            'teren, grunt budowlany wielorodzinny'=>array('type'=>25,'category'=>3),
            'teren, grunt dworek/posiadłość'=>array('type'=>17,'category'=>2),
            'teren, grunt gospodarczy'=>array('type'=>13,'category'=>2),
            'teren, grunt gospodarstwo rolne'=>array('type'=>20,'category'=>2),
            'teren, grunt leśny'=>array('type'=>24,'category'=>3),
            'teren, grunt letniskowy'=>array('type'=>14,'category'=>2),
            'teren, grunt przemysłowy'=>array('type'=>44,'category'=>5),
            'teren, grunt rekreacyjny'=>array('type'=>22,'category'=>3),
            'teren, grunt Rekreacyjny/p.o.d.'=>array('type'=>22,'category'=>3),
            'teren, grunt restauracja/pub'=>array('type'=>40,'category'=>5),
            'teren, grunt rezydencjalny'=>array('type'=>16,'category'=>2),
            'teren, grunt rolny'=>array('type'=>23,'category'=>3),
            'teren, grunt rolny do przekształcenia'=>array('type'=>23,'category'=>3),
            'teren, grunt Siedlisko'=>array('type'=>23,'category'=>3),
            'teren, grunt siedliskowy'=>array('type'=>23,'category'=>3),
            'teren, grunt Wolno stojący'=>array('type'=>9,'category'=>2),
            'teren, grunt wolnostojący'=>array('type'=>9,'category'=>2),
            'budynek'=>array('type'=>34,'category'=>5),
            'budynek'=>array('type'=>34,'category'=>5),
                );

        if(isset($types[trim($type)])){
            return $types[trim($type)];
        }
        return array('type'=>null,'category'=>null);
    }
    private function legalStatus($status){
        $s = array('dzierżawa'=>2,
                   'P.O.D.'=>3,
            'spółdzielcze własn. bez KW'=>3,
            'spółdzielcze własn. z KW'=>3,
            'spółdzielcze, własne z KW'=>3,
            'udział we własności'=>3,
            'uregulowany'=>3,
            'użytkowanie wieczyste'=>3,
            'wlasnosc prywatna'=>1,
            'własnosc prywatna'=>1,
            'własnosć'=>1,
            'własnościowe'=>1,
            'własność'=>1,
            'właściciel'=>1,
            'Właściciel - Skarb Państwa AMW'=>1,
            'wolny'=>3
            );

        if(isset($s[$status])){
            return $s[$status];
        }

        return null;
    }
    private function technicalCondition($condition){
        $c = array( 'bardzo dobry'=>1,
                    'deweloperski'=>3,
                    'do dokończenia'=>2,
                    'do odświeżenia'=>3,
                    'do remontu'=>3,
                    'do wykończenia'=>2,
                    'dobry'=>1,
                    'idealny'=>1,
                    'nienaganny'=>1,
                    'po generalnym remoncie/nowy'=>1,
                    'po remoncie'=>1,
                    'standard'=>1,
                    'surowy zamkniety'=>1,
                    'surowy zamknięty'=>1,
                    'w budowie / deweloperski'=>1,
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
    private function ogrzewanie($value){

        $c = array('C.O.',
            'centralne',
            'centralne Dalkia',
            'centralne gazowe',
            'centralne miejskie',
            'CO',
            'elektryczn',
            'elektryczne',
            'gazowe',
            'gazowe -kocioł dwufunkcyjny',
            'grzejniki na podczerwień (IR)',
            'inne',
            'jest',
            'kominek',
            'kominkowe+CO',
            'miejskie',
            'olejowe',
            'piec',
            'piec centralny',
            'piec gazowy',
            'piece',
            'tak');

        if(isset($c[$value])){
            true;
        }

        return false;
    }
    private function roofCover($value){

        $c = array('blacha'=>2,
            'blacha dachowkowa'=>2,
            'blacha dachówkowa'=>2,
            'blacha/dachówka/papa'=>2,
            'blacha/papa'=>3,
            'blacha5'=>2,
            'dachówka brass'=>1,
            'papa'=>3,
            'papa/blacha'=>3,
            );

        if(isset($c[$value])){
            return $c[$value];
        }

        return null;
    }
    private function accessRoad($value){

        $c = array('asfalt'=>1,
            'asfaltowy'=>1,
            'bardzo dobry'=>4,
            'dobry'=>4,
            'droga asfaltowa'=>1,
            'droga utwardzona'=>2,
            'kostka brukowa'=>4,
            'polbruk'=>3,
            'polny'=>3,
            'polny/leśny'=>3,
            'utwardzony'=>2,
            );


        if(isset($c[$value])){
            return $c[$value];
        }

        return null;
    }
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
    private function media($value){
        $value = mb_strtolower($value,'UTF-8');
        $media = array();
        if(preg_match('/prąd/',$value)||preg_match('/e,/',$value)){
            $media[] = 2;
        }

        if(preg_match('/gaz/',$value)){
            $media[] = 4;
        }
        if(preg_match('/woda/',$value)|| preg_match('/wodociąg miejski/',$value)
                || preg_match('/wodociąg gminny/',$value)|| preg_match('/woda miejska/',$value)
                || preg_match('/w,/',$value)){
            $media[] = 1;
        }
        if(preg_match('/internet/',$value)){
            $media[] = 8;
        }
        if(preg_match('/telefon/',$value)){
            $media[] = 7;
        }
        if(preg_match('/kanalizacja/',$value)){
            $media[] = 5;
        }
        if(preg_match('/szambo/',$value)){
            $media[] = 6;
        }

        return $media;
    }
}
?>
