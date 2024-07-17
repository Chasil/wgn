<?php
/**
 * This file is part of the AppMenuBundle package.
 *
 */
namespace App\MenuBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use App\MenuBundle\Entity\MenuItem;

/**
 * Class MenuManager
 *
 * @author wojciech przygoda
 */
class MenuManager {
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
    public function __construct(Container $container) {
      $this->services = $container;
    }
    /**
     * Get all menus
     *
     * @return Menu[]
     */
    public function getAll(){
        $repo = $this->services->get('doctrine')->getManager()
                     ->getRepository('AppMenuBundle:Menu');

        return $repo->findAll();
    }
    /**
     * Get menu items tree
     *
     * @param string $key menu key
     * @param bool $publish publish
     * @return MenuItem[]
     */
    public function getByUniqueKey($key,$publish=null){
        $repo = $this->services->get('doctrine')->getManager()
                     ->getRepository('AppMenuBundle:MenuItem');
        $result = $repo->getTreeByMenuUniqueKey($key,$publish);
        return $nestedTree = $repo->buildTreeArray($result);

    }
    /**
     * Get menu
     * @param string $key menu key
     * @return Menu
     */
    public function getMenuByUniqueKey($key){
        $repo = $this->services->get('doctrine')->getManager()
                     ->getRepository('AppMenuBundle:Menu');
        return $repo->findOneByUniqueKey($key);
    }

    /**
     * Get menu item by id
     *
     * @param int $id menu item id
     * @return MenuItem
     */
    public function findItemById($id){
       $repo = $this->services->get('doctrine')
                    ->getRepository('AppMenuBundle:MenuItem');

       return $repo->findOneById($id);
    }

    /**
     * Change menu item position
     *
     * @param int $id menu item id
     * @param string $position change positiom direction
     */
    public function changeItemPosition($id,$position){
        $item = $this->findItemById($id);
        $em = $this->services->get('doctrine')->getManager();
        $repo = $em->getRepository('AppMenuBundle:MenuItem');

        switch ($position) {
            case 'up':
                $repo->moveUp($item, 1);
            break;
            case 'down':
                $repo->moveDown($item, 1);
            break;
        }
        $em->clear();
    }

    /**
     * Remove item
     *
     * @param int $id menu item id
     */
    public function removeItem($id){
        $item = $this->findItemById($id);
        $root = clone $item->getRoot();
        $em = $this->services->get('doctrine')->getManager();
        $repo = $em->getRepository('AppMenuBundle:MenuItem');
        $repo->removeFromTree($item);
         // clear cached nodes

        $repo->reorder($root,null, 'ASC',  true);
    }

    /**
     * Save menu item
     *
     * @param MenuItem $item menu item
     */
    public function saveItem(MenuItem $item){
        $em = $this->services->get('doctrine')->getManager();
        $em->persist($item);
        $em->flush();
    }

    /**
     * Change menu item publication state
     *
     * @param int $id menu item id
     * @param bool $publish only published item
     */
    public function changeItemPublish($id, $publish){
        $em = $this->services->get('doctrine')->getManager();

        $menuItem = $this->findItemById($id);
        $menuItem->setIsPublish($publish);

        foreach($menuItem->getChildren() as $child){
            $child->setIsPublish($publish);
            $em->persist($child);
        }

        $em->persist($menuItem);
        $em->flush();
    }
}