<?php
namespace App\ImportBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use App\UserBundle\Entity\User;

class ImportDuplicatesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('import:duplicates')
            ->setDescription('update carrency exchange rates');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $results = $this->getDuplicates();
        $break = false;
        foreach($results as $row){
            
            $offers = $this->getByImportId($row['importId']);
            
            foreach ($offers as $offer){
              $output->writeln($offer['id'].' import id '.$offer['importId'].' update '. $offer['updateDate']);
              
              if($offer['updateDate']!=''){
                  $this->update($row['importId'], $offer['id']);
                  
                  //$break = true;
              }
            }
            if($break){
                break;
            }
        }
        
        $output->writeln('update');
    }
    protected function update($importId,$id){
        $em = $this->getContainer()->get('doctrine')->getManager();
        $conn = $em->getConnection();

        $offerSQLQuery = 'UPDATE `offer` set isPublish = 0 , isDelete = 1 where importId = :importId AND id!=:id and updateDate is null';
        $stmt = $conn->prepare($offerSQLQuery);
        $stmt->bindValue('importId',(int)$importId);
        $stmt->bindValue('id',(int)$id);
        $stmt->execute();        
    }

    protected function getByImportId($importId){
        $em = $this->getContainer()->get('doctrine')->getManager();
        $conn = $em->getConnection();

        $offerSQLQuery = 'SELECT * FROM `offer` where importId = :importId';
        $stmt = $conn->prepare($offerSQLQuery);
        $stmt->bindValue('importId',(int)$importId);
        $stmt->execute();
        return $stmt->fetchAll();        
    }

    protected function getDuplicates(){
        $em = $this->getContainer()->get('doctrine')->getManager();
        $conn = $em->getConnection();

        $offerSQLQuery = 'SELECT * FROM `offer` where isPublish=1 and importId > 0 group by importId HAVING count(importId)>1';
        $stmt = $conn->prepare($offerSQLQuery);

        $stmt->execute();
        return $stmt->fetchAll();        
    }
}
?>
