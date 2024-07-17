<?php

/**
 * This file is part of the AppImportBundle package.
 *
 */
namespace App\OfferBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ImportFromInetCommand
 *
 * @author wojciech przygoda
 */
class GenerateSitemapCommand extends ContainerAwareCommand {
    /**
     * Configure command
     */
    protected function configure()
    {
        $this
            ->setName('generate:sitemap:offers_old')
            ->setDescription('generate offers sitemap');
    }

    protected function execute(InputInterface $input, OutputInterface $output){
        $offers = $this->getContainer()->get('offer.manager')->findAll();

        $xml = $this->getContainer()->get('templating')
                    ->render('AppFrontPageBundle:StaticPage:sitemapOffers.xml.twig',
                            array('offers'=>$offers,)
                            );
        $path = __DIR__ . '/../../../../web/sitemap_offers.xml';
        file_put_contents($path, $xml);
        $output->writeln('sitemap was generated.');
    }
}
