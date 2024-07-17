<?php
/**
 * This file is part of the AppArticeBundle package.
 *
 */
namespace App\ArticleBundle\Controller;

use App\ArticleBundle\Entity\ArticleCategory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\ArticleBundle\Entity\Article;
use App\ArticleBundle\Form\ArticleType;
use App\ArticleBundle\Form\EditArticleType;
use App\ArticleBundle\Entity\Tag;
use App\ArticleBundle\Model\Recommend;
use App\ArticleBundle\Form\RecommendType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use \Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\AppBundle\Component\SubdomainHelper;
/**
 * Class ArticleController
 *
 * @author wojciech przygoda
 */
class BlogArticleController extends Controller
{
    /**
     * Add article
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function addAction()
    {
        $request = $this->getRequest();
        $blog = $this->get('blog.manager')->getById($request->get('id'));

        if(!is_object($blog))
        {
            throw new NotFoundHttpException();
        }

        $article = (new Article())
                    ->setBlog($blog)
        ;

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
            $articleManager->add($article,$blog->getId());

            $this->addFlash('success','Zapisano poprawnie.');

            return $this->redirect($this->generateUrl('backoffice_blog_articles_list',[
                'idCategory'=>$article->getCategory()->getId(),
                'id'=>$blog->getId(),
            ]));
        }

        return $this->render('AppArticleBundle:Article:add.html.twig', array(
                'form'=>$form->createView(),
                 'blog'=>$blog
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

        $form = $this->createForm(new EditArticleType($this->getDoctrine()->getManager()), $article);

        $form->handleRequest($request);

        if($form->isValid()){
            $blog = $article->getBlog();
            $blogId = null;
            if(is_object($blog))
            {
                $blogId = $blog->getId();
            }
            $articleManager->save($article,$blogId);
            $this->addFlash('success','Zapisano poprawnie.');

            return $this->redirect($this->generateUrl('backoffice_blog_articles_list',
                [
                    'idCategory'=>$article->getCategory()->getId(),
                    'id' => $blogId,
                ]
            ));
        }

        return $this->render('AppArticleBundle:Article:edit.html.twig', array(
                'form'=>$form->createView(),
                'article'=>$article,
                'images'=>$articleManager->findAllImages($request->get('id'))
            ));
    }
    /**
     * List of articles
     *
    */
    public function listAction()
    {
        $articleManager = $this->get('article.manager');
        $articleCategoryManager = $this->get('articlecategory.manager');
        $blog = $this->get('blog.manager')->getById($this->get('request')->get('id'));


        return $this->render('AppArticleBundle:BlogArticle:list.html.twig', array(
            'articles'=>$articleManager->getAll(false,$blog->getId()),
            'categories'=>$articleCategoryManager->getAll(),
            'blog'=>$blog
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
     * Delete article
     *
    */
    /**
     * Delete article
     *
     * @param int $id article id
     * @return RedirectResponse
     * @throws \Exception
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
     * @param $slug
     * @return Response
     */
    public function showAction($slug)
    {
        $article = $this->get('article.manager')->getBySlug($slug,true);
        $subdomain = $this->get('request')->get('subdomain');

        if(!is_object($article)) {
            return $this->redirectToRoute('app_front_page_homepage');
        }
        $blog = $article->getBlog();


        if(empty($subdomain)){
            $subdomain = $this->get('request')->get('transaction') . '-'. $this->get('request')->get('type');
        }

        if($blog->getSuBdomain()!=$subdomain)
        {
            return $this->redirectToRoute('frontend_subdomain_article',[
                'subdomain'=>$blog->getSubdomain(),
                'slug'=>$article->getSlug()]
            ,301);
        }
        $others = $this->get('article.manager')
            ->getOthersByBlogId($article->getId(), $blog->getId());
        $recommend = new Recommend();
        $route = $this->get('article.manager')->generateRoute($article);
        $message = "Witaj\nZnalazłem ciekawy artykuł na stronie wgn.pl.Poniżej link\n ".$route;
        $recommend->setMessage($message);
        $form = $this->createForm(new RecommendType(),$recommend);
        $view = 'show';
        if($this->get('request')->query->has('print')){
            $view = 'printShow';
        }

        return $this->render(sprintf('AppArticleBundle:BlogArticle:%s.html.twig',$view), array(
            'article'=>$article,
            'blog'=>$blog,
            'mainImage'=> $this->get('myimage.manager')->getRandom(),
            'images'=>$article->getImages(),
            'form'=>$form->createView(),
            'h1TextColor'=>$this->get('settings.manager')->get(1)->getH1TextColor(),
            'h1Color'=>$this->get('settings.manager')->get(1)->getH1Color(),
            'items'=>$others,
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
     * @throws \Exception
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
}
