<?php
namespace App\ImportBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use App\UserBundle\Entity\User;

class ChangeImagesOrderingCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('images:change:ordering')
            ->setDescription('update carrency exchange rates');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $conn = $em->getConnection();
        $offersSQLQuery = 'SELECT * FROM offer WHERE importId IS NOT NULL';
        //$offersSQLQuery = 'SELECT * FROM offer WHERE id = :id';
        $stmt_1 = $conn->prepare($offersSQLQuery);
        //$stmt_1->bindValue('id','24746');
        $stmt_1->execute();
        $i = 0;
        while ($row = $stmt_1->fetch()) {
            $imagesSQLQuery = 'SELECT * FROM offer_image WHERE offer_id = :offer_id ORDER BY name ASC';
            $stmt_2 = $conn->prepare($imagesSQLQuery);
            $stmt_2->bindValue('offer_id',$row['id']);
            $stmt_2->execute();
            $i = 1;
            while ($img = $stmt_2->fetch()) {

                if($i==1){
                    $output->writeln('update main photo');
                    $updateMainSQLQuery = 'UPDATE offer SET mainPhoto = :photo WHERE id = :id';
                    $stmt_4 = $conn->prepare($updateMainSQLQuery);
                    $stmt_4->bindValue('id',$row['id']);
                    $stmt_4->bindValue('photo',$img['name']);
                    $stmt_4->execute();
                }
                $updateSQLQuery = 'UPDATE offer_image SET ordering = :ordering WHERE id = :id';
                $stmt_3 = $conn->prepare($updateSQLQuery);
                $stmt_3->bindValue('id',$img['id']);
                $stmt_3->bindValue('ordering',$i);
                $stmt_3->execute();

                $i++;
            }
        }
    }
}
?>
