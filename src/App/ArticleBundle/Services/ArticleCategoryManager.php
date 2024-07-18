<?php
/**
 * This file is part of the AppArticeBundle package.
 *
 */
namespace App\ArticleBundle\Services;

use App\AppBundle\Component\UrlHelper;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\ArticleBundle\Entity\ArticleCategory;

/**
 * Class ArticleCategoryManager
 *
 * @author wojciech przygoda
 */
class ArticleCategoryManager {
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
     * Get categories with pagination
     *
     * @return Paginator
     */
    public function getAllWithPagination(){
        $em = $this->services->get('doctrine')->getManager();
        $request = $this->services->get('request');
        $dql   = "SELECT c FROM AppArticleBundle:ArticleCategory c WHERE c.isDelete != 1";
        $dql .= " ORDER BY c.name ASC";

        $query = $em->createQuery($dql);
        $paginator  = $this->services->get('knp_paginator');

        return $paginator->paginate(
            $query,
            $request->query->get('page', 1)/*page number*/,
        $request->query->get('pp', 70)/*limit per page*/
        );
    }
    /**
     * Get all categories
     *
     * @return ArticleCategory[]
     */
    public function getAll(){
        return $this->services->get('doctrine')
                    ->getRepository('AppArticleBundle:ArticleCategory')
                    ->findAll(false);
    }

    public function getAllInBox(){
        return $this->services->get('doctrine')
            ->getRepository('AppArticleBundle:ArticleCategory')
            ->findAllInBox();
    }

    /**
     * Get first category
     *
     * @return ArticleCategory
     */
    public function getFirst(){
        return $this->services->get('doctrine')
                    ->getRepository('AppArticleBundle:ArticleCategory')
                    ->findFirst(false);
    }
    /**
     * Get main cateogry
     *
     * @return ArticleCategory
     */
    public function getMain(){
        return $this->services->get('doctrine')
                    ->getRepository('AppArticleBundle:ArticleCategory')
                    ->findMain();
    }

    /**
     * Save category
     *
     * @param ArticleCategory $category
     * @return boolean
     */
    public function save($category) {
        $em = $this->services->get('doctrine')->getManager();

        $em->persist($category);
        $em->flush();

        $this->changeRobotsFile($category);
        return true;
    }
    /**
     * Get category by id
     *
     * @param int $id
     * @return ArticleCategory
     */
    public function findById($id)
    {
        return $this->services
                         ->get('doctrine')
                         ->getManager()
                         ->getRepository('AppArticleBundle:ArticleCategory')
                         ->findOneById($id);
    }
    /**
     * Remove category
     *
     * @param int $id
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
     * Change robots file
     * 
     * @param ArticleCategory $category
     */
    private function changeRobotsFile(ArticleCategory $category){
        $file = __DIR__ . '/../../../../web/robots.txt';

        $robots = file_get_contents($file);
        $categoryName = UrlHelper::prepare($category->getName());
        $route = $this->services->get('router')
                      ->generate('frontend_article_category_show',array(
                                                                         'idCategory'=>$category->getId(),
                                                                         'categoryName'=>$categoryName)
                              );
        $route = str_replace('/app_dev.php', '', $route);
        $pattern = "Disallow: ". $route;
        if(strpos($robots, $pattern) !== false && !$category->isDisallowRobots()){
            $contents = str_replace($pattern . "\r\n", '', $robots);
        }else {
            $contents = $robots . $pattern . "\r\n";
        }

        file_put_contents($file, $contents);

    }
}

