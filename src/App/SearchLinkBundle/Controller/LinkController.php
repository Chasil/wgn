<?php
/**
 * This file is part of the AppSearchLinkBundle package.
 *
 */
namespace App\SearchLinkBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\SearchLinkBundle\Entity\Link;
use App\SearchLinkBundle\Form\LinkType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class LinkController
 *
 * @author wojciech przygoda
 */
class LinkController extends Controller
{
    /**
     * Add link
     * @return RedirectResponse|Response
    */
    public function addAction()
    {
        $request = $this->getRequest();
        $link = new Link();
        $category = $this->get('search_link_category.manager')
                         ->findById($request->get('idCategory'));

        $link->setCategory($category);

        $form = $this->createForm(new LinkType(), $link);
        $form->handleRequest($request);

        if($form->isValid()){
            $this->get('search_link.manager')->save($link);

            $this->addFlash('success','Zapisano poprawnie.');

            return $this->redirect($this->generateUrl('backoffice_search_links_list',
                                        array('idCategory'=>$link->getCategory()->getId())));
        }

        return $this->render('AppSearchLinkBundle:Link:add.html.twig', array(
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
        $link= $this->get('search_link.manager')->findById($request->get('id'));

        $form = $this->createForm(new LinkType(), $link);
        $form->handleRequest($request);

        if($form->isValid()){
            $this->get('search_link.manager')->save($link);
            $this->addFlash('success','Zapisano poprawnie.');
            return $this->redirect($this->generateUrl('backoffice_search_links_list',
                                        array('idCategory'=>$link->getCategory()->getId())));
        }

        return $this->render('AppSearchLinkBundle:Link:edit.html.twig', array(
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
        $linkManager = $this->get('search_link.manager');
        $linkCategoryManager = $this->get('search_link_category.manager');


        return $this->render('AppSearchLinkBundle:Link:list.html.twig', array(
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
        $linkManager = $this->get('search_link.manager');
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
        $linkManager = $this->get('search_link.manager');
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

        $linkManager = $this->get('search_link.manager');
        $linkManager->changePublish($id,$publish );

        $this->addFlash('success','Pozycja została zmieniona.');

        return $this->redirect($this->getRequest()->headers->get('referer'));

    }
}
