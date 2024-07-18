<?php
/**
 * This file is part of the AppArticeBundle package.
 *
 */
namespace App\ArticleBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Class ArticleRepository
 *
 * @author wojciech przygoda
 */
class ArticleRepository extends EntityRepository
{
    /**
     * Get article max ordering
     * @param int $idCategory article category id
     * @param null $blogId
     * @return int
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getMaxOrdering($idCategory,$blogId = null)
    {

        $em = $this->getEntityManager();
        $connection = $em->getConnection();

        $sql = "SELECT MAX(ordering) AS maxOrdering FROM article WHERE category_id = :idCategory";
        $params = ["idCategory"=>$idCategory];
        if($blogId)
        {
            $params['blogId'] = $blogId;
            $sql .= ' AND blog_id = :blogId';
        } else {
            $sql .= ' AND blog_id IS NULL';
        }
        $statement = $connection->prepare($sql);
        $statement->execute($params);

        $maxOrdering = $statement->fetchColumn(0);

        if($maxOrdering){
            return $maxOrdering;
        }
        return 0;
    }

    /**
     * Find all published articles by category id
     *
     * @param int $idCategory article category id
     * @param int $limit limit
     * @param null $blogId
     * @return array
     */
    public function findAllPublishedByCategoryId($idCategory,$limit,$blogId = null){

        $qb = $this->createQueryBuilder('a')
                      ->join('a.category', 'c')
                      ->leftJoin('a.blog','b')
                      ->where('a.isPublish = 1')
                      ->andWhere('a.publishDate <= :now')
                      ->andWhere('c.id = :id')
                      ->setParameter('id', $idCategory)
                      ->setParameter('now', new \DateTime())
                      ->setMaxResults($limit)
                      ->orderBy('a.createDate','DESC');


        if($blogId)
        {
            $qb->andWhere('b = :blogId')
                ->setParameter('blogId',$blogId);
        }else {
            $qb->andWhere('a.blog is null');
        }

        $query = $qb->getQuery();
        return $query
            ->useResultCache(true, 50+rand(1, 10))
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    /**
     * Find others published articles by category id
     * @param int $id article id
     * @param int $idCategory article category id
     * @param int $limit limit
     * @param null $blogId
     * @return Article[]
     */
    public function findOthersPublishedByCategoryId($id, $idCategory,$limit=20,$blogId = null){
        $qb = $this->createQueryBuilder('a')
                      ->join('a.category', 'c')
                      ->leftJoin('a.blog','b')
                      ->where('a.isPublish = 1')
                      ->andWhere('a.publishDate <= :now')
                      ->andWhere('c.id = :idCategory')
                      ->andWhere('a.id != :id')
                      ->setParameter(':idCategory', $idCategory)
                      ->setParameter(':id', $id)
                      ->setParameter('now', new \DateTime())
                      ->setMaxResults($limit)
                     ->orderBy('a.createDate','DESC');

        if($blogId)
        {
            $qb->andWhere('b = :blogId')
                ->setParameter('blogId',$blogId);
        }else {
            $qb->andWhere('a.blog is null');
        }

        $query = $qb->getQuery()
              ->useResultCache(true, 50+rand(1, 10))
        ;
        return $query->getResult();
    }
    public function findOthersByBlogId($id, $blogId){
        $qb = $this->createQueryBuilder('a')
                      ->join('a.blog','b')
                      ->where('a.isPublish = 1')
                      ->andWhere('a.id != :id')
                      ->andWhere('a.publishDate <= :now')
                      ->andWhere('b = :blogId')
                      ->setParameter(':id', $id)
                      ->setParameter(':now', new \DateTime())
                      ->setParameter(':blogId',$blogId)
                      ->orderBy('a.createDate','DESC');


        $query = $qb->getQuery();
        return $query
            ->useResultCache(true, 50+rand(1, 10))
            ->getResult();
    }

    /**
     * Find published article by id
     * @param int $id article id
     * @return Article
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOnePublished($id){
        $qb = $this->createQueryBuilder('p')
                      ->join('p.category', 'c')
                      ->where('p.isPublish = 1')
                      ->andWhere('p.id = :id')
                      ->andWhere('p.publishDate <= :now')
                      ->setParameter('id', $id)
                      ->setParameter('now', new \DateTime())
                      ->setMaxResults(1);
        $query = $qb->getQuery();

        return $query
            ->useResultCache(true, 50+rand(1, 10))
            ->getOneOrNullResult();
    }

    /**
     * Find previus article
     *
     * @param Article $article
     * @param null $blogId
     * @return Article
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getPrev($article,$blogId = null)
    {
        $qb = $this->createQueryBuilder('p')
                      ->join('p.category', 'c')
                      ->leftJoin('p.blog','b')
                      ->where('p.ordering > :ordering')
                      ->andWhere('c.id = :id')
                      ->setParameter('ordering', $article->getOrdering())
                      ->setParameter('id', $article->getCategory()->getId())
                      ->setMaxResults(1)
                      ->orderBy('p.ordering','ASC');

        if($blogId)
        {
            $qb->andWhere('b = :blogId')
                ->setParameter('blogId',$blogId);
        }else {
            $qb->andWhere('p.blog is null');
        }
        $query = $qb->getQuery();

        $result = $query
            ->getOneOrNullResult();
        if(!$result){
            throw new \Exception('Not Found Preview Article');
        }
        return $result;
    }

    /**
     * Find next article
     *
     * @param Article $article
     * @param null $blogId
     * @return Article
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getNext($article,$blogId = null)
    {
        $qb = $this->createQueryBuilder('p')
                   ->join('p.category', 'c')
                   ->leftJoin('p.blog','b')
                   ->where('p.ordering < :ordering')
                   ->andWhere('c.id = :id')
                   ->setParameter('ordering', $article->getOrdering())
                   ->setParameter('id', $article->getCategory()->getId())
                   ->setMaxResults(1)
                   ->orderBy('p.ordering','DESC')
        ;
        if($blogId)
        {
            $qb->andWhere('b = :blogId')
                ->setParameter('blogId',$blogId);
        }else {
            $qb->andWhere('p.blog is null');
        }
        $query = $qb->getQuery();
        $result = $query
            ->getOneOrNullResult();
        if(!$result){
            throw new \Exception('Not Found Next Article');
        }
        return $result;
    }

    /**
     * Update Articles ordering
     *
     * @param int $lastOrdering last ordering
     * @param int $idCategory article category id
     * @param null $blogId
     * @throws \Doctrine\DBAL\DBALException
     */
    public function updateOrderingAfterDelete($lastOrdering,$idCategory, $blogId = null) {
        $sql = "UPDATE article SET ordering = ordering - 1
                         WHERE ordering > :lastOrdering AND category_id=:idcategory";

        $params = ["idcategory"=> $idCategory,"lastOrdering"=> $lastOrdering];

        if($blogId)
        {   $sql .= ' AND blog_id = :blogId';
            $params['blogId'] = $blogId;
        }else {
            $sql .= ' AND blog_id IS NULL';
        }

        $this->getEntityManager()
             ->getConnection()
             ->prepare($sql)
             ->execute($params);
        }


}
