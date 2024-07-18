<?php
/**
 * This file is part of the AppArticeBundle package.
 *
 */
namespace App\ArticleBundle\Services;

use App\AppBundle\Component\UrlHelper;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\ArticleBundle\Entity\Article;
/**
 * Class ArticleManager
 *
 * @author wojciech przygoda
 */
class ArticleManager {
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
     * Get published articles by category id
     *
     * @param int $idCategory
     * @param int $limit
     * @return Article
     */
    public function getPublishedByCategoryId($idCategory,$limit=10, $blogId = null) {
        $repo = $this->services->get('doctrine')->getManager()
                     ->getRepository('AppArticleBundle:Article');
        return $repo->findAllPublishedByCategoryId($idCategory,$limit, $blogId);

    }
    /**
     * Generate route for article
     *
     * @param Article $article
     * @return string
     */
    public function generateRoute(Article $article){
        $router = $this->services->get('router');
        $request = $this->services->get('request');

        $category = UrlHelper::prepare($article->getCategory()->getName());
        $name = UrlHelper::prepare($article->getName());
        return $request->getSchemeAndHttpHost().$router->generate('frontend_article_show', [
            'categoryName'=>$category,
            'id'=>$article->getId(),
            'articleName'=>$name,
         ]
        );
    }
    /**
     * Get others published articles by category id
     *
     * @param int $id
     * @param int $idCategory
     * @param int $limit
     * @return Paginator
     */
    public function getOthersPublishedByCategoryId($id,$idCategory,$limit=10) {

        return $this->services->get('doctrine')
                    ->getRepository('AppArticleBundle:Article')
                    ->findOthersPublishedByCategoryId($id,$idCategory,$limit);

    }
    public function getOthersByBlogId($id,$blogId) {

        return $this->services->get('doctrine')
                    ->getRepository(Article::class)
                    ->findOthersByBlogId($id, $blogId);

    }

    /**
     * Get all articles with pagination
     *
     * @param bool $onlyPublic
     * @param null $blogId
     * @return Paginator
     */
    public function getAll($onlyPublic = false, $blogId = null){
        $request = $this->services->get('request');
        $repo = $this->services->get('doctrine')->getRepository('AppArticleBundle:Article');

        $qb = $repo->createQueryBuilder('a')
                      ->join('a.category', 'c')
                      ->leftJoin('a.blog','b')
                      ->where('c.id = :id')
                      ->orderBy('a.ordering','DESC')
        ;

        if($blogId)
        {
            $qb->andWhere('b = :blogId')
                ->setParameter('blogId',$blogId);
        }else {
            $qb->andWhere('a.blog is null');
        }
        $user = $this->services->get('user.manager')->getCurrentLogged();

        if(in_array('ROLE_WRITER',$user->getRoles()) &&
                !in_array('ROLE_AUTHOR', $user->getRoles())){
            $qb->join('a.owner', 'o')
               ->andWhere('o = :owner')
               ->setParameter('owner', $user);
        }

        if($onlyPublic){
            $qb->andWhere('a.isPublish = 1 AND a.publishDate <= :now')
               ->setParameter('now', new \DateTime());
        }
       if(!$request->query->has('idCategory')){
           $category = $this->services->get('articlecategory.manager')->getMain();
           $request->query->set('idCategory',$category->getId());
        }

        $qb->setParameter('id', $request->get('idCategory'));
        $paginator  = $this->services->get('knp_paginator');

        return $paginator->paginate(
            $qb->getQuery(),
            $request->query->get('page', 1)/*page number*/,
        $request->query->get('pp', 70)/*limit per page*/
        );
    }

    /**
     * Get all articles by category
     *
     * @param bool $onlyPublic
     * @param null $blogId
     * @return Paginator
     */
    public function getAllByCategory($onlyPublic = false, $blogId = null)
    {
        $request = $this->services->get('request');
        $idCategory = $request->get('idCategory');

        if(!$idCategory){
            throw new Exception('Missing idCategory parameter');
        }

        $repo = $this->services->get('doctrine')->getRepository('AppArticleBundle:Article');
        $qb = $repo->createQueryBuilder('a')
                      ->join('a.category', 'c')
                      ->leftJoin('a.blog','b')
                      ->where('c.id = :id')
                      ->setParameter('id', $idCategory)
                      ->orderBy('a.createDate','DESC');

        if($onlyPublic){
            $qb->andWhere('a.isPublish = 1 AND a.publishDate <= :now')
               ->setParameter('now', new \DateTime());
        }
        if($blogId)
        {
            $qb->andWhere('b = :blogId')
                ->setParameter('blogId',$blogId);
        }else {
            $qb->andWhere('a.blog is null');
        }
        $paginator  = $this->services->get('knp_paginator');

        return $paginator->paginate(
            $qb->getQuery(),
            $request->query->get('page', 1)/*page number*/,
        70/*limit per page*/
        );
    }

    /**
     * Get articles by tag
     * @param Tag $tag
     * @return Paginator
     */
    public function getAllByTag($tag, $blogId = null)
    {
        $request = $this->services->get('request');
        $repo = $this->services->get('doctrine')->getRepository('AppArticleBundle:Article');
        $qb = $repo->createQueryBuilder('a')
                      ->join('a.tags','t')
                      ->join('a.category','c')
                      ->leftJoin('a.blog','b')
                      ->where('t.id = :id')
                      ->andWhere('a.isPublish = 1')
                      ->andWhere('a.publishDate <= :now')
                      ->setParameter('now', new \DateTime())
                      ->setParameter('id', $tag->getId())
                      ->orderBy('a.createDate','DESC');
        if($blogId)
        {
            $qb->andWhere('b = :blogId')
                ->setParameter('blogId',$blogId);
        }else {
            $qb->andWhere('a.blog is null');
        }

        $paginator  = $this->services->get('knp_paginator');

        return $paginator->paginate(
            $qb->getQuery(),
            $request->query->get('page', 1)/*page number*/,
        70/*limit per page*/
        );
    }

    /**
     * Add article
     *
     * @param Article $article
     * @param null $blogId
     * @return boolean
     */
    public function add($article,$blogId = null) {
        $user = $this->services->get('user.manager')->getCurrentLogged();
        $article->setOwner($user);
        $this->save($article,$blogId);

        return true;
    }

    /**
     * @param Article $article
     */
    public function generateSlug(Article $article)
    {
        $slug = UrlHelper::prepare($article->getName());
        $i = 1;
        while($this->slugExists($slug))
        {
            $slug = $slug . '-' . $i;
            $i++;
        }

        $article->setSlug($slug);
    }
    public function getBySlug($slug)
    {
        return $this->services
                    ->get('doctrine')
                    ->getManager()
                    ->getRepository(Article::class)
                    ->findOneBySlug($slug)
            ;
    }
    /**
     * Slug exists
     *
     * @param $slug
     * @return boolean
     */
    public function slugExists($slug)
    {
        return is_object($this->getBySlug($slug));
    }
    public function saveSlug($article, $newSlug)
    {
        if($article->getSlug() == $newSlug)
        {
            return $article->getSlug();
        }

        $slug =  UrlHelper::prepare($newSlug);
        $i = 1;
        while($this->slugExists($slug))
        {
            $slug = $slug . '-' . $i;
            $i++;
        }

        $article->setSlug($slug);

        $em = $this->services->get('doctrine')->getManager();
        $em->persist($article);
        $em->flush();

        return $slug;
    }
    public function save($article,$blogId = null) {
        $em = $this->services->get('doctrine')->getManager();
        $repo = $em->getRepository('AppArticleBundle:Article');

        if(!$article->getId()){

            $maxOrdering = $repo->getMaxOrdering($article->getCategory()->getId(), $blogId)+1;
           $article->setOrdering($maxOrdering);
        }elseif($article->isChange('category')){
            $changes = $article->getChanges();
            $maxOrdering = $repo->getMaxOrdering($article->getCategory()->getId(), $blogId)+1;
            $em->getRepository('AppArticleBundle:Article')
               ->updateOrderingAfterDelete($article->getOrdering(),
                                             $changes['category'][0]->getId(),
                                             $blogId
               );
             $article->setOrdering($maxOrdering);
        }

        $em->persist($article);
        $em->flush()
        ;

        return true;
    }
    /**
     *
     * Get article by id
     *
     * @param int $id
     * @param bool $isPublish
     * @return Article
     * @throws NotFoundHttpException
     */
    public function findById($id, $isPublish=false, $exceptionable=true)
    {
        $repo = $this->services
                     ->get('doctrine')
                     ->getManager()
                     ->getRepository('AppArticleBundle:Article')
        ;
        if($isPublish){
            $article = $repo->findOnePublished($id);
        }else {
            $article = $repo->findOneById($id);
        }
        if($exceptionable && !is_object($article)){
            throw new NotFoundHttpException('Nie znaleziono!');
        }

        return $article;
    }
    /**
     * Get article by legacy id
     *
     * @param int $id article legacy id
     * @return Article
     * @throws NotFoundHttpException
     */
    public function findByLegacyId($id)
    {
        $repo = $this->services
                     ->get('doctrine')
                     ->getManager()
                     ->getRepository('AppArticleBundle:Article')
        ;

        $article = $repo->findOneByLegacyId($id);

        if(!is_object($article)){
            throw new NotFoundHttpException('Nie znaleziono!');
        }

        return $article;
    }

    /**
     * Get tag by id
     *
     * @param int $id tag id
     * @return Tag
     * @throws NotFoundHttpException
     */
    public function getTagById($id) {
        $repo = $this->services->get('doctrine')
                     ->getRepository('AppArticleBundle:Tag');

        $tag = $repo->findOneById($id);

        if(!is_object($tag)){
            throw new NotFoundHttpException('Nie znaleziono!');
        }

        return $tag;
    }

    /**
     * Get tag by name
     *
     * @param type $name tag name
     *
     * @return Tag
     * @throws NotFoundHttpException
     */
    public function getTagByName($name) {
        $repo = $this->services->get('doctrine')
                     ->getRepository('AppArticleBundle:Tag');

        $tag = $repo->findOneByName($name);

        if(!is_object($tag)){
            throw new NotFoundHttpException('Nie znaleziono!');
        }

        return $tag;
    }

    /**
     * Change article state
     *
     * @param int $id article id
     * @param int $state article state
     *
     * @return boolean
     */
    public function changeState($id,$state) {

        $this->services
             ->get('doctrine')
             ->getManager()
             ->getRepository('AppArticlesBundle:Article')
             ->changeState($id,$state);

        return true;
    }

    /**
     * Change article ordering
     *
     * @param int $id article id
     * @param string $direction change ordering direction
     * @param null $blogId
     * @return boolean
     * @throws \Exception
     */
    public function changeOrdering($id,$direction) {

        $em = $this->services->get('doctrine')->getManager();
        $repo = $em->getRepository('AppArticleBundle:Article');
        $em->getConnection()->beginTransaction();

        try {
            $article = $this->findById($id);
            $blog = $article->getBlog();

            $blogId = null;
            if(is_object($blog))
            {
                $blogId = $blog->getId();
            }
            switch($direction){
                case 'up':
                    $prevArticle = $repo->getPrev($article,$blogId);

                    $article->incrementOrdering();
                    $prevArticle->decrementOrdering();

                    $em->persist($prevArticle);
                    $em->persist($article);
                    $em->flush();
                break;

                case 'down':
                    $nextArticle = $repo->getNext($article,$blogId);

                    $article->decrementOrdering();
                    $nextArticle->incrementOrdering();

                    $em->persist($nextArticle);
                    $em->persist($article);
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
     * Change article publication state
     *
     * @param int $id article id
     * @param bool $publish publication state
     */
    public function changePublish($id, $publish){
        $cache = $this->services->get('doctrine_cache.providers.main_cache');
        $em = $this->services->get('doctrine')->getManager();

        $article = $this->findById($id);
        $article->setIsPublish($publish);
        $em->persist($article);
        $em->flush();

        $idCategory = $article->getCategory()->getId();
        $cache->delete("others_articles_published_by_category_".$idCategory."_".$article->getId());
        $cache->delete("articles_published_by_category_".$idCategory);
    }

    /**
     * Remove article
     *
     * @param int $id article id
     * @throws \Exception
     */
    public function remove($id){
        $em = $this->services->get('doctrine')->getManager();
        $em->getConnection()->beginTransaction();

        try {
            $article = $this->findById($id);
            $em->remove($article);
            $em->flush();
            $blog = $article->getBlog();
            $blogId = null;
            if(is_object($blog))
            {
                $blogId = $blog->getId();
            }
            $em->getRepository('AppArticleBundle:Article')
               ->updateOrderingAfterDelete($article->getOrdering(),
                                            $article->getCategory()->getId(),
                                            $blogId
                   );
            $em->getConnection()->commit();
        }catch(\Exception $e){
            $em->getConnection()->rollback();
            throw $e;
        }
    }

    /**
     * Save article image
     *
     * @param ArticleImage $image article image
     */
    public function saveImage($image)
    {
        $em = $this->services->get('doctrine')->getManager();
        $repo = $em->getRepository('AppArticleBundle:ArticleImage');

        $image->setOrdering($repo->getMaxOrdering($image->getArticle()->getId())+1);
        $em->persist($image);
        $em->flush();
    }

    /**
     * Get all images
     *
     * @param int $id article id
     * @return ArticleImage[]
     */
    public function findAllImages($id){
        $em = $this->services->get('doctrine')->getManager();
        $repo = $em->getRepository('AppArticleBundle:ArticleImage');
        $query = $repo->createQueryBuilder('i')
                          ->join('i.article', 'o')
                          ->where('o.id = :id')
                          ->orderBy('i.ordering','ASC')
                          ->setParameter('id', $id)
                          ->getQuery();

        return $query->getResult();
    }

    /**
     * Get article image
     *
     * @param int $id article image id
     * @return ArticleImage
     */
     public function findImage($id)
    {
        $repo = $this->services->get('doctrine')->getManager()
                     ->getRepository('AppArticleBundle:ArticleImage');
        return $repo->findOneById($id);
    }

    /**
     * Remove image
     *
     * @param int $id article image id
     * @return boolean
     */
    public function removeImage($id){
        $em = $this->services->get('doctrine')->getManager();
        $repo = $em->getRepository('AppArticleBundle:ArticleImage');
        $image = $this->findImage($id);

        $em->remove($image);
        $em->flush();
        $repo->updateOrderingAfterDelete($image->getOrdering(),$image->getArticle()->getId());

        return true;
    }

    /**
     * Update image ordering
     *
     * @param int $id article image id
     * @param type $ordering ordering
     */
    public function updateImageOrdering($id, $ordering){
        $em = $this->services->get('doctrine')->getManager();
        $repo = $em->getRepository('AppArticleBundle:ArticleImage');
        $repo->updateOrdering($id, $ordering);
    }
}

