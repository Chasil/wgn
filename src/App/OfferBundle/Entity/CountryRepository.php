<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Entity;

/**
 * Class CountryRepository
 *
 * @author wojciech przygoda
 */
class CountryRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Find available countries
     *
     * @param int $idCategory id category
     * @param int $idTranasactionType transaction type id
     * @return array
     */
    public function findAvailableCountries($idCategory,$idTranasactionType,$asArray = true) {
        $qb = $this->createQueryBuilder('c')
                   ->join('c.offers', 'o')
                   ->join('o.category','ct')
                   ->join('o.transactionType','t')
                   ->andWhere('ct.id = :idCategory')
                   ->andWhere('t.id = :idTranasactionType')
                   ->setParameter('idCategory',$idCategory)
                   ->setParameter('idTranasactionType',$idTranasactionType)
                   ->groupBy('c.name')
                   ->having('count(o.id)>0')
                   ->orderBy('c.ordering','ASC') ;

        $query = $qb->getQuery();
        $query->useResultCache(true, 60*60*2, 'available_countries_'.$idCategory . '_'.$idTranasactionType);

        if($asArray){
             return $query->getArrayResult();
        }

         return  $query->getResult();
    }

}
