<?php
/**
 * This file is part of the AppOfficeBundle package.
 *
 */
namespace App\OfficeBundle\Entity;

/**
 * Class AdditionalServiceRepository
 *
 * @author wojciech przygoda
 */
class AdditionalServiceRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Find all by type
     * 
     * @param int $isPublish
     * @return array
     */
    public function findAllByType($isPublish=1){
         $qb = $this->createQueryBuilder('s')
            ->select('s,o,t,a')
            ->join('s.office','o')
            ->join('s.type','t')
            ->join('o.addresses','a')
            ->where('a.isDefault=1')
            ->addOrderBy('t.ordering','ASC')
            ->addOrderBy('o.name','ASC');

        if($isPublish){
            $qb->andWhere('o.isPublish = :isPublish')
                ->setParameter('isPublish',$isPublish);
        }
        $result =  $qb->getQuery()->getArrayResult();

        $types = array();

        foreach($result as $row){
            $types[$row['type']['id']] = array('name'=>$row['type']['name']);
        }
        foreach($result as $row){
            $types[$row['type']['id']]['office'][] = array('name'=>$row['office']['name'],
                                                           'street'=>$row['office']['addresses'][0]['street'],
                                                           'url'=>$row['url']);
        }
        return $types;
    }
}
