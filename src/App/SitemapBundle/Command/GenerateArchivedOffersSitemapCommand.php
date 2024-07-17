<?php
namespace App\SitemapBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateArchivedOffersSitemapCommand extends ContainerAwareCommand {
    /**
     * Configure command
     */
    protected function configure()
    {
        $this
            ->setName('generate:sitemap:offers:archived')
            ->setDescription('generate archived offers sitemap');
    }

    protected function execute( InputInterface $input, OutputInterface $output )
	{
        $manager = $this->getContainer()->get('offers.sitemap.manager');
		$path = $manager->getTempPath().'/oferty-archivalne';
        
        $pages = $manager->getArchivedOffersPageCount();
        $output->writeln('pages:' . $pages);
        for( $page = 0; $page < $pages; $page++ )
		{
			$posts = $manager->getArchivedOffersPageList( $page );

			$xml = $manager->generate(
					$posts,
					'AppSitemapBundle:Sitemap:archivedOffers.xml.twig'
				);

			$filepath = $path;
			if( $page ) $filepath.=$page;
			$filepath .= '.xml';

			file_put_contents( $filepath, $xml );
		}
    }
}
