<?php
/**
 * This file is part of the AppAppBundle package.
 *
 */
namespace App\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\AppBundle\Entity\BlockElement;
use App\AppBundle\Form\BlockType;

/**
 * Class BlockController
 *
 * @author wojciech przygoda
 */
class BlockController extends Controller
{
    /**
     * Add block
     *
    */
    public function addAction(){
        $request = $this->getRequest();

        if($request->query->has('service')){
            $service = $request->query->get('service');
            if(!$this->has('block.'.$service)){
                throw $this->createNotFoundException('Nie znaleziono bloku');
            }
        }else {
            $service = 'article_article_list';
        }

        $blockElement = new BlockElement();
        $blockElement->setService($service);
        $form = $this->createForm(new BlockType($this->getDoctrine()->getManager()),$blockElement);
        $form->handleRequest($this->getRequest());
        if($form->isSubmitted() && $form->isValid()){
            $blockManager = $this->get('block.manager');
            $blockManager->save($blockElement);
            $this->addFlash('success','Zapisano poprawnie.');

            return $this->redirect($this->generateUrl('backoffice_position_elements_list'));
        }
        return $this->render('AppAppBundle:Block:add.html.twig', array(
            'form'=>$form->createView(),
            'type'=>$this->get('block.'.$service)->getName()
            ));
    }
    /**
     * Edit block
     *
    */
    public function editAction(){
        $request = $this->getRequest();
        $blockManager = $this->get('block.manager');
        $blockElement = $blockManager->findById($request->query->get('id'));

        if($request->query->has('service')){
            $service = $request->query->get('service');

            if(!$this->has('block.'.$service)){
                throw $this->createNotFoundException('Nie znaleziono bloku');
            }

            $blockElement->setService($service);
        }
        $form = $this->createForm(new BlockType($this->getDoctrine()->getManager()),$blockElement);
        $form->handleRequest($this->getRequest());

        if($form->isSubmitted() && $form->isValid()){
            $blockManager->save($blockElement);
            $this->addFlash('success','Zapisano poprawnie.');

            return $this->redirect($this->generateUrl('backoffice_position_elements_list'));
        }
        return $this->render('AppAppBundle:Block:add.html.twig', array(
            'form'=>$form->createView(),
            'type'=>$this->get('block.'.$blockElement->getService())->getName()
            ));
    }
    /**
     * Delete block
     *
    */
    public function deleteAction(){
        $request = $this->getRequest();
        $blockManager = $this->get('block.manager');
        $blockManager->removeById($request->query->get('id'));
        $this->addFlash('success','Usunięto poprawnie.');

        return $this->redirect($this->getRequest()->headers->get('referer'));
    }
    /**
     * Change block publication state
     *
    */
    public function changePublishAction()
    {
        $id = $this->getRequest()->query->get('id');
        $publish = $this->getRequest()->query->get('publish');

        $blockManager = $this->get('block.manager');
        $blockManager->changePublish($id,$publish );

        $this->addFlash('success','Zapisano poprawnie.');

        return $this->redirect($this->getRequest()->headers->get('referer'));

    }
}
