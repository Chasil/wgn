<?php

namespace App\ArticleBundle\Controller;

use App\AppBundle\Component\SubdomainHelper;
use App\ArticleBundle\Entity\Blog;
use App\ArticleBundle\Form\BlogType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BlogController extends Controller
{
    public function listAction()
    {

        return $this->render('AppArticleBundle:Blog:list.html.twig',[
            'blogs'=>$this->get('blog.manager')->getWithPagination()
        ]);
    }

    public function deleteAction()
    {
        $blog = $this->get('blog.manager')
            ->getById($this->get('request')->get('id'));

        if(!is_object($blog)){
            throw new NotFoundHttpException();
        }

        if($blog->hasArticles())
        {
            $this->addFlash('error','Nie można usunąć. Blog posiada artykuły.');
            return $this->redirect($this->get('request')->headers->get('referer'));
        }

        $this->get('blog.manager')
            ->delete($blog);

        $this->addFlash('success','Usunięto poprawnie.');

        return $this->redirect($this->get('request')->headers->get('referer'));
    }
    public function addAction()
    {
        $blog = new Blog();
        $form = $this->createForm(new BlogType(), $blog);

        $form->handleRequest($this->get('request'));

        if($form->isSubmitted() && $form->isValid()){

            $subdomain = SubdomainHelper::prepareSubdomain($blog);

            if(!$this->get('blog.manager')->subdomainExists($subdomain))
            {
                $this->get('blog.manager')->save($blog);
                $this->addFlash('success','Zapisano poprawnie.');

                return $this->redirectToRoute('backoffice_blog_list');
            } else {
                $this->addFlash('error','Blog Istnieje.');
            }

        }
        return $this->render('AppArticleBundle:Blog:form.html.twig',[
            'form'=>$form->createView(),
        ]);
    }

    public function editAction()
    {

        $request = $this->get('request');
        $blog = $this->get('blog.manager')
            ->getById($request->get('id'));

        $currentSubdomain = $blog->getSubdomain();

        $form = $this->createForm(new BlogType(), $blog);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $subdomain = SubdomainHelper::prepareSubdomain($blog);

            if($subdomain == $currentSubdomain || !$this->get('blog.manager')->subdomainExists($subdomain))
            {
                $this->get('blog.manager')->save($blog);
                $this->addFlash('success','Zapisano poprawnie.');

                return $this->redirectToRoute('backoffice_blog_list');
            } else {
                $this->addFlash('error','Blog Istnieje.');
            }
        }
        return $this->render('AppArticleBundle:Blog:form.html.twig',[
            'form'=>$form->createView(),
        ]);
    }

}
