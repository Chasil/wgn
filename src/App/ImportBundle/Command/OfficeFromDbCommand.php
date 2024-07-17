<?php
namespace App\ImportBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use App\OfficeBundle\Entity\Office;
use App\OfferBundle\Entity\Country;
use App\UserBundle\Entity\Address;
class OfficeFromDbCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('import:office:fromdb')
            ->setDescription('import offices');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager('test');
        $conn = $em->getConnection();
        $officesSQLQuery = 'select * from wgn_agencje';
        $stmt_1 = $conn->prepare($officesSQLQuery);
        $stmt_1->execute();
        $i= 0;
        while ($row = $stmt_1->fetch()) {
            $output->writeln('office'.$row['id'].' '.$row['nazwa']);
            $this->insertOffice($row,$output);
             $i++;
        }

    }

    protected function insertOffice($row,$output){
        $repo = $this->getContainer()->get('doctrine')->getRepository('AppOfficeBundle:Office');
        $em = $this->getContainer()->get('doctrine')->getManager();
        $office = $repo->findOneByLegacyId($row['id']);

        if(is_object($office)){
            $this->insertPhotos($office, $output);
            return;
        }

        $office = new Office();
        $name = strip_tags(trim($row['nazwa']));
        $parts = explode(',',$row['adres']);
        $adres = preg_replace("/\(([^()]*+|(?R))*\)/", '', $parts[0]);
        $adres = rtrim($adres,'.');
        $office->setName($name);
        $office->setSignature($row['sygnatura']);
        $office->setImportId($row['numerbiura']);
        $office->setPhone($row['kontakt']);
        $office->setLegacyId($row['id']);
        $office->setCreditOfferUrl($row['www2']);
        $office->setEmail($row['email']);
        $office->setWww($row['www']);

        if($row['status']==3 && $name !=''){
            $office->setIsPublish(true);
        }else {
            $office->setIsPublish(false);
        }

        $address = $office->getDefaultAddress();

        if(!$address){
            $address = new Address();
        }
        $country = $em->getRepository('AppOfferBundle:Country')->findOneByName($row['kraj']);
        if(!is_object($country)){
            $country = new Country();
            $country->setName($row['kraj']);
            $country->setIsoCode(substr(strtolower($row['kraj']), 0, 2));
        }
        $address->setStreet($adres)
                ->setZipCode($row['kod'])
                ->setIsDefault(true)
                ->setCity($row['miasto'])
                ->setProvince($row['region'])
                ->setName('default')
                ->setCountry($country);
        $office->addAddress($address);


        $em->persist($office);
        $em->flush();
        $this->insertPhotos($office, $output);

    }

    protected function insertPhotos($office,$output){
        $em = $this->getContainer()->get('doctrine')->getManager('test');
        $conn = $em->getConnection();
        $imagesSQLQuery = 'select * from wgn_agencje_foto WHERE id_agencji = :idOffice';
        $stmt = $conn->prepare($imagesSQLQuery);
        $stmt->bindValue('idOffice',$office->getLegacyId());
        $stmt->execute();
        $ordering=1;
        while ($row = $stmt->fetch()) {
           $output->writeln('photo');
           $fileUrl = 'http://wgn.pl/agencje_foto/'.$row['foto'];
            if(!$this->remote_image_exists($fileUrl)){
                $output->writeln('file not exists '.$fileUrl);
                continue;
            }
            $this->insertPhoto($office->getLegacyId(), $office->getId(), $row['foto'], $ordering);
            $ordering++;
        }
    }
    protected function insertPhoto($legacyId,$idOffice,$fileName,$ordering){

        $path = __DIR__ . '/../../../../web/uploads/offices/'.$idOffice;
        if(!file_exists($path)){
           mkdir($path);
        }

        $em = $this->getContainer()->get('doctrine')->getManager();
        $conn = $em->getConnection();

        $imageSQLQuery = 'SELECT id, modifyDate FROM office_image WHERE name = :name AND office_id = :idOffice';
        $stmt2 = $conn->prepare($imageSQLQuery);
        $stmt2->bindValue('name',$fileName);
        $stmt2->bindValue('idOffice',$idOffice);

        $stmt2->execute();
        $imageResult = $stmt2->fetch();
//
        if(!$imageResult){
            $insertImageSQL = "INSERT INTO office_image (name,ordering,modifyDate,office_id) VALUES(:name,:ordering,NOW(),:idOffice)";
            $stmt3 = $conn->prepare($insertImageSQL);
            $stmt3->bindValue('name',$fileName);
            $stmt3->bindValue('ordering',(int)$ordering);
            $stmt3->bindValue('idOffice',(int)$idOffice);
            $stmt3->execute();
            //$conn->lastInsertId();
            $modifyDate = date('Y-m-d');

        }else {
            $modifyDate = $imageResult['modifyDate'];
            $updateImageSQL = "UPDATE office_image SET modifyDate = NOW() ,ordering = :ordering, name = :name WHERE id = :id";
            $stmt4 = $conn->prepare($updateImageSQL);
            $stmt4->bindValue('name',$fileName);
            $stmt4->bindValue('ordering',(int)$ordering);
            $stmt4->bindValue('id',(int)$imageResult['id']);
            $stmt4->execute();
        }

        $ordering++;
        if(!file_exists($path.'/'.$fileName)){
            @unlink($path.'/'.$fileName);
        }
        @copy('http://wgn.pl/agencje_foto/'.$fileName,$path.'/'.$fileName);
    }
    protected function remote_image_exists($url){
        if( @file_get_contents($url) === false ){
            return false;
        }

        return true;
    }
}

