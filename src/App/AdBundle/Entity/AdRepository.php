<?php
/**
 * This file is part of the AppAdBundle package.
 *
 */
namespace App\AdBundle\Entity;

/**
 * Class AdRepository
 *
 * @author wojciech przygoda
 */
class AdRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     *
     * Find max ordering for ad id
     *
     * @param int $idPosition
     * @return int
     */
    public function findMaxOrdering($idPosition) {

        $em = $this->getEntityManager();
        $connection = $em->getConnection();

        $statement = $connection->prepare("SELECT MAX(ordering) AS maxOrdering FROM ad WHERE position_id = :idCategory");
        $statement->execute(array("idCategory"=>$idPosition));

        $maxOrdering = $statement->fetchColumn(0);

        if($maxOrdering){
            return $maxOrdering;
        }
        return 0;
    }

    /**
     * Find published ads for ad position key
     *
     * @param string $positionKey
     * @return array
     */
    public function findPublishByPositionKey($positionKey){
        $qb = $this->createQueryBuilder('a,o');

        $qb->join('a.position', 'p')
                      ->leftJoin('a.offer','o')
                      ->where('p.uniqKey = :key')
                      ->andWhere('a.isPublish = 1')
                      ->andWhere('(a.startDate <= :now AND a.endDate >= :now)')
                      ->andWhere('(a.clickLimit = 0 OR a.clicks <= a.clickLimit)')
                      ->andWhere('(a.displayLimit = 0 OR a.hits <= a.displayLimit)')
                      ->andWhere('((o.isPublish = 1 AND o.expireDate >= :expireDate) or o.id IS NULL)')

                      ->setParameter('key', $positionKey)
                      ->setParameter('now', new \DateTime())
                      ->setParameter('expireDate', new \DateTimeImmutable())
                      ->orderBy('a.ordering','DESC');

        $query = $qb->getQuery();

        return $query->getArrayResult();
    }
    /**
     * Find published ads for ad position id
     *
     * @param int $idPosition
     * @return array
     */
    public function findPublishByPositionId($idPosition){
        $qb = $this->createQueryBuilder('a');

        $qb->select('a,o')
                      ->join('a.position', 'p')
                      ->leftJoin('a.offer','o')
                      ->where('p.id = :id')
                      ->andWhere('a.isPublish = 1')
                      ->andWhere('(a.startDate <= :now AND a.endDate >= :now)')
                      ->andWhere('(a.clickLimit = 0 OR a.clicks <= a.clickLimit)')
                      ->andWhere('(a.displayLimit = 0 OR a.hits <= a.displayLimit)')
                      ->andWhere('((o.isPublish = 1 AND o.expireDate >= :expireDate) or o.id IS NULL)')
                      ->setParameter('id', $idPosition)
                      ->setParameter('now', new \DateTime())
                      ->setParameter('expireDate', new \DateTimeImmutable())
                      ->orderBy('a.ordering','DESC');

        $query = $qb->getQuery();

        return $query->getArrayResult();
    }
    /**
     * Find published ads for ad position id and office id
     *
     * @param id $idPosition
     * @param id $idOffice
     * @return array
     */
    public function findPublishByPositionIdAndOfficeId($idPosition,$idOffice){
        $qb = $this->createQueryBuilder('a');
        $qb->select('a,o')
                      ->join('a.position', 'p')
                      ->join('a.office', 'off')
                      ->leftJoin('a.offer','o')
                      ->where('p.id = :id')
                      ->andWhere('a.isPublish = 1')
                      ->andWhere('off.id = :idOffice')
                      ->andWhere('(a.startDate <= :now AND a.endDate >= :now)')
                      ->andWhere('(a.clickLimit = 0 OR a.clicks <= a.clickLimit)')
                      ->andWhere('(a.displayLimit = 0 OR a.hits <= a.displayLimit)')
                      ->andWhere('((o.isPublish = 1 AND o.expireDate >= :expireDate) or o.id IS NULL)')
                      ->setParameter('id', $idPosition)
                      ->setParameter('idOffice', $idOffice)
                      ->setParameter('now', new \DateTime())
                      ->setParameter('expireDate', new \DateTimeImmutable())
                      ->orderBy('a.ordering','DESC');

        $query = $qb->getQuery();

        return $query->getArrayResult();
    }

    /**
     * Find ad by id
     *
     * @param int $id
     * @return Ad[]
     */
    public function findByPositionId($id){
        $qb = $this->createQueryBuilder('a')
                      ->join('a.position', 'p')
                      ->leftJoin('a.offer','o')
                      ->where('p.id = :id')
                      ->andWhere('o.isPublish = 1 OR o IS NULL')
                      ->andWhere('o.endDate <= :endDate OR o IS NULL')
                      ->setParameter('id', $id)
                      ->setParameter('endDate', new \DateTimeImmutable())
                      ->orderBy('a.ordering','ASC');

        $query = $qb->getQuery();

        return $query->getResult();
    }

    /**
     * Find previus Ad
     *
     * @param Ad $ad
     * @return Ad
     * @throws \Exception
     */
    public function findPrev($ad){
        $qb = $this->createQueryBuilder('p')
                      ->join('p.position', 'c')
                      ->where('p.ordering > :ordering')
                      ->andWhere('c.id = :id')
                      ->setParameter('ordering', $ad->getOrdering())
                      ->setParameter('id', $ad->getPosition()->getId())
                      ->setMaxResults(1)
                      ->orderBy('p.ordering','ASC');

        $query = $qb->getQuery(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        $result = $query
            ->getOneOrNullResult();

        if(!$result){
            throw new \Exception('Not Found Previus Ad');
        }
        return $result;
    }
    /**
     * Find next ad
     *
     * @param Ad $ad
     * @return Ad
     * @throws \Exception
     */
    public function findNext($ad){
        $qb = $this->createQueryBuilder('p')
                   ->join('p.position', 'c')
                   ->where('p.ordering < :ordering')
                   ->andWhere('c.id = :id')
                   ->setParameter('ordering', $ad->getOrdering())
                   ->setParameter('id', $ad->getPosition()->getId())
                   ->setMaxResults(1)
                   ->orderBy('p.ordering','DESC')
        ;

        $query = $qb->getQuery();

        $result = $query
            ->getOneOrNullResult();
        if(!$result){
            throw new \Exception('Not Found Next Ad');
        }
        return $result;
    }
    /**
     * Update ads ordering
     *
     * @param int $lastOrdering
     * @param int $idPosition
     */
    public function updateOrderingAfterDelete($lastOrdering,$idPosition) {

        $this->getEntityManager()
             ->getConnection()
             ->prepare("UPDATE ad SET ordering = ordering - 1
                         WHERE ordering > :lastOrdering AND position_id=:idposition")
             ->execute(array("idposition"=> $idPosition,"lastOrdering"=> $lastOrdering));
    }

    /**
     * Increment clicks
     *
     * @param int $id
     */
    public function incrementClicks($id) {

        $this->getEntityManager()
             ->getConnection()
             ->prepare("UPDATE ad SET clicks = clicks + 1
                         WHERE id=:id")
             ->execute(array("id"=> $id));
    }

    /**
     * increment hits
     * @param int $id
     */
    public function incrementHits($id) {

        $this->getEntityManager()
             ->getConnection()
             ->prepare("UPDATE ad SET hits = hits + 1
                         WHERE id=:id")
             ->execute(array("id"=> $id));
    }

    public function findByOfferId(int $positionId, int $id){
        return $this->createQueryBuilder('p')
            ->where('p.offer = :offerId')
            ->andWhere('p.position = :positionId')
            ->setParameter('offerId', $id)
            ->setParameter('positionId', $positionId)
            ->setMaxResults(1)
            ->getQuery()->getResult()[0]
        ;
    }
}
