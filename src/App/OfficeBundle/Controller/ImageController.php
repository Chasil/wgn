<?php
/**
 * This file is part of the AppOfficeBundle package.
 *
 */
namespace App\OfficeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\OfficeBundle\Entity\OfficeImage;
/**
 * Class ImageController
 *
 * @author wojciech przygoda
 */
class ImageController extends Controller
{
    /**
     * Add image
     *
    */
    public function addAction() {

        $files = $this->getRequest()->files->get('images');

        $officeManager = $this->get('office.manager');
        $office = $officeManager->findById($this->getRequest()->get('id'));

        $image = new OfficeImage();

        $this->denyAccessUnlessGranted('manage', $image);

        $image->setName($files[0]);
        $image->setOffice($office);
        $officeManager->saveImage($image);
        $imageUrl = $this->get('liip_imagine.cache.manager')
                         ->getBrowserPath('/uploads/offices/'.$office->getId().'/'.$image->getName(), 'article_thumbnails', array());
        return new JsonResponse(array('success'=>true,
                                      'id'=>$image->getId(),
                                      'tmpId'=>$this->getRequest()->get('tmpId'),
                                      'imageUrl'=>$imageUrl));
    }
    /**
     * Delete image
     *
    */
    public function deleteAction()
    {
        $officeManager = $this->get('office.manager');
        $this->denyAccessUnlessGranted('manage', new OfficeImage());
        $officeManager->removeImage($this->getRequest()->get('id'));
        return new JsonResponse(array('success'=>true));
    }
    /**
     * Sorting images
     *
    */
    public function sortAction()
    {
        $ids = $this->getRequest()->get('ids');
        $articleManager = $this->get('office.manager');
        $this->denyAccessUnlessGranted('manage', new OfficeImage());
        foreach($ids as $key=>$value){
            
          $articleManager->updateImageOrdering($value, $key+1);
        }

        return new JsonResponse(array('success'=>true));
    }
}
