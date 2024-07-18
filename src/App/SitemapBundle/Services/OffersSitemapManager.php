<?php

namespace App\SitemapBundle\Services;


class OffersSitemapManager extends SitemapManager
{
	public function getOffersPageCount()
	{
		return ceil($this->em
				->createQuery('SELECT COUNT(o) FROM AppOfferBundle:Offer o WHERE o.isPublish = 1 AND o.expireDate > CURRENT_TIMESTAMP()')
				->getSingleScalarResult()/self::LIMIT)
			;
	}
	
	public function getOffersPageList( $page )
	{
		$dql = 'SELECT o FROM AppOfferBundle:Offer o WHERE o.isPublish = 1 AND o.expireDate > CURRENT_TIMESTAMP()';
		
		$query = $this->em->createQuery( $dql );
		$query->setFirstResult( $page*self::LIMIT );
		$query->setMaxResults( self::LIMIT );
		
		return $query->getResult();
	}
	
	public function getArchivedOffersPageCount()
	{
		return ceil($this->em
				->createQuery('SELECT COUNT(o) FROM AppOfferBundle:Offer o WHERE o.isPublish = 0 AND o.expireDate < CURRENT_TIMESTAMP()')
				->getSingleScalarResult()/self::LIMIT)
			;
	}
	
	public function getArchivedOffersPageList( $page )
	{
		$dql = 'SELECT o FROM AppOfferBundle:Offer o WHERE o.isPublish = 0 AND o.expireDate < CURRENT_TIMESTAMP()';
		
		$query = $this->em->createQuery( $dql );
		$query->setFirstResult( $page*self::LIMIT );
		$query->setMaxResults( self::LIMIT );
		
		return $query->getResult();
	}
	
}