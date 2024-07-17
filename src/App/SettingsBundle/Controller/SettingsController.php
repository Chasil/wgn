<?php
/**
 * This file is part of the AppSettingsBundle package.
 *
 */
namespace App\SettingsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\SettingsBundle\Form\MetaTagsType;
use App\SettingsBundle\Form\CompanyAddressType;
use App\SettingsBundle\Form\ColorsType;

/**
 * Class SettingsController
 *
 * @author wojciech przygoda
 */
class SettingsController extends Controller
{
    /**
     * Edit meta tags
     *
    */
    public function editMetaTagsAction()
    {
        $settingsManager = $this->get('settings.manager');
        $settings = $settingsManager->get(false);

        $form = $this->createForm(new MetaTagsType(),$settings);
        $form->handleRequest($this->getRequest());

        if($form->isValid()){
            $settingsManager->save($settings);

            $this->addFlash('success','Zapisano poprawnie.');
        }

        return $this->render('AppSettingsBundle:Settings:editMetaTags.html.twig',
            ['form'=>$form->createView()]);
    }
    /**
     * Edit company address
     *
    */
    public function editCompanyAddressAction()
    {
        $settingsManager = $this->get('settings.manager');
        $settings = $settingsManager->get();

        $form = $this->createForm(new CompanyAddressType(),$settings);
        $form->handleRequest($this->getRequest());

        if($form->isValid()){
            $settingsManager->save($settings);

            $this->addFlash('success','Zapisano poprawnie.');
        }

        return $this->render('AppSettingsBundle:Settings:editCompanyAddress.html.twig',
            ['form'=>$form->createView()]);
    }
    public function editColorsAction()
    {
        $settingsManager = $this->get('settings.manager');
        $settings = $settingsManager->get();

        $form = $this->createForm(new ColorsType(),$settings);
        $form->handleRequest($this->getRequest());

        if($form->isValid()){
            $settingsManager->save($settings);

            $this->addFlash('success','Zapisano poprawnie.');
        }

        return $this->render('AppSettingsBundle:Settings:editColors.html.twig',
            ['form'=>$form->createView()]);
    }
}
