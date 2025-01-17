<?php

namespace App\OfferBundle\Entity;

/**
 * CategoryOfferDescriptionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CategoryOfferDescriptionRepository extends \Doctrine\ORM\EntityRepository
{
    public function findOneByParameters($transactionId,$categoryId,$city = null) {
        $qb = $this->createQueryBuilder('d');
        $qb->join('d.transactionType','t')
           ->join('d.category','c')
           ->where('c.id = :category')
           ->andWhere('t.id = :transaction')
           ->setParameter('category', $categoryId)
           ->setParameter('transaction', $transactionId)
           ->setMaxResults(1)
        ;

        if($city)
        {
            $qb
                ->andWhere('d.city = :city')
                ->setParameter('city', $city)
            ;
        }else {
            $qb
                ->andWhere($qb->expr()->isNull('d.city'))
            ;
        }

        return $qb->getQuery()->getOneOrNullResult();

    }
}
