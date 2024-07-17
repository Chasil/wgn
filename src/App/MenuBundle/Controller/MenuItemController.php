<?php
/**
 * This file is part of the AppMenuBundle package.
 *
 */
namespace App\MenuBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\MenuBundle\Form\MenuItemType;
use App\MenuBundle\Entity\MenuItem;
/**
 * Class MenuItemController
 *
 * @author wojciech przygoda
 */
class MenuItemController extends Controller
{
    /**
     * List of menu items
     *
    */
    public function listAction()
    {
        $menuManager = $this->get('menu.manager');
        $items = $menuManager->getByUniqueKey($this->getRequest()->get('key'));
        return $this->render('AppMenuBundle:MenuItem:list.html.twig',array('items'=>$items));
    }
    /**
     * Delete menu item
     *
    */
    public function deleteAction()
    {
        $request = $this->getRequest();
        $menuManager = $this->get('menu.manager');
        $menuManager->removeItem($request->get('id'));
        $this->addFlash('success','Usunięto poprawnie.');
        return $this->redirect($this->getRequest()->headers->get('referer'));
    }
    /**
     * Add menu item
     *
    */
    public function addAction()
    {
        $menuManager = $this->get('menu.manager');
        $key = $this->getRequest()->get('key');
        $type = $this->getRequest()->get('type',MenuItem::TYPE_ARTICLE);
        $menu = $menuManager->getMenuByUniqueKey($key);
        if(!$menu){
             throw $this->createNotFoundException('Menu with key '.$key.' not exist.');
        }
        $menuItem = new MenuItem();
        $menuItem->setType($type);
        $menuItem->setMenu($menu);
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(new MenuItemType($em,$menu),$menuItem);
        $form->handleRequest($this->getRequest());

        if($form->isSubmitted()&& $form->isValid()){
            $menuManager->saveItem($menuItem);
            $this->addFlash('success','Zapisano poprawnie.');
            return $this->redirect($this->generateUrl('backoffice_menu_items_list',
                                array('key'=>$menu->getUniqueKey())));
        }

        return $this->render('AppMenuBundle:MenuItem:add.html.twig',array(
            'form'=>$form->createView(),
            'menuItem'=>$menuItem));
    }
    /**
     * Edit menu item
     *
    */
    public function editAction()
    {
        $menuManager = $this->get('menu.manager');
        $menuItem = $menuManager->findItemById($this->getRequest()->get('id'));

        $type = $this->getRequest()->get('type');
        if($type){
            $menuItem->setType($type);
        }
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(new MenuItemType($em,$menuItem->getMenu()),$menuItem);
        $form->handleRequest($this->getRequest());

        if($form->isSubmitted()&& $form->isValid()){
            $menuManager->saveItem($menuItem);
            $this->addFlash('success','Zapisano poprawnie.');
            return $this->redirect($this->generateUrl('backoffice_menu_items_list',
                                array('key'=>$menuItem->getMenu()->getUniqueKey())));
        }

        return $this->render('AppMenuBundle:MenuItem:add.html.twig',array(
            'form'=>$form->createView(),
            'menuItem'=>$menuItem));
    }
    /**
     * Change article position on list
     *
    */
    public function changePositionAction()
    {
        $request = $this->getRequest();
        $menuManager = $this->get('menu.manager');
        $menuManager->changeItemPosition($request->get('id'),
                                         $request->get('direction'));
        $this->addFlash('success','Zapisano poprawnie.');
        return $this->redirect($this->getRequest()->headers->get('referer'));
    }
    /**
     * Change article publication state
     *
    */
    public function changePublishAction()
    {
        $id = $this->getRequest()->get('id');
        $publish = $this->getRequest()->get('publish');

        $menuManager = $this->get('menu.manager');
        $menuManager->changeItemPublish($id,$publish );

        $this->addFlash('success','Zapisano poprawnie.');

        return $this->redirect($this->getRequest()->headers->get('referer'));

    }
}
