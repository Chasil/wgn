<?php
/**
 * This file is part of the AppAppBundle package.
 *
 */
namespace App\AppBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use App\AppBundle\Entity\BlockElement;

/**
 * Class BlockManager
 *
 * @author wojciech przygoda
 */
class BlockManager {

    /**
     *
     * @var Container services container
     */
    private $services;

    /**
     * Constructor
     *
     * @param Container $container services container
     */
    function __construct(Container $container) {
      $this->services = $container;
    }

    /**
     * Get all block elements by position key
     *
     * @param string $position block element position
     * @param bool $isPublish get only published emenetes
     * @return BlockElement[]
     */
    public function getAllElements($position,$isPublish=null){

        $place = $this->services->get('doctrine')->getManager()
              ->getRepository('AppAppBundle:BlockPlace')
              ->findOneByUniqueKey($position);

        if(!is_object($place)){
            return;
        }

        if(!is_null($isPublish)){
            $where = array('place'=>$place,'isPublish'=>$isPublish);
        }else {
            $where = array('place'=>$place);
        }
        $elements = $this->services->get('doctrine')->getManager()
                          ->getRepository('AppAppBundle:BlockElement')
                          ->findBy($where,
                                   array('ordering'=>'DESC'));

        return $elements;
    }

    /**
     * Find block element by id
     * @param int $id block element id
     * @return BlockElement
     * @throws NotFoundHttpException
     */
    public function findById($id)
    {
        $element = $this->services
                     ->get('doctrine')
                     ->getManager()
                     ->getRepository('AppAppBundle:BlockElement')
                     ->findOneById($id);


        if(!is_object($element)){
            throw new NotFoundHttpException('Not Found!');
        }

        return $element;
    }

    /**
     * Get all block places
     *
     * @return BlockPlace[]
     */
    function getPlaces(){
        return $this->services->get('doctrine')->getManager()
                    ->getRepository('AppAppBundle:BlockPlace')
                    ->findAll(array('name'=>'DESC'));
    }

    /**
     *
     * Change block element ordering
     *
     * @param int $id block element id
     * @param string $direction direction to change ordering
     * @return boolean
     * @throws \Exception
     */
    public function changeOrdering($id,$direction) {
        $cache = $this->services->get('doctrine_cache.providers.main_cache');
        $em = $this->services->get('doctrine')->getManager();
        $repo = $em->getRepository('AppAppBundle:BlockElement');
        $em->getConnection()->beginTransaction();

        try {
            $element = $this->findById($id);

            switch($direction){
                case 'up':
                    $prevElement = $repo->getPrev($element);

                    $element->incrementOrdering();
                    $prevElement->decrementOrdering();

                    $em->persist($prevElement);
                    $em->persist($element);
                    $em->flush();
                break;

                case 'down':
                    $nextElement = $repo->getNext($element);

                    $element->decrementOrdering();
                    $nextElement->incrementOrdering();

                    $em->persist($nextElement);
                    $em->persist($element);
                    $em->flush();
                break;
            }
            $cache->delete("block_position_".$element->getPlace()->getUniqueKey()."_elements_");
            $cache->delete("block_position_".$element->getPlace()->getUniqueKey()."_elements_1");
            $em->getConnection()->commit();
        }catch(\Exception $e){
            $em->getConnection()->rollback();
            throw $e;
        }
        return true;
    }

    /**
     * Change block element publication
     *
     * @param int $id block element id
     * @param int $publish publication state
     */
    public function changePublish($id, $publish){
        $cache = $this->services->get('doctrine_cache.providers.main_cache');
        $em = $this->services->get('doctrine')->getManager();

        $element = $this->findById($id);
        $element->setIsPublish($publish);
        $em->persist($element);
        $em->flush();

        $cache->delete("block_position_".$element->getPlace()->getUniqueKey()."_elements_");
        $cache->delete("block_position_".$element->getPlace()->getUniqueKey()."_elements_1");
    }

    /**
     * Save block element
     *
     * @param BlockElement $element block element
     */
    public function save(BlockElement $element){
        $em = $this->services->get('doctrine')->getManager();
        $cache = $this->services->get('doctrine_cache.providers.main_cache');
        $repo = $em->getRepository('AppAppBundle:BlockElement');

        if(!$element->getId()){
           $element->setOrdering($repo->getMaxOrdering($element->getPlace()->getId())+1);
        }elseif($element->isChange('place')){
            $changes = $element->getChanges();
            $em->getRepository('AppAppBundle:BlockElement')
               ->updateOrderingAfterDelete($element->getOrdering(),
                                           $changes['place'][0]->getId());
            $element->setOrdering($repo->getMaxOrdering($element->getPlace()->getId())+1);

            $cache->delete("block_position_".$changes['place'][0]->getUniqueKey()."_elements_");
            $cache->delete("block_position_".$changes['place'][0]->getUniqueKey()."_elements_1");
        }
        $em->persist($element);
        $em->flush();

        $cache->delete("block_position_".$element->getPlace()->getUniqueKey()."_elements_");
        $cache->delete("block_position_".$element->getPlace()->getUniqueKey()."_elements_1");
    }

    /**
     * Remove block element by id
     * @param int $id
     * @throws \Exception
     */
    public function removeById($id){
        $em = $this->services->get('doctrine')->getManager();
        $cache = $this->services->get('doctrine_cache.providers.main_cache');
        $em->getConnection()->beginTransaction();

        try {
            $element = $this->findById($id);
            $em->remove($element);
            $em->flush();

            $em->getRepository('AppAppBundle:BlockElement')
               ->updateOrderingAfterDelete($element->getOrdering(),
                                           $element->getPlace()->getId());
            $em->getConnection()->commit();
        }catch(\Exception $e){
            $em->getConnection()->rollback();
            throw $e;
        }
        $cache->delete("block_position_".$element->getPlace()->getUniqueKey()."_elements_");
        $cache->delete("block_position_".$element->getPlace()->getUniqueKey()."_elements_1");
    }

}

