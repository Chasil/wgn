<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Entity;

/**
 * Class TransactionTypeRepository
 *
 * @author wojciech przygoda
 */
class TransactionTypeRepository extends \Doctrine\ORM\EntityRepository
{

    /**
     * Find One published
     * 
     * @param int $id
     * @return null|TransactionType
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
}
