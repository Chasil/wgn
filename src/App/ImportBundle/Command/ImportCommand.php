<?php
namespace App\ImportBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use App\UserBundle\Entity\User;

class ImportCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('import:offers')
            ->setDescription('import offers');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userManager = $this->getContainer()->get('user.manager');

ini_set('max_execution_time', 60*60*1);

        $file = __DIR__ .'/../../../../web/import/oferta.xml';
        //$officeManager = $this->get('office.manager');
        //$userManager = $this->get('user.manager');
        if(!file_exists($file)){
            echo $file;
            echo 'nie ma takiego pliku';
        }
        $xml = simplexml_load_file($file);
        $info = $xml->info;

//        foreach($xml->biura->biuro as $biuro){
//            $importId = $biuro->xbi;
//            $em = $this->getDoctrine()->getManager();
//            $em->getConnection()->beginTransaction();
//
//            try {
//                $office = $officeManager->findByImportid($importId);
//
//                if(!$office){
//                    $office = new Office();
//                }
//
//                $name = ($biuro->nazwapelna) ? $biuro->nazwapelna : '';
//                $street = ($biuro->adres) ? $biuro->adres : '';
//                $province = ($biuro->woj) ? $biuro->woj : '';
//                $zipCode = ($biuro->kod) ? $biuro->kod : '';
//                $city = ($biuro->miasto) ? $biuro->miasto : '';
//                $phone = ($biuro->tel) ? $biuro->tel : '';
//                $mobile = ($biuro->gsm) ? $biuro->gsm : '';
//                $fax = ($biuro->fax) ? $biuro->fax : '';
//                $email = ($biuro->email) ? $biuro->email : '';
//                $www = ($biuro->www) ? $biuro->www : '';
//
//                $office->setName($name)
//                       ->setEmail($email)
//                       ->setFax($fax)
//                       ->setWww($www)
//                       ->setMobile($mobile)
//                       ->setPhone($phone)
//                       ->setImportId($importId)
//                        ;
//                $address = $office->getDefaultAddress();
//
//                if(!$address){
//                    $address = new Address();
//                }
//                $country = $em->getRepository('AppOfferBundle:Country')->findOneByIsoCode('pl');
//                $address->setStreet($street)
//                        ->setZipCode($zipCode)
//                        ->setIsDefault(true)
//                        ->setCity($city)
//                        ->setProvince($province)
//                        ->setName('default')
//                        ->setCountry($country);
//                $office->addAddress($address);
//                $officeManager->save($office);
//
//                $em->getConnection()->commit();
//            }catch(\Exception $e){
//                $em->getConnection()->rollback();
//                throw $e;
//            }
//        }
//
//        foreach($xml->agenci->agent as $agent){
//            $em->getConnection()->beginTransaction();
//
//            try {
//                $update = true;
//                $idAgent = $agent->idagent;
//                $user = $userManager->findByImportId($idAgent);
//
//                if(!$user){
//                    $user = new User();
//                    $update = false;
//                }
//                $importOfficeId = ($agent->numerbiura) ? $agent->numerbiura : '';
//                $firstName= ($agent->imie) ? $agent->imie : '';
//                $lastName = ($agent->nazwisko) ? $agent->nazwisko : '';
//                $licence = ($agent->licencja) ? $agent->licencja : '';
//                $phone = ($agent->telefon) ? $agent->telefon : '';
//                $mobile = ($agent->gsm) ? $agent->gsm : '';
//                $email = ($agent->email) ? $agent->email : '';
//                $avatar = ($agent->zdjecie) ? $agent->zdjecie : '';
//
//                $user->setImportOfficeId($importOfficeId)
//                     ->setImportId($idAgent)
//                     ->setFirstName($firstName)
//                     ->setLastName($lastName)
//                     ->setLicence($licence)
//                     ->setPhone($phone)
//                     ->setMobile($mobile)
//                     ->setEmail($email)
//                     ->setUsername($email.$idAgent)
//                     ->setPlainPassword(uniqid())
//                     ->setRoles(array('ROLE_AGENT'));
//
//                $pathInfo = pathinfo($avatar);
//                $userAvatar = $user->getAvatar();
//
//                if($pathInfo['basename'] != $userAvatar){
//                $user->setAvatar($pathInfo['basename']);
//
//                }
//
//                $office = $officeManager->findByImportid($importOfficeId);
//
//                $user->setOffice($office);
//
//                if($update){
//                    $userManager->update($user);
//                }else {
//                    $userManager->add($user);
//                }
//
//                if($pathInfo['basename'] != $userAvatar && $pathInfo['basename']!=''){
//                    $user->setAvatar($pathInfo['basename']);
//                    $path = __DIR__ . '/../../../../web/uploads/avatar/'.$user->getId();
//                    if(!file_exists($path)){
//                       mkdir($path);
//                    }
//
//                    @copy('http://'.$avatar,$path.'/'.$pathInfo['basename']);
//
//
//                }
//                $em->getConnection()->commit();
//            }catch(\Exception $e){
//                $em->getConnection()->rollback();
//                throw $e;
//            }
//        }


        $em = $this->getContainer()->get('doctrine')->getManager();
        $conn = $em->getConnection();
        $queries = 0;
        $time_start = microtime(true);
        foreach($xml->insert_update->record as $record){


            if($record->akcja =='remove'){
                $offerSQLQuery = 'UPDATE offer SET isDelete = 1 WHERE importId = :id';
                $stmt = $conn->prepare($offerSQLQuery);
                $stmt->bindValue('id',(int)$record->id);
                $stmt->execute();
                $queries++;
            }elseif($record->akcja =='add'){

                $offerSQLQuery = 'SELECT id FROM offer WHERE importId = :id';
                $stmt22 = $conn->prepare($offerSQLQuery);
                $stmt22->bindValue('id',(int)$record->identyfikator);
                $stmt22->execute();
                $offerResult = $stmt22->fetch();
                $queries++;
                $cat = (string)$record->kategoria;

                $idCategory = $this->getCategory($cat);
                $idTransactionType = $this->getTransaction($cat);
                $description = (string)$record->opis;
                $region = (string)$record->woj;
                if($record->powiat !=''){
                    $region .= ' ' .$record->powiat;
                }
                if($record->region !=''){
                    $region .= ' ' .$record->region;
                }
                $city = (string)$record->miasto;
                $street = '';
                if($record->dzielnzw!=''){
                  $street  .= (string)$record->dzielnzw;
                }
                $street .= (string)$record->ulica;
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
                $video = (string)$record->youtube;
                $tytulxml = (string)$record->tytul;
                $name = trim($tytulxml);
                if($name==''){
                    $name .= $this->prepareName(array('idCat'=>$idCategory,'idTransaction'=>$idTransactionType,'squere'=>$squere));
                    //$name .= '. '.$tytulxml.'. ';
                    $name .= '. '.$city.' '.$street.';';
                }
                
                $idAgent = (int)$record->idagent;
                $days = 90;
                $now = new \DateTime();
                $isDirect = ((int)$record->bezposrednia)? 1: 0;
                $isExclusive = ((int)$record->wylacznosc)? 1: 0;
                $importId = (int)$record->identyfikator;
                $expire = clone $now;
                $expire->modify('+ 90 days');

                $isPromo = ((int)$record->wyroznienie)? 1: 0;
                $promoDays = 90;
                $promoExpire = $expire;
                $data = $this->getType((string)$record->kategoriaszcz);
                $locationIndex = $region.' '.$city.' '.$street;
                if($data){
                    $idCategory = $data['category'];
                    $idType = $data['type'];
                }else {
                    $idType = null;
                }

                $params = array('idCategory'=>$idCategory,
                                'idTransaction'=>$idTransactionType,
                                'type_id'=>$idTransactionType,
                                'locationIndex'=>$locationIndex,
                                'description'=>nl2br($description),
                                'signature'=>$cat.$record->numerbiura.'-'.$record->identyfikator,
                                'region'=>$region,
                                'city'=>$city,
                                'street'=>$street,
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
                                'idMarket'=>$idMarket,
                                'video'=>$video,
                                'name'=>$name,
                                'days'=>$days,
                                'expireDate'=>$expire->format('Y-m-d H:i:s'),
                                'isDirect'=>$isDirect,
                                'isExclusive'=>$isExclusive,
                                'idAgent'=>$idAgent,
                                'country_id'=>1,
                                'currency_id'=>1,
                                'isPromo'=>$isPromo,
                                'promoDays'=>$promoDays,
                                'promoExpire'=>$promoExpire->format('Y-m-d H:i:s'));

                if($offerResult){
                    $idOffer = $offerResult['id'];
                    $params['id'] = $idOffer;
                    $params['createDate'] = $now->format('Y-m-d H:i:s');
                    $offerUpdateSQL = 'UPDATE offer SET category_id =:idCategory , transaction_type_id = :idTransaction,'.
                                      'description = :description,region = :region, city = :city, street = :street, '.
                                      'squere = :squere, squerePlot = :squerePlot, price = :price, pricem2 = :pricem2,isPromo = :isPromo, promoDays = :promoDays,promoExpire = :promoExpire,'.
                                      'floor = :floor, rooms = :rooms, storeys = :storeys, lat = :lat, lng = :lng,type_id = :type_id,'.
                                      'yearOfBuilding = :yearOfBuilding, market_id = :idMarket, video = :video,name = :name,locationIndex = :locationIndex,'.
                                      'signature = :signature, days = :days, expireDate = :expireDate, isDirect = :isDirect, isExclusive = :isExclusive,'.
                                      'user_id = (SELECT id FROM user WHERE importId = :idAgent LIMIT 1),createDate =:createDate, country_id = :country_id, currency_id = :currency_id '.
                                      ' WHERE id = :id';
                    $stmt33 = $conn->prepare($offerUpdateSQL);

                    foreach($params as $key=>$val){

                        $stmt33->bindValue($key,$val);
                    }
                    $stmt33->execute();
                    $queries++;
                }else {
                    unset($params['idAgent']);
                    unset($params['idCategory']);
                    unset($params['idTransaction']);
                    unset($params['idMarket']);
                    $params['category_id']=$idCategory;
                    $params['transaction_type_id']= $idTransactionType;
                    $params['market_id']= $idMarket;
                    $params['importId'] = $importId;
                    $params['createDate'] = $now->format('Y-m-d H:i:s');
                    $offerInsertSQL = 'INSERT INTO offer (';

                    $offerInsertSQL .= implode(',',array_keys($params));
                    $offerInsertSQL .= ',user_id,isPublish,isDelete) VALUES(';
                    $offerInsertSQL .= implode(',', array_map(function($v){return ':'.$v;},array_keys($params)));
                    $offerInsertSQL .= ',(SELECT id FROM user WHERE id =:idAgent LIMIT 1),1,0)';

                    $stmt44 = $conn->prepare($offerInsertSQL);
                    foreach($params as $key=>$val){
                        $stmt44->bindValue($key,$val);
                    }
                    $stmt44->bindValue('idAgent',$idAgent);
                    $stmt44->execute();
                    $idOffer = $conn->lastInsertId();
                }

                $path = __DIR__ . '/../../../../web/uploads/offers/'.$idOffer;
                if(!file_exists($path)){
                   mkdir($path);
                }
                $ordering = 1;
                foreach($record->fotos->foto as $foto){
                    $fileName = (string)$foto->plik;
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
                    if($modifyDate !== $foto->data){
                        @copy($record->fotopath.$fileName,$path.'/'.$fileName);
                    }
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
        }

        return $transaction .' ' .$category . ' - ' . $params['squere'] .' m2';
    }
    private function getTransaction($key){

        $wynajem = array('WM','WO','WG','WL','WD');
        $sprzedaz = array('SM','SP','SL','SD','SG','SO');
        $parts = explode(',',$key);

        if(in_array(trim($parts[0]),$sprzedaz)){
            return  1;
        }

        if(in_array($parts[0],$wynajem)){
            return 3;
        }
        return null;
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
            'hotelowy'=>array('type'=>35,'category'=>5),
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
                );

        if(isset($types[$type])){
            return $types[$type];
        }
        return null;
    }
}
?>
