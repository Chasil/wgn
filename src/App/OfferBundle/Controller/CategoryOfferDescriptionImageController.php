<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Controller;

use App\OfferBundle\Entity\CategoryOfferDescriptionImage;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\OfferBundle\Entity\OfferImage;
/**
 * Class CategoryOfferDescriptionImageController
 *
 * @author wojciech przygoda
 */
class CategoryOfferDescriptionImageController extends Controller
{
    /**
     * Get CategoryOfferDescriptionImages Offer
     */
    public function getImagesOfferAction(int $id){
        $offerCategoryDescriptionManager = $this->get('offer_category_description.manager');
        return new JsonResponse($offerCategoryDescriptionManager->findAllOfferImages($id));
    }

    /**
     * Add image to temporary offer
     *
     */
    public function addToTmpAction() {

        $files = $this->getRequest()->files->get('images');

        $offerCategoryDescriptionManager = $this->get('offer_category_description.manager');
        $image = new CategoryOfferDescriptionImage();
        $image->setName($files[0]);
        $image->setTmpIdOffer($this->getRequest()->get('id'));
        $offerCategoryDescriptionManager->saveImage($image);
        $imageUrl = $this->get('liip_imagine.cache.manager')
            ->getBrowserPath('/uploads/categoryOffersDescription/'.$image->getTmpIdCategoryOfferDescription().'/'.$image->getName(), 'offer_thumbnails', array());
        return new JsonResponse(array('success'=>true,
            'id'=>$image->getId(),
            'tmpIdCategoryOfferDescription'=>$this->getRequest()->get('tmpIdCategoryOfferDescription'),
            'imageUrl'=>$imageUrl));
    }
    /**
     * Add image
     *
     */
    public function addAction() {
        $files = $this->getRequest()->files->get('images');

        $offerCategoryDescriptionManager = $this->get('offer_category_description.manager');
        $offerCategoryDescription = $offerCategoryDescriptionManager->findById($this->getRequest()->get('id'));
        $image = new CategoryOfferDescriptionImage();
        $image->setName($files[0]);
        $image->setCategoryOfferDescription($offerCategoryDescription);
        $offerCategoryDescriptionManager->saveImage($image);
        $imageUrl = $this->get('liip_imagine.cache.manager')
            ->getBrowserPath('/uploads/categoryOffersDescription/'.$image->getCategoryOfferDescription()->getId().'/'.$image->getName(), 'offer_thumbnails', array());
        return new JsonResponse(array('success'=>true,
            'id'=>$image->getId(),
            'tmpIdCategoryOfferDescription'=>$this->getRequest()->get('tmpIdCategoryOfferDescription'),
            'imageUrl'=>$imageUrl));
    }
    /**
     * Delete image
     *
     */
    public function deleteAction()
    {
        $offerCategoryDescriptionManager = $this->get('offer_category_description.manager');
        $offerCategoryDescriptionManager->removeImage($this->getRequest()->get('id'));
        return new JsonResponse(array('success'=>true));
    }
    /**
     * Sorting images
     *
     */
    public function sortAction()
    {
        $ids = $this->getRequest()->get('ids');
        $idCategoryOfferDescription = $this->getRequest()->get('idCategoryOfferDescription');
        $offerCategoryDescriptionManager = $this->get('offer_category_description.manager');

        $offerCategoryDescriptionManager->updateImagesOrdering($idCategoryOfferDescription,$ids);

        return new JsonResponse(array('success'=>true));
    }
}