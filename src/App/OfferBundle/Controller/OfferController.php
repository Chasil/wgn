<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Controller;

use App\AppBundle\Component\SubdomainHelper;
use App\AppBundle\Component\UrlHelper;
use App\NewsletterBundle\Entity\Newsletter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\FormError;
use App\OfferBundle\Entity\Offer;
use App\OfferBundle\Form\OfferType;
use App\OfferBundle\Form\ContactType;
use App\OfferBundle\Model\Contact;
use App\PaymentBundle\Entity\Payment;
use App\PaymentBundle\Entity\PaymentItem;
use App\PaymentBundle\Form\PaymentType;
use App\PaymentBundle\Form\PromoPaymentType;
use App\OfferBundle\Form\LookingForType;
use App\OfferBundle\Model\LookingFor;
use App\UserBundle\Form\AbuseContactType;
use App\UserBundle\Model\Contact as AbuseContact;
use App\NewsletterBundle\Entity\LookingFor as LookingForNewsletter;

/**
 * Class OfferController
 *
 * @author wojciech przygoda
 */
class OfferController extends Controller
{
    /**
     * Select offer type
     *
    */
    public function selectTypeAction(){
        return $this->render('AppOfferBundle:Offer:selectType.html.twig',array(
                             'categories'=>$this->get('offer.manager')->getAllCategories(true),
                             'transactionTypes'=>$this->get('offer.manager')->getTransactionTypes(true)));
    }
    /**
     * Add offer
     *
    */
    public function addAction() {
        $request = $this->getRequest();
        $offerManager = $this->get('offer.manager');
        $userManager = $this->get('user.manager');
        $user = $userManager->getCurrentLogged();

        if($request->get('id')){
            $offer = $offerManager->findById($request->get('id'));
            $type = $offer->getType();
            $transactionType = $offer->getTransactionType();
        }else {
            $type = $offerManager->findTypeById($request->get('type'));
            $transactionType = $offerManager->findTransactionTypeById($request->get('transaction'));
            $offer = new Offer();
            $offer->setType($type)
                  ->setCategory($type->getCategory())
                  ->setTransactionType($transactionType)
                  ->setUser($user);
        }

        $form = $this->createForm(new OfferType($type->getCategory(), $transactionType),$offer);

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $offerManager->add($offer);
                $this->get('session')->set('newIdOffer',$offer->getId());

                return $this->redirect($this->generateUrl('frontend_offer_add_step_3'));
            }
        }

        return $this->render('AppOfferBundle:Offer:add.html.twig',array(
            'offer'=>$offer,
            'type'=>$type,
            'transaction'=>$transactionType,
            'form'=>$form->createView(),
            'category'=>$type->getCategory()));
    }
    /**
     * Edit offer
     *
    */
    public function editAction(){
        $request = $this->getRequest();
        $offerManager = $this->get('offer.manager');
        $ids = $request->get('offer');
        if(count($ids)===0){
             $this->addFlash('success','Nie wybrano żadnej oferty.');
             $this->redirect($this->getRequest()->headers->get('referer'));
        }
        $offer = $offerManager->findById(current($ids));
        $this->denyAccessUnlessGranted('edit', $offer);

        $category = $offer->getCategory();
        $form = $this->createForm(new OfferType($category,$offer->getTransactionType()),$offer);
        $propertiesForm = $form->get('properties');
        $propertiesForm->remove('name');

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $offerManager->add($offer);
            $this->addFlash('success','Zapisano poprawnie.');
            return $this->redirect($this->generateUrl('user_account'));
        }

        return $this->render('AppOfferBundle:Offer:edit.html.twig',array(
            'offer'=>$offer,
            'form'=>$form->createView(),
            'transaction'=>$offer->getTransactionType(),
            'type'=>$offer->getType(),
            'category'=>$category));
    }
    /**
     * Preview offer
     *
    */
    public function previewAction(){
        $offerManager = $this->get('offer.manager');
        $offer = $offerManager->findById($this->get('session')->get('newIdOffer'));
        $contact = new Contact();
        $contact->setOffer($offer);
        $contactForm = $this->createForm(new ContactType($this->getDoctrine()->getManager()),
                                         $contact);

		$email = $offer->getEmail();
		$newsletter = $this->getDoctrine()->getRepository(Newsletter::class)->findOneBy(array('email' => $email));
		if(!is_object($newsletter)){
			$news = new Newsletter;
			$news->setEmail($email);
			$news->setIp($this->container->get('request')->getClientIp());
			$em = $this->getDoctrine()->getManager();
			$em->persist($news);
			$em->flush();
		}

        return $this->render('AppOfferBundle:Offer:preview.html.twig',array(
                               'offer'=>$offer,
                               'currencies'=>$offerManager->getCurrencies(),
                               'contactForm'=>$contactForm->createView()
        ));
    }
    /**
     * List of offers
     *
    */
    public function listAction(){
        $request = $this->getRequest();


        if($request->query->has('search_mobile')){
            $search = $request->query->get('search_mobile',[]);


        }else {
            $search = $request->query->get('search',[]);
        }

        $searchManager = $this->get('search.manager');

        $route = 'frontend_offer_search_url';

        if(isset($search['user']) && !empty($search['user'])){
            $route = 'frontend_offer_search_url_agent';
            $routeParams['user'] = $search['user'];
            unset($search['user']);
        }

        if(isset($search['locationIndexLike']) && $search['locationIndexLike']!=''){
            $routeParams['locationKey'] = null;
            $location = $searchManager->findLocationByName($search['locationIndexLike']);

            if(is_object($location))
            {
                $routeParams['locationKey'] = $location->getUniqueKey();
            }


        }

        if(isset($routeParams['locationKey']) && $routeParams['locationKey'] !=''){
            $route = 'frontend_offer_search_url_with_location';
            unset($search['locationIndexLike']);

            if(isset($routeParams['user'])){
                $route = 'frontend_offer_search_url_agent_with_location';
            }

        }elseif(isset($routeParams['locationKey'])) {
             unset($routeParams['locationKey']);
        }

        $transactionType = (isset($search['transactionType']))? $search['transactionType'] : 1;
        $category = (isset($search['category']))? $search['category'] : 1;


        $routeParams['transaction'] = $searchManager->getTransactionTypeKey($transactionType);
        $routeParams['type'] = $searchManager->getPropertyTypeKey($category);

        unset($search['transactionType']);
        unset($search['category']);

        if(isset($search['currency']) && $search['currency']==1){
            unset($search['currency']);
        }

        if(isset($search['country']) && $search['country']==1){
            unset($search['country']);
        }


        $routeParams['search'] = array_filter($search, function($value){
            if(empty($value)){
                return false;
            }

            return true;
        });

        if(isset($routeParams['transaction'])
            && isset($routeParams['type'])
            && isset($routeParams['locationKey'])
             && !isset($search['country'])
            && !empty($location->getCity())
            && empty($location->getSection())
        ) {
            $route = 'frontend_offer_search_subdomain';
            $routeParams['subdomain'] = $routeParams['transaction'] .
                '-'. $routeParams['type'] . '-'. $routeParams['locationKey'];

            unset($routeParams['type']);
            unset($routeParams['transaction']);
            unset($routeParams['locationKey']);
        }


        return $this->redirectToRoute($route, $routeParams);

    }
    public function listByUrlAction(){
        $request = $this->getRequest();
        $subdomain = $this->getCurrentSubdomain();

        $routeParams = $request->attributes->get('_route_params');
        $searchManager = $this->get('search.manager');
        $params = (!empty($request->query->has('search'))) ? $request->query->get('search') : [];

        unset($params['category']);
        unset($params['transactionType']);
        unset($params['locationIndexLike']);

        $settings = $this->get('settings.manager')->get(1);

        if(empty($subdomain)
            && isset($routeParams['transaction'])
            && isset($routeParams['type'])
            && isset($routeParams['locationKey']))
        {

            $location = $searchManager->findLocationByKey($routeParams['locationKey']);

            if(is_object($location) && !empty($location->getCity()) && empty($location->getSection()))
            {
                $transaction = SubdomainHelper::getTransactionByKey($routeParams['transaction']);
                $category = SubdomainHelper::getCategoryByKey($routeParams['type']);
                $locationKey = $routeParams['locationKey'];
                $subdomain = SubdomainHelper::prepareSubdomainFromParameters($category['id'], $transaction['id'], $locationKey);

                return $this->redirectToRoute('frontend_offer_search_subdomain',[
                    'subdomain'=>$subdomain,
                    'search'=>$params,
                    'mainImage'=> $this->get('myimage.manager')->getRandom(),
                    'h1TextColor'=>$settings->getH1TextColor(),
                    'h1Color'=>$settings->getH1Color(),
                ],301);
            }

        }

        if(!empty($subdomain) && SubdomainHelper::isOfferSubdomain($subdomain)) {

            $transaction = SubdomainHelper::getTransactionByKey($routeParams['transaction']);
            $category = SubdomainHelper::getCategoryByKey($routeParams['type']);
            $url = $this->getParameter('main_host');


            if(!empty($routeParams['locationKey']))
            {
                $url .= $this->generateUrl('frontend_offer_search_url_with_location',[
                    'transaction'=>$transaction['key'],
                    'type'=>$category['key'],
                    'locationKey'=>$routeParams['locationKey'],
                    'search'=>$params,
                    'mainImage'=> $this->get('myimage.manager')->getRandom(),
                    'h1TextColor'=>$settings->getH1TextColor(),
                    'h1Color'=>$settings->getH1Color(),
                ]);
                return $this->redirect($url,301);
            }

            $url .= $this->generateUrl('frontend_offer_search_url',[
                'transaction'=>$transaction['key'],
                'type'=>$category['key'],
                'search'=>$params,
                'mainImage'=> $this->get('myimage.manager')->getRandom(),
                'h1TextColor'=>$settings->getH1TextColor(),
                'h1Color'=>$settings->getH1Color(),
            ]);
            return $this->redirect($url,301);

        }

        $agent = null;
        $office = null;

        if(!empty($subdomain) && !SubdomainHelper::isOfferSubdomain($subdomain)){
            $office = $this->get('office.manager')->findBySubdomain($subdomain);
        }

        $form = $this->createForm(new AbuseContactType(),new AbuseContact());
        $searchManager = $this->get('search.manager');
        $offerManager = $this->get('offer.manager');

        if($request->isXmlHttpRequest()){
            return $this->render('AppOfferBundle:Offer:ajaxList.html.twig',array(
                                'paginator'=>$searchManager->paginator(),
                            ));
        }
        $searchParams = $searchManager->getSearchParams();

        $types = [];
        $sliceTypes = [];

        if(isset($searchParams['categoryId'])){
            $category = $offerManager->findCategoryById($searchParams['categoryId']);
            $types = $category->getTypes()->toArray();
            shuffle($types);
            $sliceTypes = array_slice($types, 0,6);
        }

        $query = $request->query->all();

        if(isset($query['search']['locationIndexLike'])) {
            $offer = $this->get('offer.manager')->getCurrentOffersBy([
                "transaction_type_id" => $query['search']['transactionType'],
                "category_id" => $query['search']['category'],
                "region" => $query['search']['locationIndexLike']
            ], ['id' => 'ASC'], 1);
        }

        unset($query['search']['transactionType']);
        unset($query['search']['category']);
        unset($query['search']['locationIndexLike']);
        unset($query['search']['office']);

        return $this->render('AppOfferBundle:Offer:list.html.twig',array(
                                'paginator'=>$searchManager->paginator(),
                                'reverseCategoryOffers'=>$offerManager->reverseCategoryOffers($request),
                                'office'=>$office,
                                'agent'=>$agent,
                                'form'=>$form->createView(),
                                'location'=>$searchManager->getLocation(),
                                'searchParams'=> $searchParams,
                                'categories'=>$offerManager->getAllCategories(),
                                'types'=>$types,
                                'sliceTypes'=>$sliceTypes,
                                'bestResults'=>$searchManager->getBestResults(12),
                                'query'=>$query,
                                'mainImage'=> $this->get('myimage.manager')->getRandom(),
                                'h1TextColor'=>$settings->getH1TextColor(),
                                'h1Color'=>$settings->getH1Color(),
        ));
    }

    public function listBySubdomainAction(){
        if($this->isDecommissionedTransactionType()) {
            return $this->redirectDecommissionedTransactionTypes();;
        }

        $request = $this->getRequest();

        $agent = null;
        $office = null;

        $form = $this->createForm(new AbuseContactType(),new AbuseContact());
        $searchManager = $this->get('search.manager');
        $offerManager = $this->get('offer.manager');

        if($request->isXmlHttpRequest()){
            return $this->render('AppOfferBundle:Offer:ajaxList.html.twig',array(
                                'paginator'=>$searchManager->paginator(),
                            ));
        }
        $searchParams = $searchManager->getSearchParams();

        $types = [];
        $sliceTypes = [];

        if(isset($searchParams['categoryId'])){
            $category = $offerManager->findCategoryById($searchParams['categoryId']);
            $types = $category->getTypes()->toArray();
            shuffle($types);
            $sliceTypes = array_slice($types, 0,6);
        }
        $query = $request->query->all();
        unset($query['search']['transactionType']);
        unset($query['search']['category']);
        unset($query['search']['locationIndexLike']);

        $settings = $this->get('settings.manager')->get(1);

        $ids = $this->get('offer_category_description.manager')->getIdByParams(
            $searchParams['transactionTypeId'],
            $searchParams['categoryId'],
            $searchParams['locationKey']
        );

        if(!empty($ids)){ $ids = $ids[0]['id']; }

        return $this->render('AppOfferBundle:Offer:list.html.twig',array(
                                'paginator'=>$searchManager->paginator(),
                                'reverseCategoryOffers'=>$offerManager->reverseCategoryOffers($request),
                                'office'=>$office,
                                'agent'=>$agent,
                                'form'=>$form->createView(),
                                'location'=>$searchManager->getLocation(),
                                'searchParams'=> $searchParams,
                                'categories'=>$offerManager->getAllCategories(),
                                'types'=>$types,
                                'sliceTypes'=>$sliceTypes,
                                'bestResults'=>$searchManager->getBestResults(12),
                                'query'=>$query,
                                'mainImage'=>$this->get('myimage.manager')->getRandom(),
                                'ids'=>$ids,
                                'images'=>$this->get('offer_category_description.manager')->getAllImagesByParams(
                                    $searchParams['transactionTypeId'],
                                    $searchParams['categoryId'],
                                    $searchParams['locationKey']
                                ),
                                'h1'=>$settings->getH1(),
                                'h1TextColor'=>$settings->getH1TextColor(),
                                'h1Color'=>$settings->getH1Color(),
        ));
    }
    /**
     * Show offers on map
     *
    */
    private function redirectDecommissionedTransactionTypes() {
        $request = $this->getRequest();
        $host = str_replace($this->container->getParameter('scheme') . '://','', $request->getSchemeAndHttpHost());

        $replace = [
            '/^kupno-(.*)$/'=>"sprzedaz-$1",
            '/^najem-(.*)$/' => "wynajem-$1"
        ];
        $redirectHost = preg_replace( array_keys( $replace ), array_values( $replace ), $host );
        return $this->redirect($this->container->getParameter('scheme') . '://' . $redirectHost, 301);
    }
    private function isDecommissionedTransactionType() {
        $request = $this->getRequest();
        $host = str_replace($this->container->getParameter('scheme') . '://','', $request->getSchemeAndHttpHost());
        if(!preg_match('/^(kupno|najem).*$/', $host)) {
            return false;
        }

        return true;
    }
    public function showOnMapAction(){
        $request = $this->getRequest();

        if($request->isXmlHttpRequest()){
            $searchManager = $this->get('search.manager');

            if(count($request->query->all())){
                $items = $searchManager->searchOnMap();
            }else {
                $items = array();
            }
            return new JsonResponse(array('success'=>true,'markers'=>$items));
        }


        return $this->render('AppOfferBundle:Offer:showOnMap.html.twig',array(
                            ));
    }
    /**
     * Show offer details on map
     *
    */
    public function detailsOnMapAction(){
        $request = $this->getRequest();

            $offerManager = $this->get('offer.manager');
            $offer = $offerManager->findById($request->get('id'));

            return $this->render('AppOfferBundle:Offer:detailsOnMap.html.twig',array(
                    'item'=>$offer));

    }
    /**
     * Show offer
     *
    */
    public function showAction(){
        $offerManager = $this->get('offer.manager');
        $offerLinkManager = $this->get('offer.link.manager');
        $offer = $offerManager->findById($this->getRequest()->get('id'),true);

        if(!is_object($offer)){
            return $this->redirectToRoute('frontend_offer_list', array(), 301);
        }

        $subdomain = $this->getRequest()->get('subdomain');

        if($offer->isArchivOffer() && $subdomain != 'archiwum')
        {
            return $this->redirectToRoute('frontend_offer_archive_subdomain',[
                'id'=>$offer->getId(),
                'offerName'=>UrlHelper::prepare($offer->getName()),
            ], 301);
        }

        if(!$offer->isArchivOffer() && !empty($subdomain) && empty($offer->getSubdomain()))
        {
            return $this->redirectToRoute('frontend_offer_show',[
                'id'=>$offer->getId(),
                'offerName'=>UrlHelper::prepare($offer->getName()),
                'domain'=>$this->getParameter('domain'),
            ], 301);
        }

        if( !$offer->isArchivOffer() && $this->getRequest()->get('subdomain') != $offer->getSubdomain())
        { 
            return $this->redirectToRoute('frontend_offer_subdomain',[
                'subdomain'=>$offer->getSubdomain(),
                'id'=>$offer->getId(),
                'offerName'=>UrlHelper::prepare($offer->getName()),
            ], 301);
        }

        $offerManager->incrementHits($offer->getId());
        $contact = new Contact();
        $contact->setOffer($offer);
        $contactForm = $this->createForm(new ContactType($this->getDoctrine()->getManager()),
                                         $contact);

        $citySubdomain = $offerManager->prepareSubdomain([
                'city'=>$offer->getCity(),
                'region'=>$offer->getRegion(),
                'district'=>$offer->getDistrict(),
                'category_id'=>$offer->getCategory()->getId(),
                'transaction_type_id'=>$offer->getTransactionType()->getId()
            ]
        );

        $categoryData = SubdomainHelper::getCategoryById($offer->getCategory()->getId());
        $transactionData = SubdomainHelper::getTransactionById($offer->getTransactionType()->getId());
        $view = 'show';

        if($this->get('request')->query->has('print')){
            $view = 'printShow';
        }

        return $this->render(sprintf('AppOfferBundle:Offer:%s.html.twig',$view),array(
                               'offer'=>$offer,
                               'similarOffers'=>$offerManager->findSimilar($offer),
                               'avgPricem2'=>$offerManager->getAvgPricem2($offer),
                               'currencies'=>$offerManager->getCurrencies(),
                               'contactForm'=>$contactForm->createView(),
                               'citySubdomain'=>$citySubdomain,
                               'categoryData'=>$categoryData,
                               'transactionData'=>$transactionData,
                               'links'=>$offerLinkManager->getAllByOffer($offer),
        ));
    }
    
    public function showPhoneAction()
	{
		$offerManager = $this->get('offer.manager');
		$offer = $offerManager->findById($this->getRequest()->get('id'),true);
		
		//
		if(!is_object($offer)){
			return $this->redirectToRoute('frontend_offer_list', array(), 301);
		}
		
		//
		$offer->increasesPhoneCounter();
		$em = $this->get('doctrine')->getManager();
		$em->persist($offer);
		$em->flush();
		
		//
		return $this->render(
			'AppOfferBundle:_Partials:unmaskedPhoneData.html.twig',array(
				'offer'=>$offer
			));
	}
    
    /**
     * Confirm offer
     *
    */
    public function confirmAction(){
        $offerManager = $this->get('offer.manager');
        $offer = $offerManager->findById($this->get('session')->get('newIdOffer'));
        $mailer = $this->get('mailer');
        $message = $mailer->createMessage()
            ->setSubject('Aktywacja ogłoszenia')
            ->setFrom(array($this->getParameter('mail_sender_email')=>$this->getParameter('mail_sender_name')))
            ->setTo($offer->getEmail())
            ->setBody(
                $this->renderView(
                    'AppOfferBundle:Emails:confirm.html.twig',
                    array('offer'=>$offer)
                ),
                'text/html'
            )
        ;

        $mailer->send($message);
        return $this->render('AppOfferBundle:Offer:confirm.html.twig',array(
                               'offer'=>$offer,
                               'currencies'=>$offerManager->getCurrencies(),
        ));
    }
    /**
     * Show payment options
     *
    */
    public function paymentAction(){
        $offerManager = $this->get('offer.manager');
        $subscriptionManager = $this->get('subscription_payment.manager');
        $request = $this->getRequest();
        $payment = new Payment();
        $paymentItem = new PaymentItem(PaymentItem::TYPE_PUBLICATION);
        $form = $this->createForm(new PaymentType(),$payment);

        if($request->get('code')){
            $offer = $offerManager->findByActivationCode($request->get('code'));
            $offer->setIsTmp(false);
            $offerManager->save($offer);
        }else {
             $offer = $offerManager->findById($request->get('id'));
              $this->denyAccessUnlessGranted('edit', $offer);
        }

        $payment->setOffer($offer);
        $paymentItem->setOffer($offer);
        $payment->setPromo(false);
        $payment->addPaymentItem($paymentItem);

        $response = $subscriptionManager->process($payment,'add');

        if($response['success']){
             $this->addFlash('success','Oferta została dodana.');
            return $this->redirect($response['redirectUrl']);
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
			$message = (new \Swift_Message('Nowe ogłoszenie WGN'))
				->setFrom($this->getParameter('mail_sender_email'))
				->setTo([$this->getParameter('mail_order_email'), $this->getParameter('mail_order_email_2')])
				->setBody(
					$this->renderView(
						'AppOfferBundle:Emails:paymentInfo.html.twig',
						array('payment'=>$payment)
					),
					'text/html'
				);
			$this->get('mailer')->send($message);

                $payment->addPromoItems();

                $result = $this->get('payment.manager')->process($payment,'add');
                if($result['success']){
                    return $this->redirect($result['redirectUrl']);
                }

                if($payment->getPaymentMethod()== Payment::TYPE_SMS){
                    $error = new FormError("Podany kod jest nieprawidłowy");
                    $form->get('smsCode')->addError($error);
                }
        }

        return $this->render('AppOfferBundle:Offer:payment.html.twig',array(
                               'offer'=>$offer,
                               'payment'=>$payment,
                               'form'=>$form->createView()
        ));
    }
    /**
     * Show payment success page
     *
    */
    public function paymentSuccessAction(){
        $paymentManager = $this->get('payment.manager');
        $payment = $paymentManager->findById($this->getRequest()->get('id'));
        return $this->render('AppOfferBundle:Offer:paymentSuccess.html.twig',array(
                               'offer'=>$payment->getOffer(),
        ));
    }
    /**
     * Send message from offer form
     *
    */
    public function sendMessageAction(){
        $request =  $this->getRequest();
        $contactData = new Contact();
        $contactData->setSubject('Zapytanie o ofertę');
        $form = $this->createForm(new ContactType($this->getDoctrine()->getManager()), $contactData);

        if($request->isXmlHttpRequest()) {
            $form->bind($request);
            if ($form->isValid()) {
            $messageManager = $this->get('message.manager');
            $mailer = $this->get('mailer');
            $to = $this->getParameter('mail_admin_email');

            if(!empty($contactData->getOffer()->getEmail())){
                $to = $contactData->getOffer()->getEmail();
            }elseif(is_object($contactData->getOffer()->getUser()) && !empty($contactData->getOffer()->getUser()->getEmail())){
                $to = $contactData->getOffer()->getUser()->getEmail();
            }elseif(is_object($contactData->getOffer()->getOffice()) && !empty($contactData->getOffer()->getOffice()->getEmail())){
                $to = $contactData->getOffer()->getOffice()->getEmail();
            }

            $message = $mailer->createMessage()
                ->setSubject('Zapytanie ze wgn.pl')
                ->setFrom(array($this->getParameter('mail_sender_email')=>$this->getParameter('mail_sender_name')))
                ->setTo($to)
                ->setReplyTo($contactData->getEmail())
                ->setBody(
                    $this->renderView(
                        'AppOfferBundle:Emails:ask.html.twig',
                        array('contactData'=>$contactData)
                    ),
                    'text/html'
                )
            ;

            $messageManager->sendUsingContactData($contactData);
            $mailer->send($message);
            return new JsonResponse(array('success'=>true));
            }

            return new JsonResponse(array('success'=>false));
        }
        throw $this->createAccessDeniedException('You cannot access this page!');
    }
    /**
     * Save looking for subscription
     *
    */
    public function resultsNotificationAction(){
        $request = $this->getRequest();
        $lookingFor = new LookingForNewsletter();

        parse_str($request->get('conditions'), $query);
        $lookingFor->setQuery($query['search']);
        $lookingFor->setEmail($request->get('email'));

        $this->get('newsletter.manager')->saveLookingFor($lookingFor);

        return new JsonResponse(array('success'=>true));
    }
    /**
     * Send looking for form
     *
    */
    public function lookingForAction(){

        $request = $this->getRequest();
        $lookingFor = new LookingFor();
        $form = $this->createForm(new LookingForType(),$lookingFor);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $mailer = $this->get('mailer');
            $message = $mailer->createMessage()
                ->setSubject('Zapytanie ze wgn.pl')
                ->setFrom(array($this->getParameter('mail_sender_email')=>$this->getParameter('mail_sender_email')))
                ->setTo($this->getParameter('lookingfor_email'))
                ->setBody(
                    $this->renderView(
                        'AppOfferBundle:Emails:lookingfor.html.twig',
                        array('data'=>$lookingFor)
                    ),
                    'text/html'
                )
            ;
            $mailer->send($message);
            $this->addFlash('success','Zgłoszenie zostało przyjęte. Dziękujemy.');
        }

        return $this->render('AppOfferBundle:Offer:lookingFor.html.twig',array(
                               'form'=>$form->createView(),
        ));
    }
    /**
     * Delete offer
     *
    */
    public function deleteAction() {

        $offerManager = $this->get('offer.manager');
        $ids = $this->getRequest()->get('offer');

        if(count($ids)===0){
             $this->addFlash('success','Nie wybrano żadnej oferty.');
             return $this->redirect($this->getRequest()->headers->get('referer'));
        }

        foreach($ids as $id){
            $offer = $offerManager->findById($id);
            $this->denyAccessUnlessGranted('delete', $offer);
            $offerManager->softDelete($offer);
        }


        $this->addFlash('success','Usunięto poprawnie.');

        return $this->redirect($this->getRequest()->headers->get('referer'));
    }
    /**
     * Promotion selected offers
     *
    */
    public function promoAction(){
        $offerManager = $this->get('offer.manager');
        $request = $this->getRequest();
        $payment = new Payment();
        $payment->setPromo(true);

        $ids = $this->getRequest()->get('offer');
        if(count($ids)===0){
             $this->addFlash('success','Nie wybrano żadnej oferty.');
             return $this->redirect($this->getRequest()->headers->get('referer'));
        }

        if(count($ids)==1){
            $form = $this->createForm(new PromoPaymentType(true),$payment);
        }else {
            $form = $this->createForm(new PromoPaymentType(false),$payment);
        }

        foreach($ids as $id){
            $offer = $offerManager->findById($id);
            $this->denyAccessUnlessGranted('edit', $offer);

            $paymentItem = new PaymentItem(PaymentItem::TYPE_PROMO);
            $paymentItem->setOffer($offer)
                        ->setValue(30.75)
            ;
            $payment->addPaymentItem($paymentItem);

        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
                $result = $this->get('payment.manager')->process($payment,'promo');
                if($result['success']){
                    return $this->redirect($result['redirectUrl']);
                }
                if($payment->getPaymentMethod()== Payment::TYPE_SMS){
                    $error = new FormError("Podany kod jest nieprawidłowy");
                    $form->get('smsCode')->addError($error);
                }
        }

        return $this->render('AppOfferBundle:Offer:promo.html.twig',array(
                               'payment'=>$payment,
                               'form'=>$form->createView()
        ));
    }
    /**
     * Renew selected offers
     *
    */
    public function renewAction() {
        $offerManager = $this->get('offer.manager');
        $subscriptionManager = $this->get('subscription_payment.manager');
        $request = $this->getRequest();
        $payment = new Payment();

        $ids = $this->getRequest()->get('offer');
        if(count($ids)===0){
             $this->addFlash('success','Nie wybrano żadnej oferty.');
             return $this->redirect($this->getRequest()->headers->get('referer'));
        }

        foreach($ids as $id){
            $offer = $offerManager->findById($id);
            $this->denyAccessUnlessGranted('edit', $offer);

            $paymentItem = new PaymentItem(PaymentItem::TYPE_PUBLICATION);
            $paymentItem->setOffer($offer);
            $payment->addPaymentItem($paymentItem);

        }
        $response = $subscriptionManager->process($payment,'renew');

        if($response['success']){
             $this->addFlash('success','Oferta została dodana.');
            return $this->redirect($response['redirectUrl']);
        }
        $canPaySms = (count($ids)==1)? true : false;
        $form = $this->createForm(new PaymentType($canPaySms),$payment);
        $form->remove('publication');
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
                $payment->addPromoItems();
                $result = $this->get('payment.manager')->process($payment,'renew');
                if($result['success']){
                    return $this->redirect($result['redirectUrl']);
                }

                if($payment->getPaymentMethod()== Payment::TYPE_SMS){
                    $error = new FormError("Podany kod jest nieprawidłowy");
                    $form->get('smsCode')->addError($error);
                }
        }

        return $this->render('AppOfferBundle:Offer:renew.html.twig',array(
                               'offer'=>$offer,
                               'payment'=>$payment,
                               'form'=>$form->createView()
        ));
    }
    /**
     * Activate offer
     *
    */
    public function activateAction(){
        $request = $this->getRequest();
        $offerManager = $this->get('offer.manager');
        $ids = $request->get('offer');
        if(count($ids)===0){
             $this->addFlash('success','Nie wybrano żadnej oferty.');
             return $this->redirect($this->getRequest()->headers->get('referer'));
        }
        $offer = $offerManager->findById(current($ids));
        $this->denyAccessUnlessGranted('edit', $offer);

        if($offer->getIsTmp()){
            $this->get('session')->set('newIdOffer',$offer->getId());
            return $this->redirect($this->generateUrl('frontend_offer_add_step_3'));
        }
    }
    /**
     * Send abuse message
     *
    */
    public function sendAbuseMessageAction(){
        $request =  $this->getRequest();
        $contactData = new AbuseContact();
        $form = $this->createForm(new AbuseContactType(),
                                  $contactData);
        $form->handleRequest($request);
        if($request->isXmlHttpRequest() &&
            $form->isSubmitted() && $form->isValid()) {
            try{
            $mailer = $this->get('mailer');
            $message = $mailer->createMessage()
                ->setSubject($contactData->getSubject())
                ->setFrom(array($this->getParameter('mail_sender_email')=>$this->getParameter('mail_sender_name')))
                ->setTo($this->getParameter('mail_admin_email'))
                ->setBody(
                    $this->renderView(
                        'AppUserBundle:Emails:ask.html.twig',
                        array('contactData'=>$contactData)
                    ),
                    'text/html'
                )
            ;

            $mailer->send($message);
            }catch(\Exception $ex){
                echo $ex->getMessage();
            }
            return new JsonResponse(array('success'=>true));
        }
        throw $this->createAccessDeniedException('You cannot access this page!');
    }
    /**
     * export offers to komercja24.pl
     *
    */
    public function offersToKomercjaAction() {
        $request = $this->get('request');

        if(!$request->query->has('qr')){
             return new JsonResponse(array('success'=>false,'message'=>'qr param required.'));
        }
        $signatures = explode(' ',$request->query->get('qr'));
        return new JsonResponse($this->get('offer.manager')
                                     ->findBySignatures($signatures));
    }

    private function getCurrentSubdomain(){
        $currentHost = $this->get('request')->getHttpHost();
        $baseHost = $this->getParameter('domain');

        $host = str_replace($baseHost, '', $currentHost);

        if(empty($host)){
            return null;
        }
        return trim($host,'.');
    }
}
