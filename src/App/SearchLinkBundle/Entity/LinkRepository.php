<?php
/**
 * This file is part of the AppSearchLinkBundle package.
 *
 */
namespace App\SearchLinkBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Class LinkRepository
 *
 * @author wojciech przygoda
 */
class LinkRepository extends EntityRepository
{
    /**
     * Get max ordering
     *
     * @param int $idCategory category id
     * @return int
     */
    public function getMaxOrdering($idCategory)
    {

        $em = $this->getEntityManager();
        $connection = $em->getConnection();

        $statement = $connection->prepare("SELECT MAX(ordering) AS maxOrdering FROM link WHERE category_id = :idCategory");
        $statement->execute(array("idCategory"=>$idCategory));

        $maxOrdering = $statement->fetchColumn(0);

        if($maxOrdering){
            return $maxOrdering;
        }
        return 0;
    }
    /**
     * Find all published links by category id
     *
     * @param int $idCategory category id
     * @param int $limit limit
     * @return array
     */
    public function findAllPublishedByCategoryId($idCategory,$limit){
        $qb = $this->createQueryBuilder('a')
                      ->join('a.category', 'c')
                      ->where('a.isPublish = 1')
                      ->andWhere('a.publishDate <= :now')
                      ->andWhere('c.id = :id')
                      ->setParameter('id', $idCategory)
                      ->setParameter('now', new \DateTime())
                      ->setMaxResults($limit)
                      ->orderBy('a.ordering','DESC');
        $query = $qb->getQuery();

        return $query->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }
    /**
     * Find others published links by category id
     * @param int $id id
     * @param int $idCategory category id
     * @param int $limit limit
     * @return type
     */
    public function findOthersPublishedByCategoryId($id, $idCategory,$limit=20){
        $qb = $this->createQueryBuilder('a')
                      ->join('a.category', 'c')
                      ->where('a.isPublish = 1')
                      ->andWhere('a.publishDate <= :now')
                      ->andWhere('c.id = :idCategory')
                      ->andWhere('a.id != :id')
                      ->setParameter(':idCategory', $idCategory)
                      ->setParameter(':id', $id)
                      ->setParameter('now', new \DateTime())
                      ->setMaxResults($limit)
                      ->orderBy('a.ordering','ASC');
        $query = $qb->getQuery();

        return $query->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }
    /**
     * Find plublished link by id
     *
     * @param int $id id
     * @return type
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

        return $query->getOneOrNullResult();
    }
    /**
     * Get previus link
     *
     * @param Link $article link
     * @return null|Link
     * @throws \Exception
     */
    public function getPrev($article)
    {
        $qb = $this->createQueryBuilder('p')
                      ->join('p.category', 'c')
                      ->where('p.ordering > :ordering')
                      ->andWhere('c.id = :id')
                      ->setParameter('ordering', $article->getOrdering())
                      ->setParameter('id', $article->getCategory()->getId())
                      ->setMaxResults(1)
                      ->orderBy('p.ordering','ASC');

        $query = $qb->getQuery();

        $result = $query->getOneOrNullResult();
        if(!$result){
            throw new \Exception('Not Found Preview Article');
        }
        return $result;
    }
    /**
     * Get next link
     *
     * @param Link $article link
     * @return null|Link
     * @throws \Exception
     */
    public function getNext($article)
    {
        $qb = $this->createQueryBuilder('p')
                   ->join('p.category', 'c')
                   ->where('p.ordering < :ordering')
                   ->andWhere('c.id = :id')
                   ->setParameter('ordering', $article->getOrdering())
                   ->setParameter('id', $article->getCategory()->getId())
                   ->setMaxResults(1)
                   ->orderBy('p.ordering','DESC')
        ;

        $query = $qb->getQuery();
        $result = $query->getOneOrNullResult();
        if(!$result){
            throw new \Exception('Not Found Next Article');
        }
        return $result;
    }
    /**
     * Update others links ordering
     *
     * @param int $lastOrdering last ordering
     * @param int $idCategory category id
     */
    public function updateOrderingAfterDelete($lastOrdering,$idCategory) {

        $this->getEntityManager()
             ->getConnection()
             ->prepare("UPDATE article SET ordering = ordering - 1
                         WHERE ordering > :lastOrdering AND category_id=:idcategory")
             ->execute(array("idcategory"=> $idCategory,"lastOrdering"=> $lastOrdering));
        }


}
