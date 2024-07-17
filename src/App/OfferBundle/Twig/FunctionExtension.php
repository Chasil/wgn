<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Twig;

use App\AppBundle\Component\SubdomainHelper;
use App\AppBundle\Component\UrlHelper;
use App\OfferBundle\Entity\Offer;
use http\Env\Request;
use \Twig_Filter_Function;
use \Twig_Filter_Method;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
/**
 * Class FunctionExtension
 *
 * @author wojciech przygoda
 */
class FunctionExtension extends \Twig_Extension
{
    /**
     * @const search mobile
     */
    const SEARCHMOBILE = 'search_mobile';
    /**
     * @const search
     */
    const SEARCH = 'search';
    /**
     * @const entity
     */
    const TYPE_ENTITY = 'entity';
    /**
     * @const string
     */
    const TYPE_STRING = 'string';
    /**
     *
     * @var Container services container
     */
    private $services;
    /**
     *
     * @var Request request
     */
    private $request;
    /**
     *
     * @var array title parameters
     */
    private static $titleParams =
                                array('category'=>
                                    array(
                                            'type'=>self::TYPE_ENTITY,
                                            'class'=>'App\\OfferBundle\\Entity\\Category',
                                            'decorator'=>'comma'
                                        ),
                                    'type'=>
                                    array(
                                            'type'=>self::TYPE_ENTITY,
                                            'class'=>'App\\OfferBundle\\Entity\\Type',
                                            'decorator'=>'comma'
                                        ),
                                      'transactionType'=>
                                    array(
                                            'type'=>self::TYPE_ENTITY,
                                            'class'=>'App\\OfferBundle\\Entity\\TransactionType',
                                            'decorator'=>'for'
                                        ),
                                        'locationIndexLike'=>
                                    array(
                                            'type'=>self::TYPE_STRING,
                                            'decorator'=>'comma'
                                        ),
                                        'market'=>
                                    array(
                                            'type'=>self::TYPE_ENTITY,
                                            'class'=>'App\\OfferBundle\\Entity\\Market',
                                            'decorator'=>'market'
                                        ),
                                        'office'=>
                                    array(
                                            'type'=>self::TYPE_ENTITY,
                                            'class'=>'App\\OfficeBundle\\Entity\\Office',
                                            'decorator'=>'office'
                                        ),
                                        'user'=>
                                    array(
                                            'type'=>self::TYPE_ENTITY,
                                            'class'=>'App\\UserBundle\\Entity\\User',
                                            'parent'=>'Office',
                                            'decorator'=>'agent'
                                        ),
                                );

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
            new \Twig_SimpleFunction('parseEmbedVideo',[$this,'parseEmbedVideo'],['is_safe' => ['html']]),
            new \Twig_SimpleFunction('isObserved',[$this,'isObserved'],['is_safe' => ['html']]),
            new \Twig_SimpleFunction('isExclusive',[$this,'isExclusive'],['is_safe' => ['html']]),
            new \Twig_SimpleFunction('observedCart',[$this,'observedCart'],['is_safe' => ['html']]),
            new \Twig_SimpleFunction('listOffersTitle',[$this,'listOffersTitle'],['is_safe' => ['html']]),
            new \Twig_SimpleFunction('countOffers',[$this,'countOffers'],['is_safe' => ['html']]),
            new \Twig_SimpleFunction('randomOffers',[$this,'randomOffers'],['is_safe' => ['html']]),
            new \Twig_SimpleFunction('randomArchiveOffers',[$this,'randomArchiveOffers'],['is_safe' => ['html']]),
            new \Twig_SimpleFunction('offersListDescription',[$this,'getOffersListDescription'],['is_safe' => ['html']]),
            new \Twig_SimpleFunction('offersListMetaDescription',[$this,'getOffersListMetaDescription'],['is_safe' => ['html']]),
			new \Twig_SimpleFunction('articlesListDescription',[$this,'getArticlesListDescription'],['is_safe' => ['html']]),
			new \Twig_SimpleFunction('reverseCategoryUrl',[$this,'getReverseCategoryUrl'],['is_safe' => ['html']]),
            new \Twig_SimpleFunction('blogArticlesBox',[$this,'getBlogArticlesBox'],['is_safe' => ['html']]),
            new \Twig_SimpleFunction('offerUrl',[$this,'offerUrl'],['is_safe' => ['html']]),
            new \Twig_SimpleFunction('offerUrlFromArray',[$this,'offerUrlFromArray'],['is_safe' => ['html']]),
            new \Twig_SimpleFunction('subdomain',[$this,'subdomain'],['is_safe' => ['html']]),
            new \Twig_SimpleFunction('currentOffers',[$this,'currentOffers'],['is_safe' => ['html']]),
            new \Twig_SimpleFunction('currentCityOffers',[$this,'currentCityOffers'],['is_safe' => ['html']]),
            new \Twig_SimpleFunction('currentProvinceOffers',[$this,'currentProvinceOffers'],['is_safe' => ['html']]),
            new \Twig_SimpleFunction('currentOffersBy',[$this,'currentOffersBy'],['is_safe' => ['html']]),
            new \Twig_SimpleFunction('isOccasion',[$this,'isOccasion'],['is_safe' => ['html']]),
            ];

    }

    public function isOccasion($offer)
    {
        if(!is_a($offer, 'App\OfferBundle\Entity\Offer')){
            $offer = $this->services->get('offer.manager')->findById($offer['id']);
        }

        return $this->services->get('offer.manager')->getAvgPricem2($offer) > $offer->getPricem2();
    }

    public function currentOffers($offer, $limit = 100)
    {
        return $this->services->get('templating')
            ->render('AppOfferBundle:Twig:currentOffers.html.twig',array(
                'offers'=>$this->services->get('offer.manager')->getCurrentOffers($offer, $limit),
                'offer'=>$offer
            ));
    }

    public function currentCityOffers($city, $limit = 100)
    {
        return $this->services->get('templating')
            ->render('AppOfferBundle:Twig:currentCityOffers.html.twig',array(
                'offers'=>$this->services->get('offer.manager')->getCurrentCityOffers($city, $limit),
                'city'=>$city
        ));
    }

    public function currentProvinceOffers(string $subdomain, int $limit = 100)
    {
//        $office = $this->services->get('office.manager')->findBySubdomain($subdomain);
//        return $this->services->get('templating')
//            ->render('AppOfferBundle:Twig:currentProvinceOffers.html.twig',array(
//                'offers'=>$this->services->get('offer.manager')->getCurrentProvinceOffers($province, $limit),
//                'province'=>$province
//        ));
    }

    public function currentOffersBy(
        $parameters
    )
    {
        $title = $parameters['title'] ?? false;
        $class = $parameters['class'] ?? 'col-md-4 col-xs-12 col-sm-6';
        $criteria = $parameters['criteria'] ?? [];
        $limit = $parameters['limit'] ?? false;
        $archived = $parameters['archived'] ?? false;
        $order = $parameters['order'] ?? [];

        $offers = $this->services->get('offer.manager')->getCurrentOffersBy(
                $criteria,
                $order,
                $limit,
                $archived
            );

        return $this->services->get('templating')
            ->render('AppOfferBundle:Twig:currentOffersBy.html.twig',array(
                'offers' => $offers,
                'title' => $title,
                'class' => $class
            ));
    }


    public function getReverseCategoryUrl(){
        $params = $this->services->get('search.manager')->getSearchParams();

        $subdomain = SubdomainHelper::prepareSubdomainFromParameters($params['category']['id'], $params['reverseTransactionType']['id'], $params['location']['uniqueKey']);
        $url = $this->services->get('router')
                                ->generate('frontend_offer_search_subdomain',['subdomain'=>$subdomain]);

        $name = $params['category']['name'] . ' na ' . strtolower($params['reverseTransactionType']['name']);

        if(!empty($params['location']['city'])){
            $name .= ' ' . $params['location']['city'];
        }else {
            $name .= ' ' . $params['location']['name'];
        }

        return
            [
                'url'=>$url,
                'name' =>$name,
            ];
    }
    public function getOffersListDescription() {
        $params = $this->services->get('search.manager')->getSearchParams();
        $description = null;
        $city = null;

        if(isset($params['location']['uniqueKey']))
        {
            $city = $params['location']['uniqueKey'];
        }

        if(isset($params['transactionTypeId']) && isset($params['categoryId'])){
            $description = $this->services->get('offer_category_description.manager')
                ->getDescriptionBy($params['transactionTypeId'],$params['categoryId'], $city);
        }



        if(is_object($description)){

            return $description->getDescription();
        }

        return '';
    }
     public function getOffersListMetaDescription(){
        $description = '';
        $params = $this->services->get('search.manager')->getSearchParams();

         /**
          * @var \Symfony\Component\HttpFoundation\Request $request
          */
        $request = $this->services->get('request');
        $page = $request->get('page');

        if(isset($params['transactionType'])){
            $description .= $params['transactionType']['name'];
        }
        if(isset($params['category'])){
            $description .= ' ' . $params['category']['second_plural'];
        }

        if(isset($params['transactionType']) && isset($params['category'])){
            $h1 = explode(',', $this->listOffersTitle());
            $description = $h1[0];
        }

        if(!empty($params['location']['city'])){
            $description .= ', ' . $params['location']['city'];
            $description .= ', ' . $params['location']['province'];
        }else if(!empty($params['location']['name'])){
            $description .= ', ' . $params['location']['name'];
        }

         if($page > 1)
         {
            $description .= ' -strona '.$page;
         }

        $description .= ' ✅ WGN - Na Rynku Od 1991 Roku ✅ Wykwalifikowani Agenci Nieruchomości ✅ Ponad 60 000 Ofert ze Zdjęciami ✅ Sprawdź Teraz i Zadzwoń »';

        return $description;
     }
    public function randomOffers() {

        return $this->services->get('templating')
                    ->render('AppOfferBundle:Twig:randomOffers.html.twig',array(
                        'offers'=>$this->services->get('offer.manager')->getRandomOffers(50),
                    ));
    }
    public function randomArchiveOffers() {

        return $this->services->get('templating')
                    ->render('AppOfferBundle:Twig:randomArchiveOffers.html.twig',array(
                        'offers'=>$this->services->get('offer.manager')->getCurrentOffersBy([],'rand',50,true),
                    ));
    }
    /**
     * parse embed video
     *
     * @param string $url video url
     * @return string
     */
    public function parseEmbedVideo($url){
        if(preg_match('/youtu/', $url)){
            $url = preg_replace('/watch\?v=/', 'embed/', $url);
        }
        if(preg_match('/youtu.be/', $url)){
            $url = str_replace('https://youtu.be/', 'https://www.youtube.com/embed/', $url);
        }
        return $url;
    }
    /**
     * Count offers
     *
     * @param bool $active only active offers
     * @return int
     */
    public function countOffers($active){
        return $this->services->get('offer.manager')->countOffers($active);
    }
    /**
     * Generate observed button
     *
     * @param Offer $offer
     * @return string
     */
    public function isObserved($offer){
        $observedManager = $this->services->get('observed.manager');
        $html = '<div class="col-md-12 observed hidden-print">';
        if($observedManager->isObserved($offer)){
            $html .= '<a href="#" data-action="removeFormObserved" title="Usuń z obserwowanych"><i class="fa fa-eye-slash"></i> Usuń z obserwowanych</a>';
            $html .= '<a href="#" data-action="addToObserved" class="hidden" title="Dodaj do obserwowanych"><i class="fa fa-eye"></i> Dodaj do obserwowanych</a>';
        }else {
            $html .= '<a href="#" data-action="removeFormObserved" class="hidden" title="Usuń z obserwowanych"><i class="fa fa-eye-slash"></i> Usuń z obserwowanych</a>';
            $html .= '<a href="#" data-action="addToObserved" title="Dodaj do obserwowanych"><i class="fa fa-eye"></i> Dodaj do obserwowanych</a>';
        }

        $html .= '</div>';

        return $html;
    }
    /**
     * Generate observed button
     * @param string $size
     * @poram string $isMobile
     * @return string
     */
    public function isExclusive(string $size='normal', string $isMobile=''){
        $notice = 'Oferta tylko u nas';
        if($isMobile==='' && $size==='tiny'){ $notice = 'Oferta na wyłączność'; }

        return sprintf('<p class="is-exclusive %s"><i class="fa fa-lock %s"></i> %s</p>', $isMobile, $size, $notice);
    }
    /**
     * Generate observed cart
     * @return string
     */
    public function observedCart(){
        $observedManager = $this->services->get('observed.manager');
        $userManager = $this->services->get('user.manager');

        if($userManager->isLogged()){
           $route = $this->services->get('router')->generate('user_account_observed');
        }else {
            $route = $this->services->get('router')->generate('frontend_offer_observed_list');
        }

        $count = $observedManager->countObserved();
        $hidden = ($count== 0)? 'hidden' : '';
        $html = '<a href="'.$route.'" class="observed-cart badge bg-gray '.
                $hidden.'" title="Pokaż"><i class="fa fa-eye"></i> ';
        $html .= '<span>'.$count.'</span>';
        $html .= '</a>';

        return $html;
    }

    /**
     * Generate list offers title
     * @param string $suffix
     * @return string
     */
    public function listOffersTitle($suffix = '') {
        $params = $this->services->get('search.manager')->getSearchParams();

        $cache = $this->services->get('doctrine_cache.providers.main_cache');
        $cacheKey = "list_offers_title" . $suffix . '_'.serialize($params);

        $title = $cache->fetch($cacheKey);
        if($title!=false){
            $title = rtrim($title, ', ');
            $title = str_replace(",  |"," |", $title);
        }

        if ($title === false) {
            $title = $this->prepareListTitle($params, $suffix);
            $cache->save($cacheKey,$title ,$this->services->getParameter('cache_lifetime'));
        }

        return $title;
    }

    private function prepareListTitle($params, $suffix)
    {
        $title = '';
        if(isset($params['searchQuery']['office']) && isset($params['searchQuery']['user'])){

            $office = $this->services->get('office.manager')->findById($params['searchQuery']['office']);
            $agent = $this->services->get('user.manager')->findById($params['searchQuery']['user']);

            $title = 'Ogłoszenia Agenta  '. $agent . ' z Biura WGN '.$office->getDefaultAddress()->getCity() . ', '.$office->getDefaultAddress()->getStreet();

            return $title;
        }

        if(isset($params['searchQuery']['office'])){

            $office = $this->services->get('office.manager')->findById($params['searchQuery']['office']);
            $title = 'Ogłoszenia Biura Nieruchomości, '.$office->getDefaultAddress()->getCity().', '.$office->getDefaultAddress()->getStreet().' - WGN';

            return $title;
        }

        if(isset($params['category']) && isset($params['transactionType'])){
            $title = $params['category']['plural'] . ' na ' . $params['transactionType']['name'];

            if($params['transactionType']['name'] == "Wynajęcia"){
                $title = $params['category']['plural'] . ' do ' . $params['transactionType']['name'];
            }
        }

        if(isset($params['location']['city'])){
            $title .= ', ' .$params['location']['city'];
        }
        if(isset($params['location']['section']) && !empty($params['location']['section'])){
            $title .= ', ' .$params['location']['section'];
        }

//        if(isset($params['location']['province'])){
//            $title .= ', ' .$params['location']['province'];
//        }

        if(isset($params['searchQuery']['market'])) {
            $title .= ', rynek ';
            $title .= ($params['searchQuery']['market'] == 1)? 'pierwotny' : 'wtórny';
        }

        return $title . $suffix;
    }
    /**
     * Get params
     * @return string
     */
    private function getParams(){
        $output = '';
        foreach(self::$titleParams as $param=>$settings){
            $output .= $this->getParam($param,$settings);
        }
        return rtrim($output,', ');
    }
    /**
     * Get param
     *
     * @param array $param
     * @param array $settings
     * @return string
     */
    private function getParam($param,$settings){
        $searchType = ($this->isMobileSearch()) ? self::SEARCHMOBILE : self::SEARCH;

        $value = (isset($this->services->get('request')->get($searchType)[$param])) ?
                    $this->services->get('request')->get($searchType)[$param] : '';

        if($this->hasType() && $param=='category'){
            return;
        }
        if(!$this->hasType() && $param=='type'){
            return;
        }
        if($this->hasUser() && $param=='office'){
            return;
        }

        switch($settings['type']){
            case self::TYPE_ENTITY:
                $parent = (isset($settings['parent'])) ? $settings['parent'] : null;
                $value = $this->getEntityValue($settings['class'], $value, $parent);
            break;
            case self::TYPE_STRING:
                $value = $this->getStringValue($value);
            break;
        }

        if(isset($settings['decorator']) &&
            method_exists($this, $settings['decorator'].'Decorator')){
            return $this->{$settings['decorator'].'Decorator'}($value);
        }

        if(is_array($value)){
            return implode(' ',$value);
        }
        return $value;
    }
    /**
     * Get entity value
     *
     * @param string $class class
     * @param mixed $value value
     * @param mixed $parent parent
     * @return string
     */
    private function getEntityValue($class, $value, $parent = null){
        $repo = $this->services->get('doctrine')->getRepository($class);
        $entity = $repo->findOneById((int)$value);
        if(is_a($entity,$class)){
            $entityParent = null;

            if($parent){
                $entityParent = $entity->{'get'.ucfirst($parent)}();
            }
            if(is_object($entityParent)){
                return array((string)$entity,(string)$entityParent);
            }
            return (string)$entity;
        }
    }
    /**
     * Get string value
     *
     * @param string $value value
     * @return string
     */
    private function getStringValue($value){
        return strip_tags($value);
    }
    /**
     * For decorator
     *
     * @param string $value value
     * @return string
     */
    private function forDecorator($value){
        return ($value!='') ? 'na '.strtolower($value).', ' : '';
    }
    /**
     * market decorator
     *
     * @param string $value value
     * @return string
     */
    private function marketDecorator($value){
        return ($value!='') ? 'rynek '.strtolower($value).', ' : '';
    }
    /**
     * office decorator
     *
     * @param string $value value
     * @return string
     */
    private function officeDecorator($value){
        return ($value!='') ? 'Ogłoszenia Biura WGN '.$value : '';
    }
    /**
     * agent decorator
     *
     * @param string $value value
     * @return string
     */
   private function agentDecorator($value){
        return (count($value)==2) ? 'Ogłoszenia Agenta  '.$value[0].' z Biura WGN '.$value[1] : '';
    }
    /**
     * comma decorator
     *
     * @param string $value value
     * @return string
     */
    private function commaDecorator($value){
        return ($value!='') ? $value.', ' : '';
    }
    /**
     * Is mobile search
     *
     * @return bool
     */
    private function isMobileSearch(){
        return $this->services->get('request')->query->has(self::SEARCHMOBILE);
    }
    /**
     * Has type
     * @return boolean
     */
    private function hasType(){
        if($this->isMobileSearch()){
            $params = $this->services->get('request')->query->get(self::SEARCHMOBILE);
        }else {
            $params = $this->services->get('request')->query->get(self::SEARCH);
        }

        if(isset($params['type']) && $params['type']!=''){
            return true;
        }

        return false;
    }
    /**
     * Has user
     * @return boolean
     */
    private function hasUser(){
        if($this->isMobileSearch()){
            $params = $this->services->get('request')->query->get(self::SEARCHMOBILE);
        }else {
            $params = $this->services->get('request')->query->get(self::SEARCH);
        }

        if(isset($params['user']) && !empty($params['user'])){
            return true;
        }

        return false;
    }

    /**
     * @return string|void
     * @throws \Twig_Error
     */
    public function getBlogArticlesBox()
    {
        $blogManager = $this->services->get('blog.manager');
        $subdomain = $this->services->get('request')->get('subdomain');

        if(empty($subdomain)){
            $searchParams = $this->services->get('request')->get('search');
            $subdomain = SubdomainHelper::prepareSubdomainFromParameters(
                $searchParams['category'],
                $searchParams['transactionType'],
                null
            );
        }
        $blog = $this->services->get('blog.manager')->getBySubdomain($subdomain);

        if(!is_object($blog))
        {
            return;
        }

        $articles = $blogManager->getArticles($blog);

        return $this->services
            ->get('templating')
            ->render("AppOfferBundle:Twig:articlesBox.html.twig",[
                    'articles'=>$articles,
                    'blog'=>$blog,
                ]
            );
    }
    
	public function getArticlesListDescription() {
		$request = $this->services->get('request');

		if($request->query->has('search_mobile')){
			$search = $request->query->get('search_mobile');
		}else {
			$search = $request->query->get('search');
		}
		
		$cityKey = null;
		
		if(isset($search['locationIndexLike'])){
			$cityKey = $this->services->get('search.manager')
				->findLocationKeyByName($search['locationIndexLike']);
		}
		
		
		if(is_null($cityKey)){
			$cityKey = '';
		}
		
		if(!isset($search['transactionType']) || !isset($search['category'])){
			return;
		}
		$description = $this->services->get('article_category_description.manager')
			->getDescriptionBy($search['transactionType'],$search['category'], $cityKey);
		
		if(is_object($description)){
			
			return $description->getDescription();
		}
		
		return '';
	}
	public function offerUrl(Offer $offer)
    {

        $router = $this->services->get('router');

        if($offer->isArchivOffer())
        {

            return $router->generate('frontend_offer_archive_subdomain',[
                'id'=>$offer->getId(),
                'offerName'=>UrlHelper::prepare($offer->getName()),
            ],true);
        }

        if(!empty($offer->getSubdomain()))
        {

            return $router->generate('frontend_offer_subdomain',[
                'id'=>$offer->getId(),
                'offerName'=>UrlHelper::prepare($offer->getName()),
                'subdomain'=>$offer->getSubdomain(),
            ],true);
        }

        return $router->generate('frontend_offer_show',[
            'id'=>$offer->getId(),
            'offerName'=>UrlHelper::prepare($offer->getName()),
            'domain'=>$this->services->getParameter('domain'),
        ], true);
    }
	public function offerUrlFromArray(array $offer)
    {

        $router = $this->services->get('router');

        if(!is_object($offer['expireDate']))
        {
            $offer['expireDate'] = new \DateTime($offer['expireDate']);
        }

        if($offer['expireDate'] < new \DateTime() || $offer['isPublish'] == false)
        {

            return $router->generate('frontend_offer_archive_subdomain',[
                'id'=>$offer['id'],
                'offerName'=>UrlHelper::prepare($offer['name']),
            ],true);
        }

        if(!empty($offer['subdomain']))
        {

            return $router->generate('frontend_offer_subdomain',[
                'id'=>$offer['id'],
                'offerName'=>UrlHelper::prepare($offer['name']),
                'subdomain'=>$offer['subdomain'],
            ],true);
        }

        return $router->generate('frontend_offer_show',[
            'id'=>$offer['id'],
            'offerName'=>UrlHelper::prepare($offer['name']),
            'domain'=>$this->services->getParameter('domain'),
        ], true);
    }
    public function subdomain($categoryId, $transactionId, $locationKey)
    {
        return SubdomainHelper::prepareSubdomainFromParameters($categoryId, $transactionId, $locationKey);
    }
    /**
     * Get name
     * @return string
     */
    public function getName()
    {
        return 'offer_function_extension';
    }
}

