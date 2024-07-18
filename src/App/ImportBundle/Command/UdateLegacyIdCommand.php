<?php
namespace App\ImportBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use App\UserBundle\Entity\User;

class UdateLegacyIdCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('update:legacy')
            ->setDescription('update carrency exchange rates');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $conn = $em->getConnection();

        $offerSQLQuery = 'SELECT id,signature FROM `offer` WHERE id > 28413 ORDER BY `offer`.`createDate` DESC';
        $stmt = $conn->prepare($offerSQLQuery);
        $stmt->execute();
        $result = $stmt->fetchAll();

        foreach($result as $row){

            $parts = explode('-',$row['signature']);
                if(count($parts)>1){


                    $importId = substr($parts[0],2) . $parts[1];
                }else {
                    $importId = null;
                }

            $output->writeln($row['signature'].' '.$importId);
            $duplicateSQLQuery = 'Update `offer` set importId = :importId where id = :id';
            $stmt = $conn->prepare($duplicateSQLQuery);
            $stmt->bindValue('id',$row['id']);
            $stmt->bindValue('importId',(int)$importId);
            $stmt->execute();


        }
    }
}
?>
