<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class ObservedController
 *
 * @author wojciech przygoda
 */
class ObservedController extends Controller
{
    /**
     * Add offer to observed
     *
    */
    public function addAction() {
        $request = $this->getRequest();
        $observedManager = $this->get('observed.manager');
        $observedManager->add($request->get('id'));
        return new JsonResponse(array('success'=>true));
    }
    /**
     * Delete offer form observed
     *
    */
    public function removeAction() {
        $request = $this->getRequest();
        $observedManager = $this->get('observed.manager');

        $observedManager->removeByHash($request->get('id'));
         return new JsonResponse(array('success'=>true));
    }
    /**
     * Delete selected offers form observed
     *
    */
    public function multiRemoveAction() {
        $request = $this->getRequest();
        $observedManager = $this->get('observed.manager');
        $ids = $request->get('offer');

        if(count($ids)==0){
             $this->addFlash('error','Nie wybrano żadnej oferty.');
             return $this->redirect($this->getRequest()->headers->get('referer'));
        }

        foreach($ids as $id){
            $observedManager->removeByHash($id);
        }
        return $this->redirect($this->getRequest()->headers->get('referer'));
    }
    /**
     * List of offers
     *
    */
    public function listAction() {
        $request = $this->getRequest();
        $sizes = array('s','m','l');
        $size = $request->query->get('s');

        if(!in_array($size,$sizes)){
            $size = 's';
        }
        return $this->render('AppOfferBundle:Search:embed.html.twig',array(
            'size'=>$size,
            ));
    }
}
