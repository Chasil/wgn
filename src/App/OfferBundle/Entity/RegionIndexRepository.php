<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Entity;

/**
 * Class RegionIndexRepository
 *
 * @author wojciech przygoda
 */
class RegionIndexRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Find suggestions
     * 
     * @param string $query
     * @return array
     */
    public function findSuggestions($query){
        $em = $this->getEntityManager();


        $sqlQuery = 'SELECT r.name FROM AppOfferBundle:RegionIndex r';
        $sqlQuery .= ' WHERE translate(r.name) LIKE :query ORDER BY r.name ASC';
        $qb = $em->createQuery($sqlQuery);
        $qb->setParameter('query', '%'.$query.'%');
        $qb->setMaxResults(6);

        return $qb->getArrayResult();
    }
}
