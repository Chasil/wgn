<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Entity;

/**
 * Class LocationIndexRepository
 *
 * @author wojciech przygoda
 */
class LocationIndexRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Find suggestions
     * 
     * @param string $query
     * @return LocationIndex
     */
    public function findSuggestions($query){
        $em = $this->getEntityManager();


        $sqlQuery = 'SELECT l.name FROM AppOfferBundle:LocationIndex l';
        $sqlQuery .= ' WHERE translate(l.name) LIKE :query OR l.name LIKE :query ORDER BY l.name ASC';
        $qb = $em->createQuery($sqlQuery);
        $qb->setParameter('query', '%'.$query.'%');
        $qb->setMaxResults(6);

        return $qb->getArrayResult();
    }

}
