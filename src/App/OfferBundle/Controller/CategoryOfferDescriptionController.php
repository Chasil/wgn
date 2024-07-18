<?php

namespace App\OfferBundle\Controller;

use App\ArticleBundle\Form\CategoryArticleDescriptionType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\OfferBundle\Form\CategoryOfferDescriptionType;
use App\OfferBundle\Entity\CategoryOfferDescription;
use \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CategoryOfferDescriptionController extends Controller
{
    public function addAction()
    {
        $description = new CategoryOfferDescription();
        $form = $this->createForm(new CategoryOfferDescriptionType(), $description);

        $form->handleRequest($this->get('request'));

        if($form->isSubmitted() && $form->isValid()){
            $this->get('offer_category_description.manager')->save($description);

            return $this->redirectToRoute('backoffice_offers_category_description_list');
        }
        return $this->render('AppOfferBundle:CategoryOfferDescription:form.html.twig',[
            'offer'=>$description,
            'form'=>$form->createView(),
        ]);
    }

    public function editAction()
    {

        $request = $this->get('request');
        $description = $this->get('offer_category_description.manager')
                            ->getById($request->get('id'));


        $form = $this->createForm(new CategoryArticleDescriptionType(), $description);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->get('offer_category_description.manager')->save($description);

            return $this->redirectToRoute('backoffice_offers_category_description_list');
        }
        return $this->render('AppOfferBundle:CategoryOfferDescription:form.html.twig',[
            'offer'=>$description,
            'form'=>$form->createView(),
        ]);
    }

    public function listAction()
    {

        return $this->render('AppOfferBundle:CategoryOfferDescription:list.html.twig',[
            'descriptions'=>$this->get('offer_category_description.manager')->getWithPagination()
        ]);
    }

    public function deleteAction()
    {
        $description = $this->get('offer_category_description.manager')
                            ->getById($this->get('request')->get('id'));

        if(!is_object($description)){
            throw new NotFoundHttpException();
        }

        $this->get('offer_category_description.manager')
                            ->delete($description);

        return $this->redirect($this->get('request')->headers->get('referer'));
    }

}
