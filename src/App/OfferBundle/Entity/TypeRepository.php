<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Entity;

/**
 * Class TypeRepository
 *
 * @author wojciech przygoda
 */
class TypeRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Find published by id
     *
     * @param int $id
     * @return null|Type
     */
    public function findOnePublished($id){
        $qb = $this->createQueryBuilder('t')
                      ->where('t.isPublish = 1')
                      ->andWhere('t.id = :id')
                      ->setParameter('id', $id)
                      ->setMaxResults(1);
        $query = $qb->getQuery();

        return $query->getOneOrNullResult();
    }
    /**
     * Find by category id
     * 
     * @param int $id
     * @return array
     */
    public function findByCategoryId($id){
        $qb = $this->createQueryBuilder('t')
                   ->join('t.category', 'c')
                   ->where('t.isPublish=1')
                   ->andWhere('c.id=:id')
                   ->setParameter('id', $id)
                   ->orderBy('t.ordering','ASC');
        $query = $qb->getQuery();

        return $query->getArrayResult();
    }
}
