<?php
/**
 * This file is part of the AppMenuBundle package.
 *
 */
namespace App\MenuBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class MenuController
 *
 * @author wojciech przygoda
 */
class MenuController extends Controller
{
    /**
     * List of menus
     *
    */
    public function listAction()
    {
        $menuManager = $this->get('menu.manager');
        return $this->render('AppMenuBundle:Menu:list.html.twig',array(
                             'menus'=>$menuManager->getAll()));
    }
}
