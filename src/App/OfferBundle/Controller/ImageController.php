<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\OfferBundle\Entity\OfferImage;
/**
 * Class ImageController
 *
 * @author wojciech przygoda
 */
class ImageController extends Controller
{
    /**
     * Get Images Offer
     */
    public function getImagesOfferAction(int $id){
        $offerManager = $this->get('offer.manager');
        $imagineCacheManager = $this->get('liip_imagine.cache.manager');
        $offer = $offerManager->findById($id);
        $thumbType = 'offer_list';

        if($offer->getImportId() !='' or $offer->getLegacyId() !='') {
            $thumbType = 'offer_list_no_watermark';
        }
        $images = $offerManager->findAllOfferImages($id);
        $thumbs = [];
        foreach($images as $image) {
            $thumbs[] =  ['name' => $imagineCacheManager->getBrowserPath('/uploads/offers/'.$id.'/'. $image['name'], $thumbType)];

        }
        return new JsonResponse($thumbs);
    }

    /**
     * Add image to temporary offer
     *
    */
    public function addToTmpAction() {

        $files = $this->getRequest()->files->get('images');

        $offerManager = $this->get('offer.manager');
        $image = new OfferImage();
        $image->setName($files[0]);
        $image->setTmpIdOffer($this->getRequest()->get('id'));
        $offerManager->saveImage($image);
        $imageUrl = $this->get('liip_imagine.cache.manager')
                         ->getBrowserPath('/uploads/offers/'.$image->getTmpIdOffer().'/'.$image->getName(), 'offer_thumbnails', array());
        return new JsonResponse(array('success'=>true,
                                      'id'=>$image->getId(),
                                      'tmpId'=>$this->getRequest()->get('tmpId'),
                                      'imageUrl'=>$imageUrl));
    }
    /**
     * Add image
     *
    */
    public function addAction() {
        $files = $this->getRequest()->files->get('images');

        $offerManager = $this->get('offer.manager');
        $offer = $offerManager->findById($this->getRequest()->get('id'));
        $image = new OfferImage();
        $image->setName($files[0]);
        $image->setOffer($offer);
        $offerManager->saveImage($image);
        $imageUrl = $this->get('liip_imagine.cache.manager')
                         ->getBrowserPath('/uploads/offers/'.$image->getOffer()->getId().'/'.$image->getName(), 'offer_thumbnails', array());
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
        $offerManager = $this->get('offer.manager');
        $offerManager->removeImage($this->getRequest()->get('id'));
        return new JsonResponse(array('success'=>true));
    }
    /**
     * Sorting images
     *
    */
    public function sortAction()
    {
        $ids = $this->getRequest()->get('ids');
        $idOffer = $this->getRequest()->get('idOffer');
        $offerManager = $this->get('offer.manager');

        $offerManager->updateImagesOrdering($idOffer,$ids);

        return new JsonResponse(array('success'=>true));
    }
}
