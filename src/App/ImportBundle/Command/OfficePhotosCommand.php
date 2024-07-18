<?php
namespace App\ImportBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use App\UserBundle\Entity\User;

class OfficePhotosCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('import:office:photos')
            ->setDescription('import office photos');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $conn = $em->getConnection();
        $officesSQLQuery = 'select * from office';
        $stmt_1 = $conn->prepare($officesSQLQuery,$output);
        $stmt_1->execute();
        $i= 0;
        while ($row = $stmt_1->fetch()) {
            $output->writeln('office'.$row['id'].' '.$row['name']);
            $this->insertOfficePhotos($row['importId'],$row['id'],$output);
             $i++;
        }

    }

    protected function insertOfficePhotos($legacyId,$id,$output){
        $em2 = $this->getContainer()->get('doctrine')->getManager('test');
        $conn2 = $em2->getConnection();
        $officesSQLQuery = 'select * from wgn_agencje_foto where id_agencji = :id';
        $stmt_1 = $conn2->prepare($officesSQLQuery);
        $stmt_1->bindValue('id',$legacyId);
        $stmt_1->execute();
        $ordering = 1;
        while ($row = $stmt_1->fetch()) {
            $output->writeln($row['foto']);
            $fileUrl = 'https://wgn.pl/agencje_foto/'.$row['foto'];
            if(!$this->remote_image_exists($fileUrl)){
                $output->writeln('file not exists '.$fileUrl);
                continue;
            }
            $this->insertPhoto($row['id_agencji'],$id,$row['foto'],$ordering);
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
            @copy('https://wgn.pl/agencje_foto/'.$fileName,$path.'/'.$fileName);
        }
    }
    protected function remote_image_exists($url){
        if( @file_get_contents($url) === false ){
            return false;
        }

        return true;
    }
}

