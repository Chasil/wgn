<?php
/**
 * Created by PhpStorm.
 * User: CP24
 * Date: 25.02.2019
 * Time: 14:21
 */

namespace App\SettingsBundle\Controller;


use App\SettingsBundle\Entity\Link;
use App\SettingsBundle\Form\LinkType;
use App\SettingsBundle\Services\LinkManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class LinkController extends Controller
{
    public function listAction()
    {
        $linkManager = $this->get('settings.link.manager');

        return $this->render('AppSettingsBundle:Link:list.html.twig', array(
            'pagination' => $linkManager->getWidthPagination(),
        ));
    }

    private function editActing($linkManager, $link)
    {
        $request = $this->getRequest();

        $form = $this->createForm(new LinkType(), $link);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $linkManager->save($link);
            $this->addFlash('success','Zapisano poprawnie.');

            return $this->redirectToRoute('backoffice_settings_link_list');
        }

        return $this->render('AppSettingsBundle:Link:edit.html.twig', array(
            'form' => $form->createView(),
            'link'=> $link,
        ));
    }

    public function addAction()
    {
        $linkManager = $this->get('settings.link.manager');
        $link = new Link();

        return $this->editActing(
            $linkManager,
            $link
        );
    }

    public function deleteAction($id)
    {
        $linkManager = $this->get('settings.link.manager');
        $link = $linkManager->getById($id);
        $linkManager->remove($link);

        $this->addFlash('success','Usunięto poprawnie.');

        return $this->redirectToRoute('backoffice_settings_link_list');
    }

    public function editAction($id)
    {
        $linkManager = $this->get('settings.link.manager');
        $link = $linkManager->getById($id);

        return $this->editActing(
            $linkManager,
            $link
        );
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
        $linkManager = $this->get('settings.link.manager');
        $linkManager->changeOrdering($id, $direction);

        $this->addFlash('success','Pozycja została zmieniona.');

        return $this->redirect(
            $this->getRequest()->headers->get('referer')
        );
    }
}