<?php
/**
 * This file is part of the AppSettingsBundle package.
 *
 */
namespace App\SettingsBundle\Repository;

/**
 * Class SettingsRepository
 *
 * @author wojciech przygoda
 */
class SettingsRepository extends \Doctrine\ORM\EntityRepository
{
    public function findCurrent(){
        $qb = $this->createQueryBuilder('s')
                   ->where('s.id = :id')
                   ->setParameter('id',1)
                   ->setMaxResults(1)
                ;
        $query = $qb->getQuery();
        $query->useResultCache(true, 60*60*24, 'current_settings');

        return $query->getOneOrNullResult();
    }
}
