<?php
/**
 * This file is part of the ArticleLinkBundle package.
 *
 */
namespace App\ArticleLinkBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use App\ArticleLinkBundle\Form\CategoryType;
use App\ArticleLinkBundle\Entity\Category;

/**
 * Class CategoryController
 *
 * @author wojciech przygoda
 */
class CategoryController extends Controller
{
    /**
     * Add Link category
     *
     * @param Request $request request
     * @return RedirectResponse|Response
     */
    public function addAction(Request $request)
    {
        $category = new Category();
        $form = $this->createForm(new CategoryType(), $category);
        $form->handleRequest($request);

        if($form->isValid()){
            $this->get('article_link_category.manager')->add($category);

            $this->addFlash('success','Zapisano poprawnie.');
            return new RedirectResponse($this->generateUrl('backoffice_article_link_categories_list',
                                        array()));
        }

        return $this->render('AppArticleLinkBundle:Category:edit.html.twig', array(
                'form'=>$form->createView(),
                'category'=>$category,
            ));
    }
    /**
     * Edit Link category
     *
     * @param Request $request request
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request)
    {
        $category = $this->get('article_link_category.manager')->findById($request->get('id'));

        $form = $this->createForm(new CategoryType(), $category);
        $form->handleRequest($request);

        if($form->isValid()){
            $this->get('article_link_category.manager')->save($category);

            $this->addFlash('success','Zapisano poprawnie.');
            return new RedirectResponse($this->generateUrl('backoffice_article_link_categories_list',
                                        array()));
        }

        return $this->render('AppArticleLinkBundle:Category:edit.html.twig', array(
                'form'=>$form->createView(),
                'category'=>$category,
            ));
    }
    /**
     * List of Link category
     * @return Response
    */
    public function listAction()
    {
        return $this->render('AppArticleLinkBundle:Category:list.html.twig', array(
            'categories'=>$this->get('article_link_category.manager')
                               ->getAllWithPagination()));
    }

    /**
     * Change link cateogry position on list
     * @param int $id category id
     * @param string $direction direction
     * @return RedirectResponse
     */
    public function changeOrderingAction($id,$direction)
    {
        $this->get('article_link_category.manager')
             ->changeOrdering($id,$direction);

        $this->addFlash('success','Pozycja została zmieniona.');

        return $this->redirect($this->getRequest()->headers->get('referer'));
    }
    /**
     * Delete category
     *
     * @param int $id category id
     * @return RedirectResponse|Response
    */
    public function deleteAction($id)
    {
        $categoryManager = $this->get('article_link_category.manager');
        $category = $categoryManager->findById($id);

        if($category->hasLinks()){
            $this->addFlash('error','Nie można usunąć kategorii ponieważ posiada linki.');
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }

        $categoryManager->remove($category);
        $this->addFlash('success','Usunięto poprawnie.');


        return $this->redirect($this->getRequest()->headers->get('referer'));
    }
}
