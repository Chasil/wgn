<?php
namespace App\SitemapBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateArticlesSitemapCommand extends ContainerAwareCommand {
    /**
     * Configure command
     */
    protected function configure()
    {
        $this
            ->setName('generate:sitemap:articles')
            ->setDescription('generate articles sitemap');
    }

    protected function execute( InputInterface $input, OutputInterface $output )
	{
        $manager = $this->getContainer()->get('article.sitemap.manager');
		$path = $manager->getTempPath().'/artykuly';
        
        $pages = $manager->getArticlesPageCount();
        for( $page = 0; $page < $pages; $page++ )
		{
			$posts = $manager->getArticlesPageList( $page );
			
			$xml = $manager->generate(
					$posts,
					'AppSitemapBundle:Sitemap:articles.xml.twig'
				);
			
			$filepath = $path;
			if( $page ) $filepath.=$page;
			$filepath .= '.xml';
			
			file_put_contents( $filepath, $xml );
		}
		
		
    }
}
