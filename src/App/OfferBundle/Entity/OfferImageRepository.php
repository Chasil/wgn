<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Class OfferImageRepository
 *
 * @author wojciech przygoda
 */
class OfferImageRepository extends EntityRepository
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
            $statement = $connection->prepare("SELECT MAX(ordering) AS maxOrdering FROM offer_image WHERE tmpIdOffer = :id");
            $statement->execute(array("id"=>$id));
        }else {
            $statement = $connection->prepare("SELECT MAX(ordering) AS maxOrdering FROM offer_image WHERE offer_id = :id");
            $statement->execute(array("id"=>$id));
        }
        $maxOrdering = $statement->fetchColumn(0);

        if($maxOrdering){
            return $maxOrdering;
        }
        return 0;
    }
    /**
     * Update offer id
     *
     * @param string $tmpId tmp id
     * @param int $offerId offer id
     */
    public function updateOfferId($tmpId,$offerId) {
        $connection = $this->getEntityManager()->getConnection();
        $connection->prepare("UPDATE offer_image SET offer_id = :id
                         WHERE tmpIdOffer = :tmpId")
                       ->execute(array("id"=>$offerId,"tmpId"=> $tmpId));


    }
    /**
     * Update other offers ordering
     * @param int $lastOrdering last ordering
     * @param int $id offer id
     * @param bool $tmp tmp
     */
    public function updateOrderingAfterDelete($lastOrdering,$id,$tmp=true) {
        $connection = $this->getEntityManager()->getConnection();
        if($tmp){
            $connection->prepare("UPDATE offer_image SET ordering = ordering - 1
                         WHERE ordering > :last AND tmpIdOffer = :id")
                      ->execute(array("id"=>$id,"last"=> $lastOrdering));
        }else {
            $connection->prepare("UPDATE offer_image SET ordering = ordering - 1
                         WHERE ordering > :last AND offer_id = :id")
                       ->execute(array("id"=>$id,"last"=> $lastOrdering));
        }

    }
    /**
     * Update offer ordering
     *
     * @param int $id offer id
     * @param int $ordering ordering
     */
    public function updateOrdering($id, $ordering) {

        $this->getEntityManager()
              ->getConnection()
              ->prepare("UPDATE offer_image SET ordering = :ordering
                         WHERE id = :id")
              ->execute(array("id"=>$id,"ordering"=> $ordering));

    }
}
