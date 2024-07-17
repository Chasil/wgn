<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Services;

use App\AppBundle\Component\SubdomainHelper;
use App\OfferBundle\Entity\Category;
use App\OfferBundle\Entity\LocationAutocomplete;
use App\OfferBundle\Entity\SearchStatistics;
use App\OfferBundle\Entity\TransactionType;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use App\OfferBundle\Form\SearchType;
use App\OfferBundle\Form\SearchMobileType;
use App\OfferBundle\Model\Search;
use App\OfferBundle\Model\SearchMobile;
use App\OfferBundle\Entity\Country;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class SearchManager
 *
 * @author wojciech przygoda
 */
class SearchManager {
    /**
     *
     * @var Container services container
     */
    private $services;
    /**
     *
     * @var SearchFormType search form
     */
    private $searchForm;
    /**
     *
     * @var bool is subscription form
     */
    private $isSubscriptionForm;
    /**
     *
     * @var SearchMobileFormType search form
     */
    private $searchMobileForm;
    /**
     *
     * @var Search search parameters
     */
    private $searchQuery;
    /**
     *
     * @var SearchMobile search parameters
     */
    private $searchQueryMobile;
    /**
     *
     * @var bool is mobile query
     */
    private $isMobileQuery;

    /**
     *
     * @var array permiterd order columns
     */
    protected static $permittedOrderColumns = array('o.modifyDate',
                                           'o.priceDef',
                                           'o.priceDefm2',
                                           'o.squere');

    /**
     *
     * @var array permitted limit per page
     */
     protected static $permittedLimit = array(20,100);
     protected $transactionTypes = [
            'sprzedaz'=>1,
            'kupno'=>2,
            'wynajem'=>3,
            'najem'=>4
    ];
    protected $propertyTypes = [
            'mieszkanie'=>1,
            'dom'=>2,
            'dzialka'=>3,
            'lokal'=>4,
            'komercyjne'=>5,
            'garaz'=>6
    ];
    private $location;

    /**
      * Constructor
      *
      * @param Container $container services container
      */
    function __construct(Container $container) {
        $this->services = $container;
        $this->initSearch();
        $this->createForm();
        $this->createMobileForm();

        if(!$this->services->has('request')){
           return;
        }

        $request = $this->services->get('request');


        $search = $request->query->get('search');


        $subdomain = $this->getCurrentSubdomain();

        $forbiddenSubdomains = ['archiwum',':'];

        if(!empty($subdomain)
            && !in_array($subdomain, $forbiddenSubdomains)
            && !preg_match('/^(([[:alnum:]]*-[[:alnum:]]*-[[:alnum:]-]*)*)$/', $subdomain)
        ){

            $office = $this->services->get('office.manager')->findBySubdomain($subdomain);
            $search['office'] = $office->getId();
        }

        if($request->get('_route')=='frontend_offer_search_url_with_location' ||
           $request->get('_route')=='frontend_offer_search_url_agent_with_location'
        ){
            $locationKey = $request->get('locationKey');
            $categoryKey = $request->get('type');
            $transactionKey = $request->get('transaction');

            $this->locationStats($locationKey,$categoryKey, $transactionKey);

        }

        if($request->get('_route')=='frontend_offer_search_subdomain')
        {
            $subdomainParams = SubdomainHelper::getParamsFromSubdomain($subdomain);
            $this->locationStats($subdomainParams[2],$subdomainParams[1], $subdomainParams[0]);

        }

        if($this->location) {
            $search['locationIndexLike'] = $this->location->getName();
        }

        $user = $request->get('user');

        if(!empty($user)){
            $search['user'] = $user;
        }

        if($request->get('_route')=='frontend_offer_search_url'
                || $request->get('_route')=='frontend_offer_search_url_with_location'
                || $request->get('_route')=='frontend_offer_search_url_agent'
                || $request->get('_route')=='frontend_offer_search_url_agent_with_location'
        ){

            $transactionKey = $request->get('transaction');
            $propertyType = $request->get('type');

            $search['transactionType'] = (isset($this->transactionTypes[$transactionKey])) ? $this->transactionTypes[$transactionKey] : $search['transactionType'];
            $search['category'] = (isset($this->propertyTypes[$propertyType])) ? $this->propertyTypes[$propertyType] : $search['category'];
        }elseif($request->get('_route')== 'frontend_offer_search_subdomain')
        {
            $subdomainParams = SubdomainHelper::getParamsFromSubdomain($subdomain);

            $transactionKey = SubdomainHelper::getTransactionByKey($subdomainParams[0]);
            $categoryKey = SubdomainHelper::getCategoryByKey($subdomainParams[1]);

            $search['transactionType'] = $transactionKey['id'];
            $search['category'] = $categoryKey['id'];

        }elseif($request->get('_route')=='frontend_offer_list_office'
                || $request->get('_route')=='frontend_offer_list_agent'){

            unset($search['transactionType']);
            unset($search['category']);
            unset($search['currency']);
            unset($search['country']);
        }else {

            $search['transactionType'] = (isset( $search['transactionType'])) ? $search['transactionType'] : 1;
            $search['category'] = (isset( $search['category'])) ? $search['category'] : 1;
            $search['currency'] = (isset($search['currency'])) ? $search['currency'] : 1;
            $search['country'] = (isset($search['country'])) ? $search['country'] : 1;
        }

        $request->query->set('search',$search);

        $this->searchForm->handleRequest($request);
        $this->searchMobileForm->handleRequest($request);


    }
    protected function locationStats($locationKey,$categoryKey,$transactionKey)
    {
        if(!isset($this->propertyTypes[$categoryKey]) || !isset($this->transactionTypes[$transactionKey]))
        {
            throw new NotFoundHttpException();
        }

        $em = $this->services->get('doctrine.orm.entity_manager');
        $this->location = $this->findLocationByKey($locationKey);
        if($this->location){
            $category = $em->getReference(Category::class, $this->propertyTypes[$categoryKey]);
            $transaction = $em->getReference(TransactionType::class, $this->transactionTypes[$transactionKey]);
            $stats = $this->findSearchStats($category, $transaction, $this->location);

            if(!is_object($stats))
            {
                $stats = (new SearchStatistics())
                    ->setCategory($category)
                    ->setTransaction($transaction)
                    ->setLocationId($this->location->getId())
                ;
            }
            $stats->incrementDisplayCounter();
            $em->persist($stats);
            $em->flush();
        } else {
            throw new NotFoundHttpException();
        }

    }
    public function getTransactionTypeKey($key){
        return array_search($key, $this->transactionTypes);
    }
    public function getPropertyTypeKey($key){
        return array_search($key, $this->propertyTypes);
    }
    /**
     * Create form
     *
     * @return SearchFormType
     */
    public function createForm(){
        $router = $this->services->get('router');

        $action  = $router->generate('frontend_offer_list');

        $this->searchQuery = new Search();
        $this->searchForm = $this->services->get('form.factory')
                                 ->create(new SearchType($this),$this->searchQuery,array(
                                     'action'=>$action
                                 ));

        return $this->searchForm;
    }
     /**
     * Create mobileform
     *
     * @return SearchMobileFormType
     */
    public function createMobileForm(){
        $router = $this->services->get('router');
        $action  = $router->generate('frontend_offer_list');

        $this->searchQueryMobile = new SearchMobile();
        $this->searchMobileForm = $this->services->get('form.factory')
                                 ->create(new SearchMobileType($this),$this->searchQueryMobile,array(
                                     'action'=>$action
                                 ));

        return $this->searchMobileForm;
    }
    /**
     * Get types
     *
     * @param Category $category category
     * @return Type
     */
    public function getTypes($category){
        return $this->services->get('doctrine')->getManager()
                     ->getRepository('AppOfferBundle:Type')
                     ->findByCategoryId($category);
    }
    /**
     * Search on map
     *
     * @return array
     */
    public function searchOnMap(){
        $repo = $this->services->get('doctrine')->getManager()
                     ->getRepository('AppOfferBundle:Offer');
        $filters = $this->getFilters();
        $filters[] = array('field'=>'o.lat','condition'=>'!=','value'=>'');
        $filters[] = array('field'=>'o.lng','condition'=>'!=','value'=>'');

        $normalItems = $repo->search(0,
                               200,
                               $filters,
                               $this->getOrder());

        $promoItems = $repo->searchPromo(0,
                               200,
                               $filters,
                               $this->getOrder());

        return array_merge($promoItems,$normalItems);
    }
    /**
     * Search for looking for subscription
     * @param type $values query params
     * @return array
     */
    public function searchLookingFor($values){
        $this->initSearch();
        $repo = $this->services->get('doctrine')->getManager()
                     ->getRepository('AppOfferBundle:Offer');
        $this->setQueryValues($values);
        $filters = $this->getFilters();

        $now = new \DateTime();
        $past = clone $now;
        $past->modify('- 24 hours');

        $filters[] = array('field'=>'o.modifyDate','condition'=>'<=','value'=>$now->format('Y-m-d H:i:s'));
        $filters[] = array('field'=>'o.modifyDate','condition'=>'>=','value'=>$past->format('Y-m-d H:i:s'));

        $promoItems = $repo->searchPromo(0,
                               10,
                               $filters,
                               array('o.modifyDate'=>'DESC'));


        $normalLimit = 10 + (10 - count($promoItems));
        $normalItems = $repo->search(0,
                               $normalLimit,
                               $filters,
                               array('o.modifyDate'=>'DESC'));

        return array_merge($promoItems,$normalItems);
    }
    /**
     * Handle request to form
     */
    public function handleRequest(){
        $request = $this->services->get('request');
        $this->searchForm->handleRequest($request);
    }
    /**
     * Get form view
     *
     * @return FormView
     */
    public function getFormView(){

        return $this->searchForm->createView();
    }
     /**
     * Get mobile form view
     *
     * @return FormView
     */
    public function getMobileFormView(){

        return $this->searchMobileForm->createView();
    }
    /**
     * Paginator
     *
     * @return array
     */
    public function paginator(){
        $pageRange = 3;
        $repo = $this->services->get('doctrine')->getManager()
                     ->getRepository('AppOfferBundle:Offer');

        $totalNormal = $repo->countSearchResults($this->getFilters());
        $totalPromo = $repo->countPromoSearchResults($this->getFilters());

        $totalItems = $totalNormal + $totalPromo;
        $promoPerPage = $this->calculatePromoPerPage($totalPromo,$totalItems);

        $normalItems = $repo->search($this->calculateStartForNormal($promoPerPage,$totalPromo),
                               $this->calculateNormalPerPage($promoPerPage,$totalPromo),
                               $this->getFilters(),
                               $this->getOrder());

        $promoItems = $repo->searchPromo($this->calculateStartForPromo($promoPerPage),
                               $promoPerPage,
                               $this->getFilters(),
                               $this->getOrder());

        $viewData = $this->getViewData($pageRange, $totalItems);
        $items = array_merge($promoItems,$normalItems);

        return array_merge($viewData,array('items'=>$items));
    }
    /**
     * location index autocomplete
     *
     * @param string $query query params
     * @param int $limit limit
     * @return LocationAutocomplete[]
     */
    public function locationIndexAutocomplete($query,$limit=12){
        $cache = $this->services->get('doctrine_cache.providers.main_cache');
        $key = 'location_index_autocomplete_new_'.self::prepareCacheKey($query).'_'.$limit;

        if ($cache->contains($key)) {
            return $cache->fetch($key);
        } else {
            $locations = $this->services->get('doctrine')
                     ->getRepository('AppOfferBundle:LocationAutocomplete')
                     ->findSuggestions($query,$limit);
            $cache->save($key,$locations,1);
        }

        return $locations;
    }
    /**
     * Find closest location
     *
     * @param string $query query params
     * @return LocationAutocomplete
     */
    public function findClosestLocation($query){
        $cache = $this->services->get('doctrine_cache.providers.main_cache');
        $key = 'closest_location_'.self::prepareCacheKey($query);
        if ($cache->contains($key)) {
            return $cache->fetch($key);
        } else {
            $location = $this->services->get('doctrine')
                     ->getRepository('AppOfferBundle:LocationAutocomplete')
                     ->closestLocation($query);
            $cache->save($key,$location,60*60*2);
        }

        return $location;

    }
    /**
     * Find location by key
     *
     * @param string $query query params
     * @return LocationAutocomplete
     */
    public function findLocationByKey($query){

        $repo = $this->services->get('doctrine')
                     ->getRepository('AppOfferBundle:LocationAutocomplete');

        return $repo->findOneByUniqueKey($query);
    }
    /**
     * Find suggested location
     *
     * @param string $query query params
     * @return LocationAutocomplete
     */
    public function findSuggestedLocation($query){
        $repo = $this->services->get('doctrine')
                     ->getRepository('AppOfferBundle:LocationAutocomplete');

        return $repo->findSuggestedLocation($query);
    }
    public function findLocationByName($query){
        $repo = $this->services->get('doctrine')
            ->getRepository('AppOfferBundle:LocationAutocomplete');

        return $repo->findOneByName($query);

    }
    /**
     * Find suggested location
     *
     * @param string $query query params
     * @return LocationAutocomplete
     */
    public function findLocationKeyByName($query){

        $location = $this->findLocationByName($query);

        if(is_object($location)){

            return $location->getUniqueKey();
        }

        return null;
    }
    /**
     * Region index autocomplete
     * @param string $query query params
     * @return RegionIndex[]
     */
    public function regionIndexAutocomplete($query){
        $repo = $this->services->get('doctrine')
                     ->getRepository('AppOfferBundle:RegionIndex');

        return $repo->findSuggestions($query);
    }
    /**
     * City index autocomplete
     *
     * @param type $query query params
     * @return type
     */
    public function cityIndexAutocomplete($query){
        $repo = $this->services->get('doctrine')
                     ->getRepository('AppOfferBundle:CityIndex');

        return $repo->findSuggestions($query);
    }
    /**
     * Street index autocomplete
     *
     * @param string $query query params
     * @return StreetIndex[]
     */
    public function streetIndexAutocomplete($query){
        $repo = $this->services->get('doctrine')
                     ->getRepository('AppOfferBundle:StreetIndex');

        return $repo->findSuggestions($query);
    }
    /**
     * Get view data
     * @param int $pageRange page range
     * @param int $totalItems total items
     * @return array
     */
    private function getViewData($pageRange,$totalItems){
    $pageCount = $this->getPageCount($totalItems);
    $current = $this->getCurrentPage();
    if ($pageCount < $current) {
        $current = $pageCount;
    }
    if ($pageRange > $pageCount) {
        $pageRange = $pageCount;
    }
    $delta = ceil($pageRange / 2);
    if ($current - $delta > $pageCount - $pageRange) {
        $pages = range($pageCount - $pageRange + 1, $pageCount);
    } else {
        if ($current - $delta < 0) {
            $delta = $current;
        }
        $offset = $current - $delta;
        $pages = range($offset + 1, $offset + $pageRange);
    }
    $proximity = floor($pageRange / 2);
    $startPage  = $current - $proximity;
    $endPage    = $current + $proximity;
    if ($startPage < 1) {
        $endPage = min($endPage + (1 - $startPage), $pageCount);
        $startPage = 1;
    }
    if ($endPage > $pageCount) {
        $startPage = max($startPage - ($endPage - $pageCount), 1);
        $endPage = $pageCount;
    }
    $viewData = array(
        'last'              => $pageCount,
        'current'           => $current,
        'ItemsPerPage'      => $this->getLimit(),
        'first'             => 1,
        'pageCount'         => $pageCount,
        'totalCount'        => $totalItems,
        'pageRange'         => $pageRange,
        'startPage'         => $startPage,
        'endPage'           => $endPage
    );
    if ($current - 1 > 0) {
        $viewData['previous'] = $current - 1;
    }
    if ($current + 1 <= $pageCount) {
        $viewData['next'] = $current + 1;
    }
    $viewData['pagesInRange'] = $pages;
    $viewData['firstPageInRange'] = min($pages);
    $viewData['lastPageInRange']  = max($pages);

    if ($totalItems !== null) {
        $viewData['currentItemCount'] = $this->getCurrentPage();
        $viewData['firstItemNumber'] = (($current - 1) * $this->getLimit()) + 1;
        $viewData['lastItemNumber'] = $viewData['firstItemNumber'] + $viewData['currentItemCount'] - 1;
    }

    return $viewData;
}
    /**
     * Get filters
     *
     * @return array
     */
    private function getFilters(){

//        if($this->isMobileQuery){
//            return $this->searchQueryMobile->getFilters();
//        }
        return $this->searchQuery->getFilters();
    }
    /**
     * Set query values
     *
     * @param array $values query values
     */
    public function setQueryValues($values){
        $searchQuery = $this->searchQuery;

        if($this->isMobileQuery){
            $searchQuery = $this->searchQueryMobile;
        }

        foreach ($values as $property => $value) {
            // create a setter
            $method = sprintf('set%s', ucwords($property)); // or you can cheat and omit ucwords() because PHP method calls are case insensitive
            // use the method as a variable variable to set your value
            if(method_exists($searchQuery, $method)){
               $searchQuery->$method($value);
            }

        }
    }
    /**
     * Calculate promo per page
     *
     * @param int $totalPromo total promo
     * @param int $totalItems total items
     * @return int
     */
    private function calculatePromoPerPage($totalPromo,$totalItems){
        $promoPerPage = 3;

        $promoPages = ceil($totalPromo/$promoPerPage);
        if($promoPages > $this->getPageCount($totalItems)){
            $promoPerPage = ceil($totalPromo/$this->getPageCount($totalItems));
        }

        return $promoPerPage;
    }
    /**
     * Get limit
     * @return int
     */
    private function getLimit(){
        $request = $this->services->get('request');
        $limit = (int)$request->get('limit',40);

        if(!in_array($limit, self::$permittedLimit)){
            return 40;
        }

        return $limit;
    }
    /**
     * Calculate normal per page
     *
     * @param int $promoPerPage promo per page
     * @param int $totalPromo total promo
     * @return int
     */
    private function calculateNormalPerPage($promoPerPage,$totalPromo){
        $currentPage = $this->getCurrentPage();

        if($currentPage > 1){
            $diff = $totalPromo - (($currentPage-1) * $promoPerPage);

            if($diff > $promoPerPage){
               $promoOffset = $promoPerPage;
            }else {
                $promoOffset = ($diff >= 0)? $diff : 0;
            }

           $limit = $this->getLimit() - $promoOffset;
        }else {
           if($totalPromo < $promoPerPage) {
                $limit = abs($this->getLimit() - ($totalPromo));
           }else {
                $limit = abs($this->getLimit() - $promoPerPage);
           }
        }
        return $limit;
    }
    /**
     * calculate start for normal
     *
     * @param int $promoPerPage promo per page
     * @param int $totalPromo total promo
     * @return int
     */
    private function calculateStartForNormal($promoPerPage,$totalPromo){
        $page = $this->getCurrentPage();

        if($page==1){
            return 0;
        }

        $prevPage = $page - 1;
        $promoPages = ceil($totalPromo/$promoPerPage);

        if($page<= $promoPages){
            $offset = $prevPage * ($this->getLimit()-$promoPerPage);
        }else {
            $offset = $promoPages * ($this->getLimit()-$promoPerPage);
            $restPromo = $totalPromo - ($promoPages * $promoPerPage);
            $offset += $restPromo;
            $offset += ($page - ($promoPages+1)) * $this->getLimit();
        }

        return $offset;
    }
    /**
     * Calculate start for promo
     *
     * @param int $promoPerPage promo per page
     * @return int
     */
    private function calculateStartForPromo($promoPerPage){
        $page = $this->getCurrentPage();
        return ($page==1) ? 0 : ($page-1) * $promoPerPage;
    }
    /**
     * Get page count
     *
     * @param type $totalItems total items
     * @return int
     */
    private function getPageCount($totalItems){
        return ceil($totalItems / $this->getLimit());
    }
    /**
     * Get current page
     * @return int
     */
    private function getCurrentPage(){
        $request = $this->services->get('request');
        $page = (int)$request->get('page',1);
        if($page < 1){
            $page = 1;
        }
        return $page;
    }
    /**
     * Get order
     *
     * @return array
     */
    private function getOrder(){
        $request = $this->services->get('request');

        if($request->get('order')){
            $parts = explode('_',$request->get('order'));

            if(count($parts)==2 &&
                in_array($parts[0], self::$permittedOrderColumns)){

                return  array($parts[0]=> ($parts[1]=='DESC') ? 'DESC' : 'ASC' );
            }
        }

        return array('o.modifyDate'=>'DESC');
    }
    /**
     * Get available countries
     *
     * @param int $idCategory id category
     * @param int $idTranasactionType transaction type id
     * @return Country[]
     */
    public function getAvailableCountries($idCategory,$idTranasactionType) {
        $repo = $this->services->get('doctrine')
                     ->getRepository('AppOfferBundle:Country');

        return $repo->findAvailableCountries($idCategory,$idTranasactionType);
    }
    private function getCurrentSubdomain(){
        $currentHost = $this->services->get('request')->getHttpHost();
        $baseHost = $this->services->getParameter('domain');

        $host = str_replace($baseHost, '', $currentHost);

        if(empty($host)){
            return null;
        }
        return trim($host,'.');
    }

    public function findAvailableCountries($idCategory,$idTranasactionType){

        return $this->services->get('doctrine')
                         ->getRepository(Country::class)
                         ->findAvailableCountries($idCategory,$idTranasactionType,false);
    }
    private static function prepareCacheKey($query, $delimiter='-')
    {
        setlocale(LC_ALL, 'en_US.UTF8');

	$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $query);
	$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
	$clean = strtolower(trim($clean, '-'));
	$clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);

	return $clean;
    }

    /**
     * @return LocationAutocomplete
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param int $limit
     * @return mixed
     */
    public function getBestResults($limit = 12){
        return $this->services->get('doctrine')
            ->getRepository(SearchStatistics::class)
            ->findBestResults($limit);
    }
    public function getSearchParams()
    {
        $params = [];
        $request = $this->services->get('request');

        if($request->query->has('search_mobile')){
            $search = $request->query->get('search_mobile');
        }else {
            $search = $request->query->get('search');
        }
        $params['searchQuery'] = $search;
        $location = null;

        if(isset($search['locationIndexLike'])){
            $params['location'] = $this->services->get('search.manager')
                ->findClosestLocation($search['locationIndexLike']);
        }


        if(empty($params['location'])){
            $params['location'] = null;
            $params['locationKey'] = null;
        }

        $params['locationKey'] = $params['location']['uniqueKey'];


        $categories = [
            1 => [
                'key'=>'mieszkanie',
                'name'=>'Mieszkanie',
                'plural'=>'Mieszkania',
                'second_plural'=>'Mieszkań',
                'id'=>1,
            ],
            2 =>  [
                'key'=>'dom',
                'name'=>'Dom',
                'plural'=>'Domy',
                'second_plural'=>'Domów',
                'id'=>2,
            ],
            3 =>[
                'key'=>'dzialka',
                'name'=>'Działka',
                'plural'=>'Działki',
                'second_plural'=>'Działek',
                'id'=>3,
            ],
            4 => [
                'key'=>'lokal',
                'name'=>'Lokal',
                'plural'=>'Lokale',
                'second_plural'=>'Lokali',
                'id'=>4,
            ],
            5 => [
                'key'=>'komercyjne',
                'name'=>'Nieruchomość Komercyjna',
                'plural'=>'Nieruchomości Komercyjne i inwestycyjne',
                'second_plural'=>'Nieruchomości Komercyjnych',
                'id'=>5,
            ],
            6 =>[
                'key'=>'garaz',
                'name'=>'Garaż',
                'plural'=>'Garaże',
                'second_plural'=>'Garaży',
                'id'=>6,
            ],
        ];

        $transactionTypes = [
            1=>[
                'key'=>'sprzedaz',
                'name'=>'Sprzedaż',
                'id'=>1,
            ],
            2=>[
                'key'=>'kupno',
                'name'=>'Kupno',
                'id'=>2,
            ],
            3=>[
                'key'=>'wynajem',
                'name'=>'Wynajęcia',
                'id'=>3,
            ],
            4=>[
                'key'=>'najem',
                'name'=>'Najem',
                'id'=>4,
            ],
        ];
        $reverseTransactionTypes = [
            'wynajem'=>[
                'key'=>'sprzedaz',
                'name'=>'Sprzedaż',
                'id'=>1,
            ],
            'najem'=>[
                'key'=>'kupno',
                'name'=>'Kupno',
                'id'=>2,
            ],
            'sprzedaz'=>[
                'key'=>'wynajem',
                'name'=>'Wynajem',
                'id'=>3,
            ],
            'kupno'=>[
                'key'=>'najem',
                'name'=>'Najem',
                'id'=>4,
            ],
        ];

        if($search['transactionType']===3){
            $categories[1]["second_plural"] = "Mieszkania do ";
        }

            if(isset($search['category'])){
                $params['category'] = $categories[$search['category']];
                $params['categoryId'] = $search['category'];
            }

            if(isset($search['transactionType'])){
                $params['transactionType'] = $transactionTypes[$search['transactionType']];
                $params['transactionTypeId'] = $search['transactionType'];
                $params['reverseTransactionType'] = $reverseTransactionTypes[$params['transactionType']['key']];
            }


        $params['transactionTypes'] = $transactionTypes;
        $params['categories'] = $categories;

        return $params;
    }

    public function findSearchStats(Category $category, TransactionType $transaction, LocationAutocomplete $location)
    {
        return $this->services->get('doctrine')->getRepository(SearchStatistics::class)
                    ->findOneBy(['category'=>$category, 'transaction'=>$transaction,'locationId'=>$location->getId()])
            ;
    }

    /**
     * @return void
     */
    public function initSearch()
    {
        $this->searchQuery = new Search();
        $this->searchQueryMobile = new SearchMobile();
    }

}

