<?php
/**
 * This file is part of the AppAdBundle package.
 *
 */
namespace App\AdBundle\Entity;

/**
 * Class AdPositionRepository
 *
 * @author wojciech przygoda
 */
class AdPositionRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Find ads to list
     * 
     * @return array
     */
    public function findToList(){
        $qb = $this->createQueryBuilder('p')
                    ->select(array('p.id','p.name'))
                    ->orderBy('p.name','DESC');
        $query = $qb->getQuery();

        $result = $query->getArrayResult();
        $rows = array();
        foreach($result as $row){
            $rows[$row['id']] = $row['name'];
        }

        return $rows;
    }
}
