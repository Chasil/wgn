<?php
/**
 * This file is part of the ArticleLinkBundle package.
 *
 */
namespace App\ArticleLinkBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\ArticleLinkBundle\Entity\ArticleLink;
use App\ArticleLinkBundle\Form\ArticleLinkType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class LinkController
 *
 * @author wojciech przygoda
 */
class ArticleLinkController extends Controller
{
    /**
     * Add link
     * @return RedirectResponse|Response
    */
    public function addAction()
    {
        $request = $this->getRequest();
        $link = new ArticleLink();
        $category = $this->get('article_link_category.manager')
                         ->findById($request->get('idCategory'));

        $link->setCategory($category);

        $form = $this->createForm(new ArticleLinkType(), $link);
        $form->handleRequest($request);

        if($form->isValid()){
            $this->get('article_link.manager')->save($link);

            $this->addFlash('success','Zapisano poprawnie.');

            return $this->redirect($this->generateUrl('backoffice_article_links_list',
                                        array('idCategory'=>$link->getCategory()->getId())));
        }

        return $this->render('AppArticleLinkBundle:ArticleLink:add.html.twig', array(
                'form'=>$form->createView(),
            ));
    }
    /**
     * Edit link
     * @return RedirectResponse|Response
    */
    public function editAction()
    {
        $request = $this->getRequest();
        $link= $this->get('article_link.manager')->findById($request->get('id'));

        $form = $this->createForm(new ArticleLinkType(), $link);
        $form->handleRequest($request);

        if($form->isValid()){
            $this->get('article_link.manager')->save($link);
            $this->addFlash('success','Zapisano poprawnie.');
            return $this->redirect($this->generateUrl('backoffice_article_links_list',
                                        array('idCategory'=>$link->getCategory()->getId())));
        }

        return $this->render('AppArticleLinkBundle:ArticleLink:edit.html.twig', array(
                'form'=>$form->createView(),
                'link'=>$link,
            ));
    }
    /**
     * List of links
     * @return Response
    */
    public function listAction()
    {
        $linkManager = $this->get('article_link.manager');
        $linkCategoryManager = $this->get('article_link_category.manager');


        return $this->render('AppArticleLinkBundle:ArticleLink:list.html.twig', array(
            'links'=>$linkManager->getAll(),
            'categories'=>$linkCategoryManager->getAll(),
            ));

    }
    /**
     * Delete link
     *
     * @param int $id link id
     * @return RedirectResponse|Response
    */
    public function deleteAction($id)
    {
        $linkManager = $this->get('article_link.manager');
        $linkManager->remove($id);
        $this->addFlash('success','Usunięto poprawnie.');

        return $this->redirect($this->getRequest()->headers->get('referer'));
    }
    /**
     * Change link position on list
     *
     * @param int $id link id
     * @param string $direction direction
     * @return RedirectResponse
     *
    */
    public function changeOrderingAction($id,$direction)
    {
        $linkManager = $this->get('article_link.manager');
        $linkManager->changeOrdering($id,$direction);

        $this->addFlash('success','Pozycja została zmieniona.');

        return $this->redirect($this->getRequest()->headers->get('referer'));
    }
    /**
     * Change link publication state
     * @return RedirectResponse
    */
    public function changePublishAction()
    {
        $id = $this->getRequest()->get('id');
        $publish = $this->getRequest()->get('publish');

        $linkManager = $this->get('article_link.manager');
        $linkManager->changePublish($id,$publish );

        $this->addFlash('success','Pozycja została zmieniona.');

        return $this->redirect($this->getRequest()->headers->get('referer'));

    }
}
