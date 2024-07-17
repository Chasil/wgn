<?php
/**
 * This file is part of the AppSearchLinkBundle package.
 *
 */
namespace App\SearchLinkBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use App\SearchLinkBundle\Form\CategoryType;

/**
 * Class CategoryController
 *
 * @author wojciech przygoda
 */
class CategoryController extends Controller
{

    /**
     * Edit Link category
     *
     * @param Request $request request
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request)
    {
        $category = $this->get('search_link_category.manager')->findById($request->get('id'));

        $form = $this->createForm(new CategoryType(), $category);
        $form->handleRequest($request);

        if($form->isValid()){
            $this->get('search_link_category.manager')->save($category);

            $this->addFlash('success','Zapisano poprawnie.');
            return new RedirectResponse($this->generateUrl('backoffice_search_link_categories_list',
                                        array()));
        }

        return $this->render('AppSearchLinkBundle:Category:edit.html.twig', array(
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
        return $this->render('AppSearchLinkBundle:Category:list.html.twig', array(
            'categories'=>$this->get('search_link_category.manager')
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
        $this->get('search_link_category.manager')
             ->changeOrdering($id,$direction);

        $this->addFlash('success','Pozycja została zmieniona.');

        return $this->redirect($this->getRequest()->headers->get('referer'));
    }

}
