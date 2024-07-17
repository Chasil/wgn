<?php
namespace App\ImportBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use App\UserBundle\Entity\User;

class OfferCarrencyCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('offer:calculate:prices')
            ->setDescription('update offers price');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $offerManager = $this->getContainer()->get('offer.manager');
        $offerManager->updatePrices();
        $output->writeln('update offer price');
    }
}

