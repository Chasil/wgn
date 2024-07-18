<?php
namespace App\ImportBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use App\UserBundle\Entity\User;

class DuplicatesAgentsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('duplicates:agents')
            ->setDescription('update carrency exchange rates');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $conn = $em->getConnection();

        $offerSQLQuery = 'SELECT * FROM `user` WHERE importId is not null and office_id is not null group by importId HAVING count(importId)>1';
        $stmt = $conn->prepare($offerSQLQuery);
        $stmt->execute();
        $result = $stmt->fetchAll();

        foreach($result as $row){
            
            $duplicateSQLQuery = 'SELECT u.id, u.firstName, u.lastName, u.importId, o.name as office FROM user u join office o on u.office_id=o.id where u.importId = :importId';
            $stmt = $conn->prepare($duplicateSQLQuery);
            $stmt->bindValue('importId',$row['importId']);
            $stmt->execute();
            $agents = $stmt->fetchAll();
            
            foreach($agents as $agent){
               file_put_contents('agents.csv',$agent['firstName'].";".$agent['lastName'].";".$agent['importId'].";".$agent['office']."\n", FILE_APPEND); 
            }
            

        }

    }
}
?>
