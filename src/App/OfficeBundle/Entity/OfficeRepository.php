<?php
/**
 * This file is part of the AppOfficeBundle package.
 *
 */
namespace App\OfficeBundle\Entity;

use App\OfficeBundle\Entity\Office;

/**
 * Class OfficeRepository
 *
 * @author wojciech przygoda
 */
class OfficeRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Find all with default dddress
     * @param int $type office type
     * @param int $isPublish publication state
     * @param string $countryIsoCode
     * @return array
     */
    public function findAllWithDefaultAddress($type,$isPublish, $countryIsoCode = 'pl'){
        $qb = $this->createQueryBuilder('o')
            ->select('o,a')
            ->join('o.addresses','a')
            ->join('a.country','c')
            ->where('a.isDefault=1')
            ->andWhere('c.isoCode = :isoCode')
            ->setParameter('isoCode',$countryIsoCode)
            ->orderBy('o.name','ASC');

        if($type==Office::TYPE_CREDIT){
           $qb->andWhere("o.creditOfferUrl !='' ");
        }elseif($type) {
            $qb->andWhere("o.type = :type")
               ->setParameter('type',$type);
        }
        if($isPublish){
            $qb->andWhere('o.isPublish = :isPublish')
                ->setParameter('isPublish',$isPublish);
        }
        return $qb->getQuery()
            ->getArrayResult();
    }
    function findDevelopment()
    {

        $qb = $this->createQueryBuilder('o')
            ->select('o,a')
            ->join('o.addresses','a')
            ->join('a.country','c')
            ->where('a.isDefault=1')
            ->andWhere('c.isoCode=:isoCode')
            ->andWhere("o.developmentOfferUrl !='' ")
            ->setParameter('isoCode','pl')
            ->orderBy('o.name','ASC');

        return $qb->getQuery()
            ->getArrayResult();
    }
    /**
     * Search office
     *
     * @param string $query query
     * @return array
     */
    public function search($query){
        $em = $this->getEntityManager();
        $sql = 'SELECT o,a FROM AppOfficeBundle:Office o LEFT JOIN o.addresses a WHERE o.isPublish=1 AND o.type = 1 ';

        $words =  $this->prepereLikeKeyWords($query);
        for($i=0; $i<count($words);$i++){
            $sql .= ' AND (translate(o.name) LIKE ';
            $sql .= ':name'.$i;
            $sql .= ' OR o.name LIKE ';
            $sql .= ':name'.$i.' )';
        }
        $sql .= 'Order By o.name ASC';

        $qb = $em->createQuery($sql);
        for($i=0; $i<count($words);$i++){
            $qb->setParameter('name'.$i, '%'.$words[$i].'%');
        }
        return $qb->getArrayResult();
    }
    /**
     * Prepere like key words
     * 
     * @param string $query query
     * @return array
     */
    function prepereLikeKeyWords($query){
        $keywords = str_replace(' ', ',', $query);
        return explode(',',$keywords);
    }
}
