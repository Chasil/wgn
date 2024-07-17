<?php
/**
 * This file is part of the AppAppBundle package.
 *
 */
namespace App\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class BlockPositionController
 *
 * @author wojciech przygoda
 */
class BlockPositionController extends Controller
{
    /**
     * List blocks
     *
    */
    public function listAction()
    {
        $request = $this->getRequest();
        $blockManager = $this->get('block.manager');
        $places = $blockManager->getPlaces();

        $elements = $blockManager->getAllElements($request->get('place',$places[0]->getUniqueKey()));

        return $this->render('AppAppBundle:BlockPosition:list.html.twig', array(
            'items'=>$elements,
            'places'=>$places ));
    }
    /**
     * Change Block position on list
     *
    */
    public function changeOrderingAction()
    {
        $id = $this->getRequest()->get('id');
        $direction = $this->getRequest()->get('direction');

        $blockManager = $this->get('block.manager');
        $blockManager->changeOrdering($id,$direction);

        $this->addFlash('success','Pozycja została zmieniona.');

        return $this->redirect($this->getRequest()->headers->get('referer'));

    }
    /**
     * Change block position publication state
     *
    */
    public function changePublishAction()
    {
        $id = $this->getRequest()->get('id');
        $publish = $this->getRequest()->get('publish');

        $blockManager = $this->get('block.manager');
        $blockManager->changePublish($id,$publish );

        $this->addFlash('success','Pozycja została zmieniona.');

        return $this->redirect($this->getRequest()->headers->get('referer'));

    }
}
