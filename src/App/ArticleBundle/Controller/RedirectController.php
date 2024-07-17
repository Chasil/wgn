<?php
/**
 * This file is part of the AppArticeBundle package.
 *
 */
namespace App\ArticleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class RedirectController
 *
 * @author wojciech przygoda
 */
class RedirectController extends Controller
{

    /**
     * Redirect old article
     *
     * @param int $legacyId artice legacy id
     * @return RedirectResponse
     */
    public function articleAction($legacyId){

        $article = $this->get('article.manager')->findByLegacyId($legacyId);

        return $this->redirect($this->get('article.manager')->generateRoute($article), 301);
    }
    /**
     * Redirect old news
     *
     * @return RedirectResponse
    */
    public function newsAction(){

        return $this->redirect($this->generateUrl('frontend_article_category_show',array('idCategory'=>77,'categoryName'=>'aktualnosci')), 301);
    }
    /**
     * Redirect old articles with tag
     *
     * @param string $tagName tag name
     * @return RedirectResponse
    */
    public function articlesByTagAction($tagName){
        $tagName = preg_replace('/\+/',' ',$tagName);
        $tag = $this->get('article.manager')->getTagByName($tagName);

        return $this->redirectToRoute('frontend_articles_show_by_tag',array('idTag'=>$tag->getId(),
                                                                            'tagName'=>$tag->getName()), 301);
    }
}
