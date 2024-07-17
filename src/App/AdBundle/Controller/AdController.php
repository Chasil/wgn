<?php
/**
 * This file is part of the AppAdBundle package.
 *
 */
namespace App\AdBundle\Controller;

use App\AppBundle\Component\UrlHelper;
use App\ImportBundle\Command\CheckLocationsCommand;
use Gedmo\Sluggable\SluggableTest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use \Symfony\Component\HttpFoundation\Response;
use \Symfony\Component\HttpFoundation\Request;

use App\AdBundle\Entity\Ad;
use App\AdBundle\Form\AdType;

/**
 * Class AdController
 *
 * @author wojciech przygoda
 */
class AdController extends Controller
{
    /**
    * Add ad
    * @return Response
    */
    public function addAction()
    {
        $request = $this->getRequest();
        $idOffice = $request->query->get('idOffice');

        $ad = new Ad();

        if($request->query->has('idPosition')){
            $position = $this->get('adposition.manager')
                             ->findById($request->query->get('idPosition'));

            $ad->setPosition($position);

        }
        if($request->query->has('idOffice')){

            $officeManager = $this->get('office.manager');
            $office = $officeManager->findById($idOffice);
            $ad->setOffice($office);
        }
        $this->denyAccessUnlessGranted('manage', $ad);

        $form = $this->createForm(
            new AdType($this->getDoctrine()->getManager(), $request->query->has('idOffice')),
            $ad
        );
        $form->handleRequest($request);

        if($form->isValid()){
            $this->get('ad.manager')
                 ->save($ad);

            $this->addFlash('success','Zapisano poprawnie.');

            return $this->redirect($this->generateUrl('backoffice_ads_list',
                                        array('idPosition'=>$ad->getPosition()->getId(),
                                              'idOffice'=>$idOffice)));
        }

        return $this->render('AppAdBundle:Ad:add.html.twig', array(
                'form'=>$form->createView(),
                'ad'=>$ad
            ));
    }
    /**
    * Edit exist ad
    * @return Response | RedirectResponse
    */
    public function editAction()
    {
        $request = $this->getRequest();

        $ad = $this->get('ad.manager')
                   ->findById($request->get('id'));

        $office = $ad->getOffice();

        $isOfficePosition = (is_object($office))? true : false;
        $idOffice = (is_object($office))? $office->getId() : null;

        $form = $this->createForm(new AdType($this->getDoctrine()->getManager(),
                                             $isOfficePosition),
                                  $ad);
        $form->handleRequest($request);

        if($form->isValid()){

            $this->get('ad.manager')->save($ad);
            $this->addFlash('success','Zapisano poprawnie.');

            return $this->redirect($this->generateUrl('backoffice_ads_list',
                                        array('idPosition'=>$ad->getPosition()->getId(),
                                              'idOffice'=>$idOffice)));
        }

        return $this->render('AppAdBundle:Ad:edit.html.twig', array(
                'form'=>$form->createView(),
                'ad'=>$ad,
            ));
    }
    /**
     * List ads
     * @return Response
    */
    public function listAction()
    {
        $request = $this->getRequest();
        $positions = $this->get('adposition.manager')
                          ->getAll($request->query->has('idOffice'));

        $isOfficePosition = false;

        if($request->query->has('idOffice')){
            $isOfficePosition = true;
        }
        if($request->query->has('idPosition')){
            $idPosition = $request->query->get('idPosition');
        }else {
            $position = $this->get('adposition.manager')
                             ->getLast($isOfficePosition);

            $idPosition = $position->getId();
        }
        return $this->render('AppAdBundle:Ad:list.html.twig', array(
            'ads'=>$this->get('ad.manager')->getAll(),
            'positions'=>$positions,
            'idPosition'=>$idPosition,
            ));
    }
    /**
     *
     * List of ads from the selected category
     *
     * @param Request $request
     * @return Response
     */
    public function listByCategoryAction(Request $request)
    {
        $category = $this->get('ad.manager')
                         ->findCategoryById($request->get('idCategory'));

        $ads = $this->get('ad.manager')
                    ->getAllByCategory($request);

        return $this->render('AppAdBundle:Ad:listByCategory.html.twig', array(
            'items'=>$ads,
            'category'=>$category,
            'categories'=>$this->get('ad.manager')->getAllCategories(true)));
    }

    /**
     *
     * Delete ad
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function deleteAction($id)
    {
        $this->get('ad.manager')
             ->remove($id);

        $this->addFlash('success','Usunięto poprawnie.');

        return $this->redirect($this->getRequest()->headers->get('referer'));
    }
    /**
     *
     * Change ad ordering on list
     *
     * @param int $id
     * @param string $direction
     * @return RedirectResponse
     */
    public function changeOrderingAction($id,$direction)
    {
        $this->get('ad.manager')
             ->changeOrdering($id,$direction);

        $this->addFlash('success','Pozycja została zmieniona.');

        return $this->redirect($this->getRequest()->headers->get('referer'));

    }
    /**
     * Redirect to ad url
     *
     * @return RedirectResponse
    */
    public function openUrlAction(){
        $ad = $this->get('ad.manager')
                   ->incrementClicks($this->getRequest()->get('id'));

        if(null !== $ad->getOffer()){
            $offer = $this->get('offer.manager')->findById($ad->getOffer());

            if(!empty($offer->getSubdomain())){
                return $this->redirectToRoute('frontend_offer_subdomain', [
                    'id' => $offer->getId(),
                    'offerName' => UrlHelper::prepare($offer->getName()),
                    'subdomain' => $offer->getSubdomain()
                ]);
            }

            return $this->redirectToRoute('frontend_offer_show', [
                'id' => $offer->getId(),
                'offerName' => UrlHelper::prepare($offer->getName())
            ]);
        }

        return $this->redirect($ad->getUrl());
    }
}
