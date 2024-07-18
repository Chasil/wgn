<?php
/**
 * Created by PhpStorm.
 * User: CP24
 * Date: 25.02.2019
 * Time: 13:29
 */

namespace App\OfferBundle\Controller;


use App\OfferBundle\Entity\Link;
use App\OfferBundle\Form\LinkType;
use App\OfferBundle\Services\LinkManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class LinkController extends Controller
{
    public function listAction($idOffer)
    {
        $linkManager = $this->get('offer.link.manager');
        $offerManager = $this->get('offer.manager');
        $offer = $offerManager->findById($idOffer);

        return $this->render('AppOfferBundle:Link:list.html.twig', array(
            'pagination' => $linkManager->getWidthPagination($idOffer),
            'offer' => $offer
        ));
    }

    private function editActing($linkManager, $offer, $link)
    {
        $request = $this->getRequest();

        $form = $this->createForm(new LinkType(), $link);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $linkManager->save($link);
            $this->addFlash('success','Zapisano poprawnie.');

            return $this->redirectToRoute('backoffice_offer_link_list', array(
                'idOffer' => $offer->getId()
            ));
        }

        return $this->render('AppOfferBundle:Link:edit.html.twig', array(
            'offer' => $offer,
            'form' => $form->createView(),
            'link'=> $link,
        ));
    }

    public function addAction($idOffer)
    {
        $linkManager = $this->get('offer.link.manager');
        $offerManager = $this->get('offer.manager');
        $link = new Link();
        $offer = $offerManager->findById($idOffer);
        $link->setOffer($offer);

        return $this->editActing(
            $linkManager,
            $offer,
            $link
        );
    }

    public function deleteAction($id)
    {
        $linkManager = $this->get('offer.link.manager');
        $link = $linkManager->getById($id);
        $offer = $link->getOffer();
        $linkManager->remove($link);

        $this->addFlash('success','Usunięto poprawnie.');

        return $this->redirectToRoute('backoffice_offer_link_list', array(
            'idOffer' => $offer->getId()
        ));
    }

    public function editAction($id)
    {
        $linkManager = $this->get('offer.link.manager');
        $link = $linkManager->getById($id);
        $offer = $link->getOffer();

        return $this->editActing(
            $linkManager,
            $offer,
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
        $linkManager = $this->get('offer.link.manager');
        $linkManager->changeOrdering($id, $direction);

        $this->addFlash('success','Pozycja została zmieniona.');

        return $this->redirect(
            $this->getRequest()->headers->get('referer')
        );
    }
}