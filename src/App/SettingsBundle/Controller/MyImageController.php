<?php
namespace App\SettingsBundle\Controller;

use App\SettingsBundle\Entity\MyImage;
use App\SettingsBundle\Form\MyImageType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use App\SearchLinkBundle\Form\CategoryType;

/**
 * Class MyImageController
 */
class MyImageController extends Controller
{

    /**
     * Edit Link category
     *
     * @param Request $request request
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request)
    {
    	$manager = $this->get('myimage.manager');
    	$myImage = $manager->get($request->get('id'));
	
		$form = $this->createForm( new MyImageType(), $myImage );
		$form->handleRequest( $request );
	
		if( $form->isValid() )
		{
			$manager->save( $myImage );
			$this->addFlash('success','Zapisano poprawnie.');
		}
		    	
        return $this->render('AppSettingsBundle:MyImage:edit.html.twig', array(
                'form'=>$form->createView(),
				'image'=>$myImage,
            ));
    }
    public function addAction(Request $request)
    {
    	$manager = $this->get('myimage.manager');
        $image = new MyImage();

		$form = $this->createForm( new MyImageType(), $image );
		$form->handleRequest( $request );

		if( $form->isValid() && $form->isSubmitted())
		{

			$manager->save( $image );
			$this->addFlash('success','Zapisano poprawnie.');
			return $this->redirect($this->generateUrl('app_settings_list_my_image'));
		}

        return $this->render('AppSettingsBundle:MyImage:add.html.twig', array(
                'form'=>$form->createView(),
            ));
    }
    public function listAction(Request $request)
    {
    	$manager = $this->get('myimage.manager');



        return $this->render('AppSettingsBundle:MyImage:list.html.twig', array(
				'images'=>$manager->getAll(),
            ));
    }
    public function deleteAction(Request $request)
    {
        $id = $request->get('id');

        $manager = $this->get('myimage.manager');
        $manager->remove($id);

        $this->addFlash('success','Pozycja została zmieniona.');

        return $this->redirect($request->headers->get('referer'));
    }
    public function changeEnabledAction(Request $request)
    {
        $id = $request->get('id');
        $enabled = $request->get('publish');

        $manager = $this->get('myimage.manager');
        $manager->changeEnabled($id,$enabled);

        $this->addFlash('success','Pozycja została zmieniona.');

        return $this->redirect($request->headers->get('referer'));

    }
}
