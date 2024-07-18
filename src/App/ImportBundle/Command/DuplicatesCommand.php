<?php
namespace App\ImportBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use App\UserBundle\Entity\User;

class DuplicatesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('duplicates:remove')
            ->setDescription('update carrency exchange rates');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $conn = $em->getConnection();
        $disableSQLQuery = 'SET foreign_key_checks = 0;';
        $stmt0 = $conn->prepare($disableSQLQuery);
        $stmt0->execute();

        $offerSQLQuery = 'SELECT signature , MAX(modifyDate) as md FROM `offer` where isDelete = 0 group by signature HAVING count(signature)>1 ORDER BY MAX(modifyDate) DESC';
        $stmt = $conn->prepare($offerSQLQuery);
        $stmt->execute();
        $result = $stmt->fetchAll();

        foreach($result as $row){
            $output->writeln($row['signature'].' '.$row['md']);
            $duplicateSQLQuery = 'SELECT id, modifyDate FROM `offer` where signature like :signature and modifyDate != :modifyDate';
            $stmt = $conn->prepare($duplicateSQLQuery);
            $stmt->bindValue('modifyDate',$row['md']);
            $stmt->bindValue('signature','%'.$row['signature'].'%');
            $stmt->execute();
            $result = $stmt->fetch();

            $output->writeln('duplikat z :'.$result['id']);
//            $deleteSQLQuery = 'DELETE FROM `offer` where id = :id LIMIT 1';
//            $stmt2 = $conn->prepare($deleteSQLQuery);
//            $stmt2->bindValue('id',$result['id']);
//            $stmt2->execute();
            file_put_contents('log.txt', sprintf("oferta %s zostala usunieta.\n",$result['id']), FILE_APPEND);

        }
        $enableSQLQuery = 'SET foreign_key_checks = 1;';
        $stmt3 = $conn->prepare($enableSQLQuery);
        $stmt3->execute();
    }
}
?>
