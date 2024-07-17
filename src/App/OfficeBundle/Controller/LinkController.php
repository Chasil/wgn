<?php
/**
 * Created by PhpStorm.
 * User: CP24
 * Date: 22.02.2019
 * Time: 13:32
 */

namespace App\OfficeBundle\Controller;


use App\OfficeBundle\Entity\Link;
use App\OfficeBundle\Form\LinkType;
use App\OfficeBundle\Services\LinkManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class LinkController extends Controller
{
    public function listAction($idOffice)
    {

        $linkManager = $this->get('office.link.manager');
        $officeManager = $this->get('office.manager');
        $office = $officeManager->findById($idOffice);

        $this->denyAccessUnlessGranted('manage_links', $office);

        return $this->render('AppOfficeBundle:Link:list.html.twig', array(
            'pagination' => $linkManager->getWidthPagination($idOffice),
            'office' => $office
        ));
    }

    private function editActing($linkManager, $office, $link)
    {
        $this->denyAccessUnlessGranted('manage_links', $office);

        $request = $this->getRequest();

        $form = $this->createForm(new LinkType(), $link);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $linkManager->save($link);
            $this->addFlash('success','Zapisano poprawnie.');

            return $this->redirectToRoute('backoffice_office_link_list', array(
                'idOffice' => $office->getId()
            ));
        }

        return $this->render('AppOfficeBundle:Link:edit.html.twig', array(
            'office' => $office,
            'form' => $form->createView(),
            'link'=> $link,
        ));
    }

    public function addAction($idOffice)
    {
        $linkManager = $this->get('office.link.manager');
        $officeManager = $this->get('office.manager');
        $link = new Link();
        $office = $officeManager->findById($idOffice);
        $this->denyAccessUnlessGranted('manage_links', $office);
        $link->setOffice($office);

        return $this->editActing(
            $linkManager,
            $office,
            $link
        );
    }

    public function deleteAction($id)
    {
        $linkManager = $this->get('office.link.manager');
        $link = $linkManager->getById($id);
        $office = $link->getOffice();

        $this->denyAccessUnlessGranted('manage_links', $office);

        $linkManager->remove($link);

        $this->addFlash('success','Usunięto poprawnie.');

        return $this->redirectToRoute('backoffice_office_link_list', array(
            'idOffice' => $office->getId()
        ));
    }

    public function editAction($id)
    {
        $linkManager = $this->get('office.link.manager');
        $link = $linkManager->getById($id);
        $office = $link->getOffice();

        $this->denyAccessUnlessGranted('manage_links', $office);

        return $this->editActing(
            $linkManager,
            $office,
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
        $linkManager = $this->get('office.link.manager');
        $linkManager->changeOrdering($id, $direction);

        $this->addFlash('success','Pozycja została zmieniona.');

        return $this->redirect(
            $this->getRequest()->headers->get('referer')
        );
    }
}
