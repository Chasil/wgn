<?php
/**
 * This file is part of the AppArticeBundle package.
 *
 */
namespace App\ArticleBundle\Entity;

/**
 * Class TagRepository
 *
 * @author wojciech przygoda
 */
class TagRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Find tag by name
     * 
     * @param string $name
     * @return null|Tag
     */
    public function findOneByName($name){
        $qb = $this->createQueryBuilder('t')
                      ->where('t.name LIKE :name')
                      ->setParameter('name', '%'.$name.'%')
                      ->setMaxResults(1)
        ;
        $query = $qb->getQuery();

        return $query->getOneOrNullResult();
    }
}
