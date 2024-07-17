<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Class CategoryOfferDescriptionImageRepository
 *
 * @author wojciech przygoda
 */
class CategoryOfferDescriptionImageRepository extends EntityRepository
{
    /**
     * Get max ordering
     *
     * @param int $id
     * @param bool $tmp
     * @return int
     */
    public function getMaxOrdering($id,$tmp=true)
    {
        $connection = $this->getEntityManager()->getConnection();
        if($tmp){
            $statement = $connection->prepare("SELECT MAX(ordering) AS maxOrdering FROM category_offer_description_image WHERE tmpIdCategoryOfferDescription = :id");
            $statement->execute(array("id"=>$id));
        }else {
            $statement = $connection->prepare("SELECT MAX(ordering) AS maxOrdering FROM category_offer_description_image WHERE category_offer_description_id = :id");
            $statement->execute(array("id"=>$id));
        }
        $maxOrdering = $statement->fetchColumn(0);

        if($maxOrdering){
            return $maxOrdering;
        }
        return 0;
    }
    /**
     * Update categoryOfferDescription id
     *
     * @param string $tmpId tmp id
     * @param int $categoryOfferDescriptionId categoryOfferDescription id
     */
    public function updateCategoryOfferDescriptionId($tmpId,$categoryOfferDescriptionId) {
        $connection = $this->getEntityManager()->getConnection();
        $connection->prepare("UPDATE category_offer_description_image SET category_offer_description_id = :id
                         WHERE tmpIdCategoryOfferDescription = :tmpId")
            ->execute(array("id"=>$categoryOfferDescriptionId,"tmpId"=> $tmpId));


    }
    /**
     * Update other categoryOfferDescriptions ordering
     * @param int $lastOrdering last ordering
     * @param int $id categoryOfferDescription id
     * @param bool $tmp tmp
     */
    public function updateOrderingAfterDelete($lastOrdering,$id,$tmp=true) {
        $connection = $this->getEntityManager()->getConnection();
        if($tmp){
            $connection->prepare("UPDATE category_offer_description_image SET ordering = ordering - 1
                         WHERE ordering > :last AND tmpIdCategoryOfferDescription = :id")
                ->execute(array("id"=>$id,"last"=> $lastOrdering));
        }else {
            $connection->prepare("UPDATE category_offer_description_image SET ordering = ordering - 1
                         WHERE ordering > :last AND category_offer_description_id = :id")
                ->execute(array("id"=>$id,"last"=> $lastOrdering));
        }

    }
    /**
     * Update categoryOfferDescription ordering
     *
     * @param int $id categoryOfferDescription id
     * @param int $ordering ordering
     */
    public function updateOrdering($id, $ordering) {

        $this->getEntityManager()
            ->getConnection()
            ->prepare("UPDATE category_offer_description_image SET ordering = :ordering
                         WHERE id = :id")
            ->execute(array("id"=>$id,"ordering"=> $ordering));

    }
}
