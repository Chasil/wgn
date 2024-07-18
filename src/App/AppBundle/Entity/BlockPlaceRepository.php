<?php
/**
 * This file is part of the AppAppBundle package.
 *
 */
namespace App\AppBundle\Entity;

/**
 * Class BlockPlaceRepository
 *
 * @author wojciech przygoda
 */
class BlockPlaceRepository extends \Doctrine\ORM\EntityRepository
{
    public function findOneByUniqueKey($position) {
        $qb = $this->createQueryBuilder('b');
        $qb->where('b.uniqueKey = :uniqueKey')
           ->setParameter('uniqueKey', $position)
           ->setMaxResults(1)
        ;

//        $query = $qb->getQuery();
//        $query->useResultCache(TRUE, 50* rand(0,20), 'block_place_'.$position);

        return  $qb->getQuery()->getOneOrNullResult();
    }
}
