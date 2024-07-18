<?php
/**
 * Created by PhpStorm.
 * User: KRZYSZTOF
 * Date: 2018-07-10
 * Time: 11:23
 */

namespace App\SitemapBundle\Services;


use Doctrine\ORM\EntityManager;

class SitemapManager
{
	const LIMIT = 5000;
	protected $templating;
	protected $rootDir;
	protected $em;
	
	public function __construct( EntityManager $em, $templating, $rootDir )
	{
		$this->em = $em;
		$this->templating = $templating;
		$this->rootDir = $rootDir;
	}
	
	public function generate( $posts, $template )
	{
		return $this->templating
			->render( $template,
				[ 'posts' => $posts, ]
			);
	}
	
	public function getTempPath()
	{
		return realpath($this->rootDir . '/../web/sitemap');
	}
	
	public function getSitemapPath()
	{
		return realpath($this->rootDir . '/../web');
	}
}