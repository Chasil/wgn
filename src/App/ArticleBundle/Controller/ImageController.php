<?php
/**
 * This file is part of the AppArticeBundle package.
 *
 */
namespace App\ArticleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\ArticleBundle\Entity\ArticleImage;

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

        $articleManager = $this->get('article.manager');
        $article = $articleManager->findById($this->getRequest()->get('id'));

        $image = new ArticleImage();
        $image->setName($files[0]);
        $image->setArticle($article);
        $articleManager->saveImage($image);
        $imageUrl = $this->get('liip_imagine.cache.manager')
                         ->getBrowserPath('/uploads/articles/'.$article->getId().'/'.$image->getName(), 'article_thumbnails', array());
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
        $articleManager = $this->get('article.manager');
        $articleManager->removeImage($this->getRequest()->get('id'));
        return new JsonResponse(array('success'=>true));
    }
    /**
     * Sorting images
     *
    */
    public function sortAction()
    {
        $ids = $this->getRequest()->get('ids');
        $articleManager = $this->get('article.manager');

        foreach($ids as $key=>$value){
          $articleManager->updateImageOrdering($value, $key+1);
        }

        return new JsonResponse(array('success'=>true));
    }
}
