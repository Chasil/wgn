<?php
/**
 * This file is part of the AppSearchLinkBundle package.
 *
 */
namespace App\SearchLinkBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Class CategoryRepository
 *
 * @author wojciech przygoda
 */
class CategoryRepository extends EntityRepository
{
    /**
     * Find categories
     * @return array
     */
    public function findToList(){
        $qb = $this->createQueryBuilder('c')
                    ->select(array('c.id','c.name'))
                    ->orderBy('c.name','ASC');

        $query = $qb->getQuery();

        $result = $query->getArrayResult();
        $rows = array();
        foreach($result as $row){
            $rows[$row['id']] = $row['name'];
        }

        return $rows;
    }
    /**
     * Get previus category
     *
     * @param Category $category category
     * @return null|Category
     * @throws \Exception
     */
    public function getPrev($category)
    {
        $qb = $this->createQueryBuilder('c')
                      ->where('c.ordering > :ordering')
                      ->setParameter('ordering', $category->getOrdering())
                      ->setMaxResults(1)
                      ->orderBy('c.ordering','ASC');

        $query = $qb->getQuery();

        $result = $query->getOneOrNullResult();
        if(!$result){
            throw new \Exception('Not Found Preview Article');
        }
        return $result;
    }
    /**
     * Get next category
     *
     * @param Category $category category
     * @return null|Category
     * @throws \Exception
     */
    public function getNext($category)
    {
        $qb = $this->createQueryBuilder('c')
                   ->where('c.ordering < :ordering')
                   ->setParameter('ordering', $category->getOrdering())
                   ->setMaxResults(1)
                   ->orderBy('c.ordering','DESC')
        ;

        $query = $qb->getQuery();
        $result = $query->getOneOrNullResult();
        if(!$result){
            throw new \Exception('Not Found Next Article');
        }
        return $result;
    }
    /**
     * Get first category
     *
     * @return null|Category
     * @throws \Exception
     */
    public function findFirst(){
        $qb = $this->createQueryBuilder('c')
                    ->orderBy('c.name','ASC')
                    ->setMaxResults(1)
                ;

        $query = $qb->getQuery();

        return $query->getOneOrNullResult();
    }
    /**
     * Get main category
     *
     * @return null|Category
     * @throws \Exception
     */
    public function findMain(){
        $qb = $this->createQueryBuilder('c')
                    ->setMaxResults(1)
                    ->where('c.isMain =1')
                ;

        $query = $qb->getQuery();

        return $query->getOneOrNullResult();
    }
}
