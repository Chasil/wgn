<?php
/**
 * This file is part of the AppArticeBundle package.
 *
 */
namespace App\ArticleBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Class ArticleImageRepository
 *
 * @author wojciech przygoda
 *
 */
class ArticleImageRepository extends EntityRepository
{
    /**
     * Get image max ordering
     * @param int $id article image id
     * @return int
     */
    public function getMaxOrdering($id)
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $statement = $connection->prepare("SELECT MAX(ordering) AS maxOrdering FROM article_image WHERE article_id = :id");
        $statement->execute(array("id"=>$id));
        $maxOrdering = $statement->fetchColumn(0);

        if($maxOrdering){
            return $maxOrdering;
        }
        return 0;
    }
    /**
     * Update Images ordering
     * 
     * @param int $lastOrdering last ordering
     * @param int $id image id
     */
    public function updateOrderingAfterDelete($lastOrdering,$id) {

        $this->getEntityManager()
              ->getConnection()
              ->prepare("UPDATE article_image SET ordering = ordering - 1
                         WHERE ordering > :last AND article_id = :id")
              ->execute(array("id"=>$id,"last"=> $lastOrdering));

    }
    /**
     * Update image ordering
     *
     * @param int $id image id
     * @param int $ordering image new ordering
     */
    public function updateOrdering($id, $ordering) {

        $this->getEntityManager()
              ->getConnection()
              ->prepare("UPDATE article_image SET ordering = :ordering
                         WHERE id = :id")
              ->execute(array("id"=>$id,"ordering"=> $ordering));

    }
}
