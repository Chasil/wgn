<?php
/**
 * This file is part of the AppArticeBundle package.
 *
 */
namespace App\ArticleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\ArticleBundle\Entity\ArticleCategory;
use App\ArticleBundle\Form\ArticleCategoryType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class CategoryController
 *
 * @author wojciech przygoda
 */
class CategoryController extends Controller
{

    /**
     * Add article category
     *
     * @param Request $request request
     * @return RedirectResponse
     */
    public function addAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_AUTHOR', null, 'Unable to access this page!');
        $category = new ArticleCategory();
        $form = $this->createForm(new ArticleCategoryType(), $category);
        $form->handleRequest($request);

        if($form->isValid()){
            $this->get('articlecategory.manager')->save($category);
            $this->addFlash('success','Zapisano poprawnie.');
            return new RedirectResponse($this->generateUrl('backoffice_article_categories_list',
                                        array()));
        }

        return $this->render('AppArticleBundle:Category:add.html.twig', array(
                'form'=>$form->createView(),
            ));
    }

    /**
     * Edit article category
     *
     * @param Request $request request
     * @return RedirectResponse
     */
    public function editAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_AUTHOR', null, 'Unable to access this page!');
        $category = $this->get('articlecategory.manager')->findById($request->get('id'));

        $form = $this->createForm(new ArticleCategoryType(), $category);
        $form->handleRequest($request);

        if($form->isValid()){
            $this->get('articlecategory.manager')->save($category);

            $this->addFlash('success','Zapisano poprawnie.');
            return new RedirectResponse($this->generateUrl('backoffice_article_categories_list',
                                        array()));
        }

        return $this->render('AppArticleBundle:Category:edit.html.twig', array(
                'form'=>$form->createView(),
                'category'=>$category,
            ));
    }
    /**
     * List of cateogories
     *
    */
    public function listAction()
    {
        return $this->render('AppArticleBundle:Category:list.html.twig', array(
            'categories'=>$this->get('articlecategory.manager')->getAllWithPagination()));
    }
    /**
     * List of cateogories in view modal
     *
    */
    public function ListModalAction()
    {
        return $this->render('AppArticleBundle:Category:listModal.html.twig', array(
            'categories'=>$this->get('articlecategory.manager')->getAllWithPagination()));
    }
    /**
     * Show category
     *
    */
    public function showAction(){
        $request = $this->getRequest();
        $categoryManager = $this->get('articlecategory.manager');
        $articleManager = $this->get('article.manager');

        $category = $categoryManager->findById($request->get('idCategory'));

        if( !is_object($category) ||
            $category->getIsDelete()
        )
        {
            throw new NotFoundHttpException();
        }

        if(in_array($category->getId(), [78, 7, 79, 13, 22]))
        {
            return $this->render('AppArticleBundle:Category:news.html.twig', array(
                'items'=>$articleManager->getAllByCategory(true),
                'category'=>$category,
            ));
        }

        return $this->render('AppArticleBundle:Category:show.html.twig', array(
            'items'=>$articleManager->getAllByCategory(true),
            'category'=>$category,
            ));
    }

    /**
     * Delete category
     *
     * @param int $id article id
     * @return RedirectResponse
     */
    public function deleteAction($id)
    {
        $this->denyAccessUnlessGranted('ROLE_AUTHOR', null, 'Unable to access this page!');
        $category = $this->get('articlecategory.manager')->findById($id);
        if($category->hasArticles()){
            $this->addFlash('error','Nie można usunąć kategorii ponieważ posiada strony.');
        }else {
            $this->get('articlecategory.manager')->remove($category);
            $this->addFlash('success','Usunięto poprawnie.');
        }

        return new RedirectResponse($this->getRequest()->headers->get('referer'));
    }

}
