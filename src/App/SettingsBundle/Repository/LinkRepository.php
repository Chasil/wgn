<?php
/**
 * Created by PhpStorm.
 * User: CP24
 * Date: 25.02.2019
 * Time: 14:38
 */

namespace App\SettingsBundle\Repository;

use App\SettingsBundle\Entity\Link;

/**
 * LinkRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class LinkRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Find previous link
     *
     * @param Link $link
     * @return Link
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getPrev($link)
    {
        $qb = $this->createQueryBuilder('l')
            ->where('l.ordering > :ordering')
            ->setParameter('ordering', $link->getOrdering())
            ->setMaxResults(1)
            ->orderBy('l.ordering','ASC');

        $query = $qb->getQuery();
        $result = $query->getOneOrNullResult();

        if(!$result){
            throw new \Exception('Not Found Previous Link');
        }

        return $result;
    }

    /**
     * Find next link
     *
     * @param Link $link
     * @return Link
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getNext($link)
    {
        $qb = $this->createQueryBuilder('l')
            ->where('l.ordering < :ordering')
            ->setParameter('ordering', $link->getOrdering())
            ->setMaxResults(1)
            ->orderBy('l.ordering','DESC')
        ;

        $query = $qb->getQuery();
        $result = $query->getOneOrNullResult();

        if(!$result){
            throw new \Exception('Not Found Next Link');
        }

        return $result;
    }

    /**
     * Get article max ordering
     * @return int
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getMaxOrdering()
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $sql = "SELECT MAX(ordering) AS maxOrdering FROM settings_link";
        $statement = $connection->prepare($sql);
        $statement->execute();

        $maxOrdering = $statement->fetchColumn(0);

        if($maxOrdering){
            return $maxOrdering;
        }

        return 0;
    }

    /**
     * Update Articles ordering
     *
     * @param int $lastOrdering last ordering
     * @param int $idOffice link office id
     * @throws \Doctrine\DBAL\DBALException
     */
    public function updateOrderingAfterDelete($lastOrdering)
    {
        $sql = "UPDATE settings_link SET ordering = ordering - 1
                         WHERE ordering > :lastOrdering";

        $params = array(
            "lastOrdering"=> $lastOrdering
        );

        $this->getEntityManager()
            ->getConnection()
            ->prepare($sql)
            ->execute($params);
    }
}