<?php
/**
 * This file is part of the AppArticeBundle package.
 *
 */
namespace App\ArticleBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Class ArticleCategoryRepository
 *
 * @author wojciech przygoda
 */
class ArticleCategoryRepository extends EntityRepository
{
    /**
     * Find categories
     *
     * @param bool $showDeleted
     * @return array
     */
    public function findToList($showDeleted=false){
        $qb = $this->createQueryBuilder('c')
                    ->select(array('c.id','c.name'))
                    ->orderBy('c.name','ASC');

        if(!$showDeleted){
           $qb->where('c.isDelete !=1');
        }

        $query = $qb->getQuery();

        $result = $query->getArrayResult();
        $rows = array();
        foreach($result as $row){
            $rows[$row['id']] = $row['name'];
        }

        return $rows;
    }
    /**
     * Find all categories
     *
     * @param bool $showDeleted
     * @return ArticleCategory[]
     */
    public function findAll($showDeleted=false){
        $qb = $this->createQueryBuilder('c')
                    ->orderBy('c.name','ASC');

        if(!$showDeleted){
           $qb->where('c.isDelete !=1');
        }

        $query = $qb->getQuery();

        return $query->getResult();
    }

    public function findAllInBox()
    {
        $qb = $this->createQueryBuilder('c')
            ->orderBy('c.name','ASC');

        $qb->andWhere('c.isDelete != 1')
            ->andWhere('c.isMain != 1')
            ->andWhere('c.id not in (19,41,12,15)')
        ;

        $query = $qb->getQuery();

        return $query->getResult();
    }

    /**
     * Find first category
     *
     * @param bool $showDeleted
     * @return ArticleCategory
     */
    public function findFirst($showDeleted=false){
        $qb = $this->createQueryBuilder('c')
                    ->orderBy('c.name','ASC')
                    ->setMaxResults(1)
                ;

        if(!$showDeleted){
           $qb->where('c.isDelete !=1');
        }

        $query = $qb->getQuery();

        return $query->getOneOrNullResult();
    }
    /**
     * Find main category
     *
     * @return ArticleCategory
     */
    public function findMain(){
        $qb = $this->createQueryBuilder('c')
                    ->setMaxResults(1)
                    ->where('c.isMain =1')
                ;

        $query = $qb->getQuery();

        return $query->getOneOrNullResult();
    }
    public function findOneById($id) {
        $qb = $this->createQueryBuilder('c');
        $qb->where('c.id = :id')
           ->setParameter('id', $id)
           ->setMaxResults(1)
        ;

        $query = $qb->getQuery();
       // $query->useResultCache(TRUE, 0, 'article_category_'.$id);

        return $query->getOneOrNullResult();
    }
}
