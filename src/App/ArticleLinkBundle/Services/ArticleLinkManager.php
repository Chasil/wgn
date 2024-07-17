<?php
/**
 * This file is part of the AppArticleLinkBundle package.
 *
 */
namespace App\ArticleLinkBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\LinkBundle\Entity\Link;

/**
 * Class LinkManager
 *
 * @author wojciech przygoda
 */
class ArticleLinkManager {
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
     * Get published links by category id
     *
     * @param int $idCategory category id
     * @param int $limit limit
     * @return Link[]
     */
    public function getPublishedByCategoryId($idCategory,$limit=10) {
        $repo = $this->services->get('doctrine')->getManager()
                     ->getRepository('AppArticleLinkBundle:Link');
        return $repo->findAllPublishedByCategoryId($idCategory,$limit);

    }
    /**
     * Get all links
     *
     * @return Link[]
     */
    public function getAll(){
        $request = $this->services->get('request');
        $repo = $this->services->get('doctrine')->getRepository('AppArticleLinkBundle:ArticleLink');
        $qb = $repo->createQueryBuilder('l')
                      ->join('l.category', 'c')
                      ->where('c.id = :id')
                      ->orderBy('l.ordering','DESC');


       if(!$request->query->has('idCategory')){
           $category = $this->services->get('search_link_category.manager')->getMain();
           $request->query->set('idCategory',$category->getId());
        }

        $qb->setParameter('id', $request->get('idCategory'));
        $paginator  = $this->services->get('knp_paginator');

        return $paginator->paginate(
            $qb->getQuery(),
            $request->query->get('page', 1)/*page number*/,
        $request->query->get('pp', 25)/*limit per page*/
        );
    }
    /**
     * Get all links by category
     *
     * @param boolean $onlyPublic only published links
     * @return Paginator
     * @throws Exception
     */
    public function getAllByCategory($onlyPublic = false)
    {
        $request = $this->services->get('request');
        $idCategory = $request->get('idCategory');

        if(!$idCategory){
            throw new Exception('Missing idCategory parameter');
        }

        $repo = $this->services->get('doctrine')->getRepository('AppArticleLinkBundle:ArticleLink');
        $qb = $repo->createQueryBuilder('a')
                      ->join('a.category', 'c')
                      ->where('c.id = :id')
                      ->setParameter('id', $idCategory)
                      ->orderBy('a.ordering','DESC');

        if($onlyPublic){
            $qb->andWhere('a.isPublish = 1 AND a.publishDate <= :now')
               ->setParameter('now', new \DateTime());
        }

        $paginator  = $this->services->get('knp_paginator');

        return $paginator->paginate(
            $qb->getQuery(),
            $request->query->get('page', 1)/*page number*/,
        10/*limit per page*/
        );
    }
    /**
     * Add Link
     *
     * @param Link $link link
     * @return boolean
     */
    public function add($link) {
        $this->save($link);

        return true;
    }
    /**
     * Save Link
     *
     * @param Link $link link
     * @return boolean
     */
    public function save($link) {
        $em = $this->services->get('doctrine')->getManager();
        $repo = $em->getRepository('AppArticleLinkBundle:ArticleLink');

        if(!$link->getId()){
           $link->setOrdering($repo->getMaxOrdering($link->getCategory()->getId())+1);
        }elseif($link->isChange('category')){
            $changes = $link->getChanges();
            $em->getRepository('AppArticleLinkBundle:ArticleLink')
               ->updateOrderingAfterDelete($link->getOrdering(),
                                             $changes['category'][0]->getId());
             $link->setOrdering($repo->getMaxOrdering($link->getCategory()->getId())+1);
        }

        $em->persist($link);
        $em->flush()
        ;

        return true;
    }
    /**
     * Find link by id
     *
     * @param int $id link id
     * @param boolean $isPublish only published
     * @return Link
     * @throws NotFoundHttpException
     */
    public function findById($id,$isPublish=false)
    {
        $repo = $this->services
                     ->get('doctrine')
                     ->getManager()
                     ->getRepository('AppArticleLinkBundle:ArticleLink')
        ;
        if($isPublish){
            $link = $repo->findOnePublished($id);
        }else {
            $link = $repo->findOneById($id);
        }
        if(!is_object($link)){
            throw new NotFoundHttpException('Nie znaleziono!');
        }

        return $link;
    }
    /**
     * Change link ordering
     *
     * @param int $id link id
     * @param string $direction direction
     * @return boolean
     * @throws \Exception
     */
    public function changeOrdering($id,$direction) {

        $em = $this->services->get('doctrine')->getManager();
        $repo = $em->getRepository('AppArticleLinkBundle:ArticleLink');
        $em->getConnection()->beginTransaction();

        try {
            $link = $this->findById($id);

            switch($direction){
                case 'up':
                    $prevLink = $repo->getPrev($link);

                    $link->incrementOrdering();
                    $prevLink->decrementOrdering();

                    $em->persist($prevLink);
                    $em->persist($link);
                    $em->flush();
                break;

                case 'down':
                    $nextLink = $repo->getNext($link);

                    $link->decrementOrdering();
                    $nextLink->incrementOrdering();

                    $em->persist($nextLink);
                    $em->persist($link);
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
    /**
     * Change link publication state
     *
     * @param int $id link id
     * @param type $publish only published
     */
    public function changePublish($id, $publish){
        $cache = $this->services->get('doctrine_cache.providers.main_cache');
        $em = $this->services->get('doctrine')->getManager();

        $link = $this->findById($id);
        $link->setIsPublish($publish);
        $em->persist($link);
        $em->flush();

        $idCategory = $link->getCategory()->getId();
        $cache->delete("others_articles_published_by_category_".$idCategory."_".$link->getId());
        $cache->delete("articles_published_by_category_".$idCategory);
    }
    /**
     * Remove link
     * @param int $id link id
     * @throws \Exception
     */
    public function remove($id){
        $em = $this->services->get('doctrine')->getManager();
        $em->getConnection()->beginTransaction();

        try {
            $link = $this->findById($id);
            $em->remove($link);
            $em->flush();

            $em->getRepository('AppArticleLinkBundle:ArticleLink')
               ->updateOrderingAfterDelete($link->getOrdering(),
                                             $link->getCategory()->getId());
            $em->getConnection()->commit();
        }catch(\Exception $e){
            $em->getConnection()->rollback();
            throw $e;
        }
    }
}

