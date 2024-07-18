<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Entity;

/**
 * Class CityIndexRepository
 *
 * @author wojciech przygoda
 */
class CityIndexRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Find suggestions
     * @param sting $query
     * @return array
     */
    public function findSuggestions($query){
        $em = $this->getEntityManager();


        $sqlQuery = 'SELECT c.name FROM AppOfferBundle:CityIndex c';
        $sqlQuery .= ' WHERE translate(c.name) LIKE :query ORDER BY c.name ASC';
        $qb = $em->createQuery($sqlQuery);
        $qb->setParameter('query', '%'.$query.'%');
        $qb->setMaxResults(6);

        return $qb->getArrayResult();
    }
}
