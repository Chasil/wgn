<?php
namespace App\ImportBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use App\UserBundle\Entity\User;

class CarrencyCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('currency:exchange:rates')
            ->setDescription('update carrency exchange rates');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $offerManager = $this->getContainer()->get('offer.manager');
        $em = $this->getContainer()->get('doctrine')->getManager();
        $url = 'https://www.nbp.pl/kursy/xml/LastA.xml';
        $carrencies = @simplexml_load_file($url);
        foreach($carrencies->pozycja as $carrency){
           $c = $offerManager->findCurrencyByCode($carrency->kod_waluty);
           if($c){
               $rate = str_replace(',', '.', $carrency->kurs_sredni);
               $c->setExchangeRate($rate);
               $em->persist($c);
           }

        }
        $em->flush();
        $output->writeln('update');
    }
}
?>
