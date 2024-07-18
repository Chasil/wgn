<?php

namespace App\SitemapBundle\Services;


class ArticlesSitemapManager extends SitemapManager
{
	public function getArticlesPageCount()
	{
		return ceil($this->em
			->createQuery('SELECT COUNT(a) FROM AppArticleBundle:Article a WHERE a.isPublish = 1')
			->getSingleScalarResult()/self::LIMIT)
			;
	}
	
	public function getArticlesPageList( $page )
	{
		$dql = 'SELECT a, c FROM AppArticleBundle:Article a JOIN a.category c WHERE a.isPublish = 1 ORDER BY a.id ';
		
		$query = $this->em->createQuery( $dql );
		$query->setFirstResult( $page*self::LIMIT );
		$query->setMaxResults( self::LIMIT );
		
		return $query->getResult();
	}
	
	
}