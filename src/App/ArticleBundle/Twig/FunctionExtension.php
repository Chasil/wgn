<?php
/**
 * This file is part of the AppArticeBundle package.
 *
 */
namespace App\ArticleBundle\Twig;

use App\AppBundle\Component\SubdomainHelper;
use App\ArticleBundle\Entity\Article;
use App\ArticleBundle\Entity\ArticleCategory;
use App\ArticleBundle\Entity\Blog;
use App\ArticleBundle\Security\ArticleVoter;
use \Twig_Filter_Function;
use \Twig_Filter_Method;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use App\AdBundle\Entity\AdPosition;
/**
 * Class FunctionExtension
 *
 * @author wojciech przygoda
 */
class FunctionExtension extends \Twig_Extension
{
    /**
     *
     * @var Container services container
     */

    private $services;
    private $randomOffers;

    /**
     * Constructor
     *
     * @param Container $container services container
     */
    function __construct(Container $container) {

        $this->services = $container;
    }
    /**
     * Return the functions registered as twig extensions
     *
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('file_exists',[$this,'file_exists'],['is_safe' => ['html']]),
            new \Twig_SimpleFunction('contentWithRandomOffers',[$this,'contentWithRandomOffers'],['is_safe' => ['html']]),
            new \Twig_SimpleFunction('articleCategoryUrl',[$this,'articleCategoryUrl'],['is_safe' => ['html']]),
            ];

    }

    public function fileExists($filename){
        return file_exists($filename);
    }

	public function contentWithRandomOffers(Blog $blog, $content)
    {
        $doc = new \DOMDocument('1.0', 'UTF-8');
        @$doc->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
        $doc->encoding = 'utf-8';

        $header1 = $doc->getElementsByTagName("h2")->item(0);
        $header2 = $doc->getElementsByTagName("h2")->item(1);
        $header3 = $doc->getElementsByTagName("h2")->item(2);

        if(count($this->randomOffers)==0)
        {
            if($blog->isWithoutLocation()) {
                $params = SubdomainHelper::getParamsFromSubdomain($blog->getSubdomain());
                $transaction = SubdomainHelper::getTransactionByKey($params[0]);
                $category = SubdomainHelper::getCategoryByKey($params[1]);

                $this->randomOffers = $this->services
                                           ->get('offer.manager')
                                           ->findRandomOffersByParams($transaction['id'],$category['id'],30);
            } else {
                $this->randomOffers = $this->services
                                           ->get('offer.manager')
                                           ->findRandomOfferFromSubdomain($blog->getSubdomain(),30);
            }
        }


        if (!is_null($header1)) {
            $html1 = $this->services->get('templating')->render(
                'AppArticleBundle:_Partials:randomOffers.html.twig',[
                    'offers'=>array_slice($this->randomOffers,0,10),
                    'blog'=>$blog
            ]);

            if(!empty($html1))
            {
                $this->addHtml($doc, $header1, $html1);
            }

        }
        if (!is_null($header2)) {
            $html2 = $this->services->get('templating')->render(
                'AppArticleBundle:_Partials:randomOffers.html.twig',[
                'offers'=>array_slice($this->randomOffers,10,10),
                'blog'=>$blog
            ]);

            if(!empty($html2))
            {
                $this->addHtml($doc, $header2, $html2);
            }
        }
        if (!is_null($header3)) {
            $html3 = $this->services->get('templating')->render(
                'AppArticleBundle:_Partials:randomOffers.html.twig',[
                'offers'=>array_slice($this->randomOffers,20,10),
                'blog'=>$blog
            ]);

            if(!empty($html3))
            {
                $this->addHtml($doc, $header3, $html3);
            }
        }

        return preg_replace('~<(?:!DOCTYPE|/?(?:html|body))[^>]*>\s*~i', '', $doc->saveHTML());
    }

    private function addHtml($doc, $element, $html)
    {
        $box = $doc->createElement('div');
        $box->setAttribute('class','offer');
        $tpl = new \DOMDocument;
        $tpl->loadHtml(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        foreach ($tpl->getElementsByTagName('body')->item(0)->childNodes as $node) {
            $node = $doc->importNode($node, true);
            $box->appendChild($node);
        }
        $element->parentNode->insertBefore($box, $element);
    }

    public function articleCategoryUrl(ArticleCategory $category)
    {
        $router = $this->services->get('router');
        $request = $this->services->get('request');
        $page = $request->get('page');

        $categoryName = $category->getName();
        $categoryName = str_replace(' ', '-', $categoryName);

        $parameters = [
            'idCategory' => $category->getId(),
            'categoryName' => $categoryName,
        ];

        if($page && $page > 1)
        {
            $parameters['page'] = $page;
        }

        return $router->generate(
            'frontend_article_category_show',
            $parameters
        );
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return 'page_function_extension';
    }
}

