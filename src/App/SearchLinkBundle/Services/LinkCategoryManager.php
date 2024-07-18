<?php
/**
 * This file is part of the AppSearchLinkBundle package.
 *
 */
namespace App\SearchLinkBundle\Services;

use App\SearchLinkBundle\Entity\Category;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
/**
 * Class LinkCategoryManager
 *
 * @author wojciech przygoda
 */
class LinkCategoryManager {
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
     * Get links with pagination
     *
     * @return Paginator
     */
    public function getAllWithPagination(){
        $em = $this->services->get('doctrine')->getManager();
        $request = $this->services->get('request');
        $dql   = "SELECT c FROM AppSearchLinkBundle:Category c";
        $dql .= " ORDER BY c.ordering DESC";

        $query = $em->createQuery($dql);
        $paginator  = $this->services->get('knp_paginator');

        return $paginator->paginate(
            $query,
            $request->query->get('page', 1)/*page number*/,
        $request->query->get('pp', 25)/*limit per page*/
        );
    }
    /**
     * Get all links
     *
     * @return Category[]
     */
    public function getAll(){
        return $this->services->get('doctrine')
                    ->getRepository(Category::class)
                    ->findBy(array(),array('ordering'=>'DESC'));
    }
    /**
     * Get first category
     *
     * @return Category
     */
    public function getFirst(){
        return $this->services->get('doctrine')
                    ->getRepository('AppSearchLinkBundle:Category')
                    ->findFirst(false);
    }
    /**
     * Get main category
     *
     * @return Category
     */
    public function getMain(){
        return $this->services->get('doctrine')
                    ->getRepository('AppSearchLinkBundle:Category')
                    ->findMain();
    }
    /**
     * Save category
     *
     * @param Category $category category
     * @return boolean
     */
    public function save($category) {
        $em = $this->services->get('doctrine')->getManager();
        $em->persist($category);
        $em->flush();

        return true;
    }
    /**
     * Find category by id
     *
     * @param int $id category id
     * @return Category
     * @throws NotFoundHttpException
     */
    public function findById($id)
    {
        $category = $this->services
                         ->get('doctrine')
                         ->getManager()
                         ->getRepository('AppSearchLinkBundle:Category')
                         ->findOneById($id);

        if(!is_object($category)){
            throw new NotFoundHttpException('Nie znaleziono!');
        }

        return $category;
    }
    /**
     * Remove category
     *
     * @param int $id category id
     * @return boolean
     */
    public function remove($id) {
        $em = $this->services->get('doctrine')->getManager();

        $category = $this->findById($id);
        $em->remove($category);
        $em->flush();


        return true;
    }
    /**
     * Change category ordering
     *
     * @param int $id category id
     * @param string $direction direction
     * @return boolean
     * @throws \Exception
     */
    public function changeOrdering($id,$direction) {

        $em = $this->services->get('doctrine')->getManager();
        $repo = $em->getRepository('AppSearchLinkBundle:Category');
        $em->getConnection()->beginTransaction();

        try {
            $category = $this->findById($id);

            switch($direction){
                case 'up':
                    $prevCategory = $repo->getPrev($category);

                    $category->incrementOrdering();
                    $prevCategory->decrementOrdering();

                    $em->persist($prevCategory);
                    $em->persist($category);
                    $em->flush();
                break;

                case 'down':
                    $nextCategory = $repo->getNext($category);
                    $nextCategory->getOrdering();
                    $category->decrementOrdering();
                    $nextCategory->incrementOrdering();
                    $nextCategory->getOrdering();
                    $em->persist($nextCategory);
                    $em->persist($category);
                    $em->flush();
                break;
            }
            $em->getConnection()->commit();
        }catch(\Exception $e){
            $em->getConnection()->rollback();
            throw $e;
        }
        return true;
    }
}

