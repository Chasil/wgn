<?php
namespace App\ImportBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UnpublishFromDBCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('unpublish:db:offers')
            ->setDescription('import offers from db');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        ini_set('max_execution_time', 60*60*1);

        $em = $this->getContainer()->get('doctrine')->getManager();
        $conn = $em->getConnection();

        $em2 = $this->getContainer()->get('doctrine')->getManager('test');
        $conn2 = $em2->getConnection();

        $offersSQLQuery = 'SELECT i.name,signature,o.id as offer_id FROM offer_image i JOIN offer o on i.offer_id = o.id order by o.id';
        $stmt = $conn->prepare($offersSQLQuery);
        $stmt->execute();
        $i = 0;
        while ($row = $stmt->fetch()) {
            $path = __DIR__ . '/../../../../web/uploads/offers/'.$row['offer_id'];
            $dir = substr($row['signature'], 2);
            $dir = str_replace('-', '/', $dir);
            $url  = 'http://www.inet.wgn.pl/oferty/'.$dir.'/foto/';

            $output->writeln('########## offer '.$row['offer_id']);
            if($this->remote_image_exists($url.$row['name'])){

               $output->writeln($url.$row['name']);
               @copy($url.$row['name'],$path.'/'.$row['name']);
            }

        }


        $output->writeln('total '.$i);
    }
    protected function remote_image_exists($url){
        if( @file_get_contents($url) === false ){
            return false;
        }

        return true;
    }
}
?>
