<?php
namespace App\SitemapBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateSitemapCommand extends ContainerAwareCommand {
    /**
     * Configure command
     */
    protected function configure()
    {
        $this
            ->setName('generate:sitemap:index')
            ->setDescription('generate archived offers sitemap');
    }

    protected function execute( InputInterface $input, OutputInterface $output )
	{
        $manager = $this->getContainer()->get('sitemap.manager');
		
        $path = $manager->getSitemapPath();
		$temp = $manager->getTempPath();
		
		//
		# unlink( $path.'/sitemap.xml' );
		array_map( 'unlink', glob($path.'/artykuly*.xml' ));
		array_map( 'unlink', glob($path.'/oferty*.xml' ));
		
		//

		$glob = glob( $temp.'/*.xml' );

		foreach( $glob as $filepath )
		{
			copy( $filepath, $path.'/'.basename( $filepath ) );
		}
		
		//
		$xml = $manager->generate( $glob,
				'AppSitemapBundle:Sitemap:index.xml.twig'
			);
		
		//
		file_put_contents( $path.'/sitemap.xml', $xml );
    }
}
