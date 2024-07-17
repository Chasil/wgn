<?php
/**
 * This file is part of the AppMenuBundle package.
 *
 */
namespace App\MenuBundle\Entity;

use Gedmo\Tree\Traits\Repository\ORM\NestedTreeRepositoryTrait;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
/**
 * Class MenuItemRepository
 *
 * @author wojciech przygoda
 */
class MenuItemRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     *
     * @var string children index
     */
    protected $childrenIndex = '__children';

    use NestedTreeRepositoryTrait;

    /**
     * Constructor
     *
     * @param EntityManager $em entity manager
     * @param ClassMetadata $class class metadata
     */
    public function __construct(EntityManager $em, ClassMetadata $class)
    {
        parent::__construct($em, $class);

        $this->initializeTreeRepository($em, $class);
    }

    /**
     * {@inheritDoc}
     *
     * @param string $key
     * @param bool $publish
     * @return array
     */
    public function getTreeByMenuUniqueKey($key,$publish=null) {
        $qb = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('node')
            ->from('AppMenuBundle:MenuItem', 'node')
            ->join('node.menu','m')
            ->orderBy('node.root, node.lft', 'ASC')
            ->where('m.uniqueKey = :key')
            ->andWhere('node.lvl >0')
            ->setParameter('key',$key)
            ;
        if($publish){
            $qb->andWhere('node.isPublish = 1');
        }
        $query =  $qb->getQuery();
        $query->useResultCache(false, 60*1, 'menu_items_'.$key);

        return $query->getArrayResult();
    }
}
