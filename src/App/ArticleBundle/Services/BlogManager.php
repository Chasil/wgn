<?php

namespace App\ArticleBundle\Services;
use App\AppBundle\Component\SubdomainHelper;
use App\AppBundle\Component\UrlHelper;
use App\ArticleBundle\Entity\Article;
use App\ArticleBundle\Entity\Blog;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use \Doctrine\Common\Cache\CacheProvider;

class BlogManager {

    protected $om;
    protected $paginator;
    protected $request;
    protected $cache;

    public function __construct(ObjectManager $om,
                                PaginatorInterface $paginator,
                                RequestStack $requestStack,
                                CacheProvider $cache
            ) {
        $this->om = $om;
        $this->paginator = $paginator;
        $this->cache = $cache;

        if($requestStack->getCurrentRequest()){
            $this->request = $requestStack->getCurrentRequest();
        }
    }
    public function getById($id){
        return $this->om
                    ->getRepository(Blog::class)
                    ->findOneBy(['id'=>$id]);
    }
    public function save(Blog $blog){

        $subdomain = SubdomainHelper::prepareSubdomain($blog);
        $blog->setSubdomain($subdomain);

        $this->om->persist($blog);
        $this->om->flush();

        $this->cache
             ->delete('blog_'
                     .$blog->getTransactionType()->getId()
                     .'_'. $blog->getCategory()->getId()
                     .'_'.$blog->getCity());
    }

    public function subdomainExists($subdomain)
    {
        return is_object($this->getBySubdomain($subdomain));
    }
    public function getArticles(Blog $blog, $limit = 100)
    {
        return $this->om->getRepository(Article::class)->findBy(['blog'=>$blog,'isPublish'=>true],['ordering'=>'DESC'],$limit);
    }
    public function getBySubdomain($subdomain)
    {
        return $this->om->getRepository(Blog::class)->findOneBySubdomain($subdomain);
    }

    public function delete(Blog $blog) {
        $this->cache
             ->delete('blog_'
                     .$blog->getTransactionType()->getId()
                     .'_'. $blog->getCategory()->getId()
                     .'_'.$blog->getCity());

        $this->om->remove($blog);
        $this->om->flush();
    }
    public function getWithPagination(){
        $repo = $this->om
                    ->getRepository(Blog::class);

        $qb = $repo->createQueryBuilder('b');


        return $this->paginator->paginate(
            $qb->getQuery(),
            $this->request->query->get('page', 1)/*page number*/,
            $this->request->query->get('pp', 20)/*limit per page*/
        );
    }

    public function getBlogBy($transactionId,$categoryId,$city){

        $key = 'blog_' .$transactionId
              .'_'. $categoryId .'_'.$city;

        if ($this->cache->contains($key)) {
            $blog = $this->cache->fetch($key);
        } else {
            $blog = $this->om->getRepository(Blog::class)
                                ->findOneByParameters($transactionId,$categoryId,$city);

            $this->cache->save($key, $blog,1);
        }

        return $blog;
    }
}

