<?php
/**
 * This file is part of the AppAdBundle package.
 *
 */
namespace App\AdBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\AdBundle\Entity\AdPosition;
use App\AdBundle\Form\AdPositionType;

/**
 * Class PositionController
 *
 * @author wojciech przygoda
 */

class PositionController extends Controller
{

    /**
     *
     * Add ad position
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function addAction(Request $request)
    {
        $position = new AdPosition();
        $form = $this->createForm(new AdPositionType(), $position);
        $form->handleRequest($request);

        if($form->isValid()){
            $adPositionManager = $this->get('adposition.manager');
            $adPositionManager->save($position);

            $this->addFlash('success','Zapisano poprawnie.');
            return new RedirectResponse($this->generateUrl('backoffice_ad_positions_list',
                                        array()));
        }

        return $this->render('AppAdBundle:Position:add.html.twig', array(
                'form'=>$form->createView(),
            ));
    }

    /**
     *
     * Edit ad position
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function editAction(Request $request)
    {
        $adPositionManager = $this->get('adposition.manager');

        $position = $adPositionManager->findById($request->get('id'), false);

        $form = $this->createForm(new AdPositionType(), $position);
        $form->handleRequest($request);

        if($form->isValid()){
            $adPositionManager->save($position);
            $this->addFlash('success','Zapisano poprawnie.');
            return new RedirectResponse($this->generateUrl('backoffice_ad_positions_list',
                                        array()));
        }

        return $this->render('AppAdBundle:Position:edit.html.twig', array(
                'form'=>$form->createView(),
                'position'=>$position,
            ));
    }
    /**
     * List ad positions
     *
     * @return Response
    */
    public function listAction()
    {
        $adPositionManager = $this->get('adposition.manager');

        return $this->render('AppAdBundle:Position:list.html.twig', array(
            'positions'=>$adPositionManager->getAll()));
    }

    /**
     *
     * Delete ad position
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function deleteAction($id)
    {
        $em = $this->get('doctrine')->getManager();
        $repo = $em->getRepository('AppAdBundle:AdPosition');
        $position = $repo->findOneById($id);
        if($position->hasAds()){
            $this->addFlash('error','Nie można usunąć kategorii ponieważ posiada banery.');
        }else {
            $em->remove($position);
            $em->flush();
            $this->addFlash('success','Usunięto poprawnie.');
        }

        return new RedirectResponse($this->getRequest()->headers->get('referer'));
    }

}
