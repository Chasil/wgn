<?php
namespace App\ImportBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use App\UserBundle\Entity\User;

class NewImportIdCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('generate:newimportid')
            ->setDescription('update carrency exchange rates');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $conn = $em->getConnection();

        $userSQLQuery = 'SELECT offer.id, offer.signature, office.importId FROM offer JOIN office ON offer.office_id = office.id WHERE offer.isPublish = 1 AND office_id is not null';
        $stmt = $conn->prepare($userSQLQuery);
        $stmt->execute();


        foreach($stmt->fetchAll() as $row){
            $signature = explode('-',$row['signature']);
            $output->writeln($row['importId'].'-'.$signature[1]);

            $stmt2 = $conn->prepare("UPDATE offer SET newImportId = :newImportId WHERE id = :id");
            $stmt2->bindValue('newImportId',$row['importId'].'-'.$signature[1]);
            $stmt2->bindValue('id',$row['id']);
            $stmt2->execute();
        }
    }
}
?>
