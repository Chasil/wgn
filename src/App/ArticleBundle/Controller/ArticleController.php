<?php
/**
 * This file is part of the AppArticeBundle package.
 *
 */
namespace App\ArticleBundle\Controller;

use App\AppBundle\Component\UrlHelper;
use App\ArticleBundle\Entity\ArticleCategory;
use http\Url;
use Intervention\Image\Exception\NotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\ArticleBundle\Entity\Article;
use App\ArticleBundle\Form\ArticleType;
use App\ArticleBundle\Entity\Tag;
use App\ArticleBundle\Model\Recommend;
use App\ArticleBundle\Form\RecommendType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use \Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ArticleController
 *
 * @author wojciech przygoda
 */
class ArticleController extends Controller
{
    /**
     * Add article
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function addAction()
    {
        $request = $this->getRequest();
        $article = new Article();
        if($request->query->has('idCategory'))
        {
            $category = $this->get('doctrine.orm.entity_manager')
                ->getReference(ArticleCategory::class, $request->get('idCategory'));
            $article->setCategory($category);
        }
        $form = $this->createForm(new ArticleType($this->getDoctrine()->getManager()), $article);
        $form->handleRequest($request);

        if($form->isValid()){
            $articleManager = $this->get('article.manager');
            $articleManager->add($article);

            $this->addFlash('success','Zapisano poprawnie.');

            return $this->redirect($this->generateUrl('backoffice_articles_list',
                                        array('idCategory'=>$article->getCategory()->getId())));
        }

        return $this->render('AppArticleBundle:Article:add.html.twig', array(
                'form'=>$form->createView(),
            ));
    }
    /**
     * Edit article
     *
    */
    public function editAction()
    {
        $request = $this->getRequest();
        $articleManager = $this->get('article.manager');

        $article = $articleManager->findById($request->get('id'));
        $this->denyAccessUnlessGranted('owner', $article);

        $form = $this->createForm(new ArticleType($this->getDoctrine()->getManager()), $article);
        $form->handleRequest($request);

        if($form->isValid()){
            $articleManager->save($article);
            $this->addFlash('success','Zapisano poprawnie.');
            return $this->redirect($this->generateUrl('backoffice_articles_list',
                                        array('idCategory'=>$article->getCategory()->getId())));
        }

        return $this->render('AppArticleBundle:Article:edit.html.twig', array(
                'form'=>$form->createView(),
                'article'=>$article,
                'images'=>$articleManager->findAllImages($request->get('id'))
            ));
    }
    /**
     * Edit slug article
     *
     */
    public function saveSlugAction()
    {
        $request = $this->getRequest();
        if(!$request->isXmlHttpRequest())
        {
            throw new AccessDeniedHttpException();
        }
        $articleManager = $this->get('article.manager');

        $article = $articleManager->findById($request->get('id'));

        if(!is_object($article))
        {
            throw new NotFoundHttpException();
        }
        $newSlug = $articleManager->saveSlug($article, $request->get('slug'));

        return new JsonResponse(['success'=>true,'slug'=>$newSlug]);
    }
    /**
     * List of articles
     *
    */
    public function listAction()
    {
        $articleManager = $this->get('article.manager');
        $articleCategoryManager = $this->get('articlecategory.manager');


        return $this->render('AppArticleBundle:Article:list.html.twig', array(
            'articles'=>$articleManager->getAll(),
            'categories'=>$articleCategoryManager->getAll(),
            ));

    }
    /**
     * List of articles from the selected category
     *
     * @param Request $request request
     * @return Response
     */
    public function listByCategoryAction(Request $request)
    {
        $articleManager = $this->get('article.manager');
        $category = $articleManager->findCategoryById($request->get('idCategory'));

        $articles = $articleManager->getAllByCategory();

        return $this->render('AppArticleBundle:Article:listByCategory.html.twig', array(
            'items'=>$articles,
            'category'=>$category,
            'categories'=>$articleManager->getAllCategories(true)));
    }
    /**
     * List of articles in modal view
     *
    */
    public function listModalAction()
    {
        $articleManager = $this->get('article.manager');
        $articleCategoryManager = $this->get('articlecategory.manager');


        return $this->render('AppArticleBundle:Article:listModal.html.twig', array(
            'articles'=>$articleManager->getAll(),
            'categories'=>$articleCategoryManager->getAll(),
            ));
    }
    /**
     * List of articles with specyfic tag
     *
    */
    public function listByTagAction()
    {
        $request = $this->getRequest();
        $articleManager = $this->get('article.manager');

        $tag = $articleManager->getTagById($request->get('idTag'));

        $articles = $articleManager->getAllByTag($tag);

        return $this->render('AppArticleBundle:Article:listByTag.html.twig', array(
            'items'=>$articles,
            'tag'=>$tag,
        ));
    }
    /**
     * Delete article
     *
    */
    /**
     * Delete article
     *
     * @param int $id article id
     * @return RedirectResponse
     */
    public function deleteAction($id)
    {
        $articleManager = $this->get('article.manager');
        $article = $articleManager->findById($id);
        $this->denyAccessUnlessGranted('owner', $article);
        $articleManager->remove($id);
        $this->addFlash('success','Usunięto poprawnie.');

        return $this->redirect($this->getRequest()->headers->get('referer'));
    }

    /**
     * Show article
     *
     * @param int $id article id
     * @return Response
     */
    public function showAction($id)
    {
        $article = $this->get('article.manager')->findById($id, false, false);
        $category = null;

        if(is_object($article))
        {
            $category = $article->getCategory();
        }

        /**
         * @var Article $article
         */
        if( !is_object($article) ||
            !$article->getIsPublish())
        {
            if(!$category)
            {
                $categoryManager = $this->get('articlecategory.manager');
                $category = $categoryManager->findById(78);
            }

            return $this->redirectToRoute('frontend_article_category_show', [
                'idCategory' => $category->getId(),
                'categoryName' => UrlHelper::prepare($category->getName()),
            ], 301);
        }

        if($category && in_array($category->getId(), [78, 7, 79, 13, 22]))
        {
            return $this->redirectToRoute('frontend_article_category_show', [
                'idCategory' => $category->getId(),
                'categoryName' => UrlHelper::prepare($category->getName()),
            ], 301);
        }

        $blog = $article->getBlog();
        $host = $this->get('request')->getHost();

        if(is_object($blog) && $host == $this->getParameter('domain'))
        {
            return $this->redirectToRoute('frontend_subdomain_article',[
                'subdomain'=>$blog->getSubdomain(),
                'slug'=>$article->getSlug()
            ],301);
        }

        $others = $this->get('article.manager')
                       ->getOthersPublishedByCategoryId($id,$category->getId(), 50);

        $recommend = new Recommend();
        $route = $this->get('article.manager')->generateRoute($article);

        if(is_object($article->getBlog()) && strpos($this->getRequest()->getUri(), $article->getSlug())===false){
            return $this->redirect($route, 301);
        } else if(!is_object($article->getBlog()) && strpos($this->getRequest()->getUri(), $route)===false){
            return $this->redirect($route, 301);
        }

        $message = "Witaj\nZnalazłem ciekawy artykuł na stronie wgn.pl.Poniżej link\n ".$route;
        $recommend->setMessage($message);
        $form = $this->createForm(new RecommendType(),$recommend);
        $view = 'show';
        if($this->get('request')->query->has('print')){
            $view = 'printShow';
        }

        return $this->render(sprintf('AppArticleBundle:Article:%s.html.twig',$view), array(
            'article'=>$article,
            'category'=>$category,
            'items'=>$others,
            'images'=>$article->getImages(),
            'form'=>$form->createView(),
            ));

    }

    /**
     * Show article in print mode
     *
     * @param int $id article id
     * @return Response
     */
    public function printAction($id)
    {
        $article = $this->get('article.manager')->findById($id,true);
        $category = $article->getCategory();

        $others = $this->get('article.manager')
                       ->getOthersPublishedByCategoryId($id,$category->getId(), 20);

        $recommend = new Recommend();
        $route = $this->get('article.manager')->generateRoute($article);
        $message = "Witaj\nZnalazłem ciekawy artykuł na stronie wgn.pl.Poniżej link\n ".$route;
        $recommend->setMessage($message);
        $form = $this->createForm(new RecommendType(),$recommend);

        return $this->render('AppArticleBundle:Article:show.html.twig', array(
            'article'=>$article,
            'category'=>$category,
            'others'=>$others,
            'images'=>$article->getImages(),
            'form'=>$form->createView(),
            ));

    }

    /**
     * Change article position on list
     *
     * @param int $id article id
     * @param string $direction change ordering direction
     * @return RedirectResponse
     */
    public function changeOrderingAction($id,$direction)
    {
        $articleManager = $this->get('article.manager');
        $articleManager->changeOrdering($id,$direction);

        $this->addFlash('success','Pozycja została zmieniona.');

        return $this->redirect($this->getRequest()->headers->get('referer'));
    }
    /**
     * Change article publication state
     *
    */
    public function changePublishAction()
    {
        $id = $this->getRequest()->get('id');
        $publish = $this->getRequest()->get('publish');

        $articleManager = $this->get('article.manager');
        $article = $articleManager->findById($id);
        $this->denyAccessUnlessGranted('owner', $article);
        $articleManager->changePublish($id,$publish );

        $this->addFlash('success','Pozycja została zmieniona.');

        return $this->redirect($this->getRequest()->headers->get('referer'));

    }
    /**
     * Send Recommend
     *
     * @return JsonResponse
     * @throws AccessDeniedException
     */
    public function sendRecommendAction(){
        $request =  $this->getRequest();
        $recommend = new Recommend();
        $form = $this->createForm(new RecommendType(),
                                  $recommend);
        $form->handleRequest($request);
        if($request->isXmlHttpRequest() &&
            $form->isSubmitted() && $form->isValid()) {
            $mailer = $this->get('mailer');
            $message = $mailer->createMessage()
                ->setSubject('Znajomy polecił ci artykuł ze strony wgn.pl')
                ->setFrom(array($this->getParameter('mail_sender_email')=>$this->getParameter('mail_sender_name')))
                ->setTo($recommend->getEmail())
                ->setBody(
                    $this->renderView(
                        'AppArticleBundle:Emails:recommend.html.twig',
                        array('data'=>$recommend)
                    ),
                    'text/html'
                )
            ;

            $mailer->send($message);
            return new JsonResponse(array('success'=>true));
        }

        throw $this->createAccessDeniedException('You cannot access this page!');
    }

    public function redirectAction($category, $id){
        $article = $this->get('article.manager')->findById($id);

        if(!is_object($article))
        {
            return $this->redirectToRoute('app_front_page_homepage',[],301);
        }
        $categoryName = $category = $article->getCategory()->getName();

        return $this->redirectToRoute('frontend_article_show',[
            'categoryName'=>UrlHelper::prepare($categoryName),
            'articleName' => UrlHelper::prepare($article->getName()),
            'id'=>$article->getId()
        ], 301);
    }
}
