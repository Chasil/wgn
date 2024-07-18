<?php
namespace App\SitemapBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateOffersSitemapCommand extends ContainerAwareCommand {
    /**
     * Configure command
     */
    protected function configure()
    {
        $this
            ->setName('generate:sitemap:offers')
            ->setDescription('generate offers sitemap');
    }

    protected function execute( InputInterface $input, OutputInterface $output )
	{
        $manager = $this->getContainer()->get('offers.sitemap.manager');
		$path = $manager->getTempPath().'/oferty';
        
        $pages = $manager->getOffersPageCount();
        for( $page = 0; $page < $pages; $page++ )
		{
			$posts = $manager->getOffersPageList( $page );

			$xml = $manager->generate(
					$posts,
					'AppSitemapBundle:Sitemap:offers.xml.twig'
				);

			$filepath = $path;
			if( $page ) $filepath.=$page;
			$filepath .= '.xml';

			file_put_contents( $filepath, $xml );
		}
    }
}
