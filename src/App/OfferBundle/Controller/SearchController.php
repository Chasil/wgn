<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class SearchControlle
 *
 * @author wojciech przygoda
 */
class SearchController extends Controller
{
    /**
     * Show embed search page
     *
    */
    public function embedAction() {
        $searchManager = $this->get('search.manager');
        $request = $this->getRequest();
        $sizes = array('s','m','l','xl');
        $size = $request->query->get('s');

        if(!in_array($size,$sizes)){
            $size = 's';
        }
        return $this->render('AppOfferBundle:Search:embed.html.twig',array(
            'size'=>$size,
            'form'=>$searchManager->getFormView()
            ));
    }
    /**
     * Show partner page
     *
    */
    public function partnerAction(){
        return $this->render('AppOfferBundle:Search:partner.html.twig',array(
            ));
    }
    /**
     * List of locations for autocomplete
     *
    */
    public function locationAutocompleteAction(){
        $searchManager = $this->get('search.manager');
        $request = $this->getRequest();
        $suggestions = $searchManager->locationIndexAutocomplete($request->get('q'));

        return new JsonResponse(array('success'=>true,'results'=>$suggestions));
    }
    /**
     * List of regions for autocomplete
     *
    */
    public function regionAutocompleteAction(){
        $searchManager = $this->get('search.manager');
        $request = $this->getRequest();
        $suggestions = $searchManager->regionIndexAutocomplete($request->get('q'));
        return new JsonResponse(array('success'=>true,'results'=>$suggestions));
    }
    /**
     * List of cities for autocomplete
     *
    */
    public function cityAutocompleteAction(){
        $searchManager = $this->get('search.manager');
        $request = $this->getRequest();
        $suggestions = $searchManager->cityIndexAutocomplete($request->get('q'));
        return new JsonResponse(array('success'=>true,'results'=>$suggestions));
    }
    /**
     * List of streets for autocomplete
     *
    */
    public function streetAutocompleteAction(){
        $searchManager = $this->get('search.manager');
        $request = $this->getRequest();
        $suggestions = $searchManager->streetIndexAutocomplete($request->get('q'));
        return new JsonResponse(array('success'=>true,'results'=>$suggestions));
    }
    /**
     * List of offers types for autocomplete
     *
    */
    public function getTypesAction(){
        $searchManager = $this->get('search.manager');
        $request = $this->getRequest();
        $types = $searchManager->getTypes($request->get('category'));
        return new JsonResponse(array('success'=>true,'results'=>$types));
    }

    /**
     * List of category types for autocomplete
     *
     * @param int $idCategory category id
     * @return JsonResponse
     */
    public function getCategoryTypesAction($idCategory) {

        return new JsonResponse(array('success'=>true,
                                      'results'=>$this->get('search.manager')
                                                   ->getTypes($idCategory),
                               ));
    }
    /**
     * Get search advanced form
     *
    */
    public function getAdvancedFormAction(){
        $request = $this->getRequest();
        $search = $request->get('search');
        $searchManager = $this->get('search.manager');

        $views = array(1=>'flat',
                       2=>'house',
                       3=>'plot',
                       4=>'local',
                       5=>'commercial',
                       6=>'garage');

        if(isset($views[$search['category']])){
            $view = $views[$search['category']];
        }else {
            $view = 'flat';
        }


        return $this->render('AppOfferBundle:Search:'.$view.'.html.twig',array(
            'form'=>$searchManager->getFormView(),
       ));
    }
    /**
     * List of available countries
     *
    */
    public function availableCountriesAction(){
        $request = $this->get('request');
        $countries = $this->get('search.manager')
                          ->getAvailableCountries($request->get('idCategory'),
                                                  $request->get('idTransactionType'));
        return new JsonResponse(array('success'=>true,'data'=>$countries));
    }
}
