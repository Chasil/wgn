<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Entity;

/**
 * Class StreetIndexRepository
 *
 * @author wojciech przygoda
 */
class StreetIndexRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Find suggestions
     * 
     * @param string $query
     * @return array
     */
    public function findSuggestions($query){
        $em = $this->getEntityManager();


        $sqlQuery = 'SELECT s.name FROM AppOfferBundle:StreetIndex s';
        $sqlQuery .= ' WHERE translate(s.name) LIKE :query ORDER BY s.name ASC';
        $qb = $em->createQuery($sqlQuery);
        $qb->setParameter('query', '%'.$query.'%');
        $qb->setMaxResults(6);

        return $qb->getArrayResult();
    }
}
