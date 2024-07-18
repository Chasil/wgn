<?php
/**
 * This file is part of the AppAppBundle package.
 *
 */
namespace App\AppBundle\Entity;

/**
 * Class BlockElementRepository
 *
 * @author wojciech przygoda
 */
class BlockElementRepository extends \Doctrine\ORM\EntityRepository
{

    /**
     * Get max ordering for block element
     *
     * @param int $idPlace block place id
     * @return int
     */
    public function getMaxOrdering($idPlace)
    {

        $em = $this->getEntityManager();
        $connection = $em->getConnection();

        $statement = $connection->prepare("SELECT MAX(ordering) AS maxOrdering FROM block_element WHERE place_id = :idPlace");
        $statement->execute(array("idPlace"=>$idPlace));

        $maxOrdering = $statement->fetchColumn(0);

        if($maxOrdering){
            return $maxOrdering;
        }
        return 0;
    }

    /**
     * Get previus element
     *
     * @param BlockElement $element block element
     * @return null|BlockElement
     * @throws \Exception
     */
    public function getPrev($element)
    {
        $qb = $this->createQueryBuilder('e')
                      ->join('e.place', 'p')
                      ->where('e.ordering > :ordering')
                      ->andWhere('p.id = :id')
                      ->setParameter('ordering', $element->getOrdering())
                      ->setParameter('id', $element->getPlace()->getId())
                      ->setMaxResults(1)
                      ->orderBy('e.ordering','ASC');

        $query = $qb->getQuery();

        $result = $query->getOneOrNullResult();
        if(!$result){
            throw new \Exception('Not Found Preview Item');
        }
        return $result;
    }

    /**
     * Get next element
     *
     * @param BlockElement $element block element
     * @return null|BlockElement
     * @throws \Exception
     */
    public function getNext($element)
    {
        $qb = $this->createQueryBuilder('e')
                   ->join('e.place', 'p')
                   ->where('e.ordering < :ordering')
                   ->andWhere('p.id = :id')
                   ->setParameter('ordering', $element->getOrdering())
                   ->setParameter('id', $element->getPlace()->getId())
                   ->setMaxResults(1)
                   ->orderBy('e.ordering','DESC')
        ;

        $query = $qb->getQuery();
        $result = $query->getOneOrNullResult();
        if(!$result){
            throw new \Exception('Not Found Next Item');
        }
        return $result;
    }

    /**
     * Update elements ordering
     *
     * @param int $lastOrdering last element ordering
     * @param int $idPlace place id
     */
    public function updateOrderingAfterDelete($lastOrdering,$idPlace) {

        $this->getEntityManager()
             ->getConnection()
             ->prepare("UPDATE block_element SET ordering = ordering - 1
                         WHERE ordering > :lastOrdering AND place_id=:idPlace")
             ->execute(array("idPlace"=> $idPlace,"lastOrdering"=> $lastOrdering));
        }
}
