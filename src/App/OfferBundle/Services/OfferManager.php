<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Services;

use App\AppBundle\Component\SubdomainHelper;
use App\AppBundle\Component\UrlHelper;
use App\OfferBundle\Entity\OfferRepository;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\OfferBundle\Entity\Offer;
use App\OfferBundle\Model\BackOfficeSearch;
/**
 * Class OfferManager
 *
 * @author wojciech przygoda
 */
class OfferManager {
    /**
     *
     * @var Container services container
     */
    private $services;

    /**
     * Constructor
     *
     * @param Container $container services container
     */
    function __construct(Container $container) {
      $this->services = $container;
    }

    /**
     * Get all categories
     *
     * @param bool $public only published offers
     * @return Category[]
     */
    public function getAllCategories($public = false){
        $repo = $this->services
                     ->get('doctrine')
                     ->getManager()
                     ->getRepository('AppOfferBundle:Category')
                ;

        if($public){
            return $repo->findBy(array('isPublish'=>1));
        }

        return $repo->findAll();
    }
    public function getRandomOffers($limit){

        return  $this->services
                     ->get('doctrine')
                     ->getManager()
                     ->getRepository('AppOfferBundle:Offer')
                     ->findRandom($limit);
    }

    /**
     * @param Offer $offer
     * @param int $limit
     * @return array
     */
    public function getCurrentOffers($offer, $limit = 100)
    {
        /**
         * @var OfferRepository $repo
         */
        $repo = $this->services
            ->get('doctrine')
            ->getManager()
            ->getRepository('AppOfferBundle:Offer')
        ;

        return $repo->findCurrentOffers($offer, $limit);
    }

    /**
    +     * @param String $city
    +     * @param int $limit
    +     * @return array
    +     */
    public function getCurrentCityOffers(string $city, int $limit = 100)
    {
        /**
         * @var OfferRepository $repo
         */
        $repo = $this->services
            ->get('doctrine')
            ->getManager()
            ->getRepository('AppOfferBundle:Offer')
        ;

        return $repo->findCurrentCityOffers($city, $limit);
    }

    /**
     * @param String $province
     * @param int $limit
     * @return array
     */
    public function getCurrentProvinceOffers(string $province, int $limit = 100)
    {
        /**
         * @var OfferRepository $repo
         */
        $repo = $this->services
            ->get('doctrine')
            ->getManager()
            ->getRepository('AppOfferBundle:Offer')
        ;

        return $repo->findCurrentProvinceOffers($province, $limit);
    }

    /**
     * @param String $city
     * @param int $limit
     * @return array
     */
    public function getProvinceByCity(string $city)
    {
        $city = str_replace("-", " ", $city);
        $city = ucwords($city);

        /**
        * @var OfferRepository $repo
        */
        $repo = $this->services
            ->get('doctrine')
            ->getManager()
            ->getRepository('AppOfferBundle:Offer')
        ;

        $city = $repo->findProvinceByCity($city);

        return $city[0]['province'];
    }

    public function getCurrentOffersBy(
        $criteria,
        $order,
        $limit,
        $archived = false
    )
    {
        /**
         * @var OfferRepository $repo
         */
        $repo = $this->services
            ->get('doctrine')
            ->getManager()
            ->getRepository('AppOfferBundle:Offer')
        ;

        return $repo->findCurrentOffersBy(
            $criteria,
            $order,
            $limit,
            $archived
        );
    }

    /**
     * Get transaction types
     *
     * @param bool $public only published transaction types
     * @return TransactionType[]
     */
    public function getTransactionTypes($public = false){
        $repo = $this->services
                     ->get('doctrine')
                     ->getManager()
                     ->getRepository('AppOfferBundle:TransactionType')
                ;

        if($public){
            return $repo->findBy(array('isPublish'=>1));
        }

        return $repo->findAll();
    }
    /**
     * Find offer by id
     *
     * @param int $id offer id
     * @param bool $isPublish only published offer
     * @return Offer
     * @throws NotFoundHttpException
     */
    public function findById($id,$isPublish=false){
        $repo = $this->services
                     ->get('doctrine')
                     ->getManager()
                     ->getRepository('AppOfferBundle:Offer')
        ;

        if($isPublish){
            $offer = $repo->findOneBy(array('id'=>$id,'isDelete'=>0));
        }else {
            $offer = $repo->findOneById($id);
        }

        return $offer;
    }
    /**
     * Find all offer
     *
     * @param int $id offer id
     * @param bool $isPublish only published offer
     * @return Offer
     * @throws NotFoundHttpException
     */
    public function findAll(){
        $repo = $this->services
                     ->get('doctrine')
                     ->getManager()
                     ->getRepository('AppOfferBundle:Offer')
        ;
        return $repo->findAllAsArray();
    }
    /**
     * Find by import id
     *
     * @param int $id offer import id
     * @return Offer
     */
    public function findByImportId($id){
        $repo = $this->services
                     ->get('doctrine')
                     ->getManager()
                     ->getRepository('AppOfferBundle:Offer')
        ;

        return $repo->findOneByImportId($id);

    }
    /**
     * Find by signatures
     *
     * @param array $signature signatures
     * @return string
     */
    public function findBySignatures(array $signature){
        $repo = $this->services
                     ->get('doctrine')
                     ->getManager()
                     ->getRepository('AppOfferBundle:Offer')
        ;

        $result = $repo->findBySignatures($signature);

        $offers = array();

        foreach($result as $row){
            $name = UrlHelper::prepare($row->getName());
            $url = $this->services->get('request')->getSchemeAndHttpHost().
                   $this->services->get('router')
                        ->generate('frontend_offer_show',array(
                                                               'id'=>$row->getId(),
                                                               'offerName'=>$name,
                                                               ));
            $mainPhoto = $this->services->get('liip_imagine.cache.manager')
                         ->getBrowserPath('/uploads/offers/'.$row->getId().'/'.$row->getMainPhoto(),
                                          'offer_list_no_watermark', array());
            $offers[] = array(
                'foto'=>$mainPhoto,
                'sygnatura'=>$row->getSignature(),
                'cena'=>number_format($row->getPrice(),0, ',', ' ' ).' '.$row->getCurrency(),
                'lokalizacja'=>$row->getCity(),
                'kategoria'=>  ucfirst((string)$row->getType()),
                'url'=>$url,
                );
        }

        return $offers;
    }
    /**
     * Find by activation code
     *
     * @param string $code offer code
     * @return Offer
     * @throws NotFoundHttpException
     */
    public function findByActivationCode($code){
        $repo = $this->services
                     ->get('doctrine')
                     ->getManager()
                     ->getRepository('AppOfferBundle:Offer')
        ;

        $offer = $repo->findOneByActivationCode($code);


        if(!is_object($offer)){
            throw new NotFoundHttpException('Nie znaleziono!');
        }

        return $offer;
    }
    /**
     * Find similar
     *
     * @param Offer $offer offer
     * @return Offer[]
     */
    public function findSimilar(Offer $offer){
        $repo = $this->services
                     ->get('doctrine')
                     ->getManager()
                     ->getRepository('AppOfferBundle:Offer')
        ;
        return $repo->findSimilar($offer);
    }

    /**
     * Find random offers from subdomain
     *
     * @param $subdomain
     * @param int $limit
     * @return Offer[]
     */
    public function findRandomOfferFromSubdomain($subdomain, $limit = 9){
        $repo = $this->services
            ->get('doctrine')
            ->getManager()
            ->getRepository(Offer::class)
        ;
        return $repo->findRandomOffersFromSubdomain($subdomain,$limit);
    }
    public function findRandomOffersByParams($transactionId, $categoryId, $limit = 9){
        $repo = $this->services
            ->get('doctrine')
            ->getManager()
            ->getRepository(Offer::class)
        ;
        return $repo->findRandomOffersByParams($transactionId, $categoryId, $limit);
    }
    /**
     * Find type by id
     *
     * @param int $id type id
     * @param bool $isPublish only published types
     * @return Type
     * @throws NotFoundHttpException
     */
    public function findTypeById($id,$isPublish=false){
        $repo = $this->services
                     ->get('doctrine')
                     ->getManager()
                     ->getRepository('AppOfferBundle:Type')
        ;

        if($isPublish){
            $type = $repo->findOnePublished($id);
        }else {
            $type = $repo->findOneById($id);
        }

        if(!is_object($type)){
            throw new NotFoundHttpException('Nie znaleziono!');
        }

        return $type;
    }
    public function prepareSubdomain($data)
    {
        $searchManager = $this->services->get('search.manager');
        $query = $data['region'];

        if(empty($data['region']) || empty($data['city']))
        {
            return '';
        }
        if($data['district']!='' && strpos($data['district'], 'grodzki') === false)
        {
            $query .= ', ' .  $data['district'];
        }

        $query .= ', ' . $data['city'];

        $location = $searchManager->findClosestLocation($query);

        if(!$location)
        {
            return '';
        }

        if(strtolower($location['city']) != strtolower($data['city']))
        {
            return '';
        }

        return SubdomainHelper::prepareSubdomainFromParameters($data['category_id'],$data['transaction_type_id'],$location['uniqueKey']);
    }
    /**
     * Save offer
     *
     * @param Offer $offer offer
     */
    public function save(Offer $offer){
        $em = $this->services->get('doctrine')->getManager();
        $subdomain = $this->prepareSubdomain([
            'category_id'=>$offer->getCategory()->getId(),
            'transaction_type_id'=>$offer->getTransactionType()->getId(),
            'region'=>$offer->getRegion(),
            'district'=>$offer->getDistrict(),
            'city'=>$offer->getCity(),
        ]);
        $currency = $offer->getCurrency();
        $price = $offer->getPrice();
        $pricem2 = $offer->getPricem2();
        $offer->setPriceDef($price*$currency->getExchangeRate());
        $offer->setPriceDefm2($pricem2*$currency->getExchangeRate());
        $offer->setSubdomain($subdomain);
        $em->persist($offer);
        $em->flush();
    }
    /**
     * Find transaction type by id
     *
     * @param int $id transaction type id
     * @param bool $isPublish only published transaction type
     * @return TransactionType
     * @throws NotFoundHttpException
     */
    public function findTransactionTypeById($id,$isPublish=false){
        $repo = $this->services
                     ->get('doctrine')
                     ->getManager()
                     ->getRepository('AppOfferBundle:TransactionType')
        ;

        if($isPublish){
            $type = $repo->findOnePublished($id);
        }else {
            $type = $repo->findOneById($id);
        }

        if(!is_object($type)){
            throw new NotFoundHttpException('Nie znaleziono!');
        }

        return $type;
    }
    /**
     * Find category by id
     *
     * @param int $id category id
     * @return Category
     * @throws NotFoundHttpException
     */
    public function findCategoryById($id){
        $repo = $this->services
                     ->get('doctrine')
                     ->getManager()
                     ->getRepository('AppOfferBundle:Category')
        ;

        $type = $repo->findOneById($id);


        if(!is_object($type)){
            throw new NotFoundHttpException('Nie znaleziono!');
        }

        return $type;
    }
    /**
     * Find category by unique key
     *
     * @param string $key key
     * @return Category
     * @throws NotFoundHttpException
     */
    public function findCategoryByUniqueKey($key){
        $repo = $this->services
                     ->get('doctrine')
                     ->getManager()
                     ->getRepository('AppOfferBundle:Category')
        ;

        $type = $repo->findOneByUniqueKey($key);


        if(!is_object($type)){
            throw new NotFoundHttpException('Nie znaleziono!');
        }

        return $type;
    }
    /**
     * Increment hits
     *
     * @param int $id offer id
     */
    public function incrementHits($id){
        $repo = $this->services
                     ->get('doctrine')
                     ->getManager()
                     ->getRepository('AppOfferBundle:Offer');

        $repo->incrementHits($id);
    }
    /**
     * Update offers Price
     */
    public function updatePrices(){
        $repo = $this->services
                     ->get('doctrine')
                     ->getManager()
                     ->getRepository('AppOfferBundle:Offer');

        $repo->updatePrices();
    }
    /**
     * Add offer
     * @param Offer $offer offer
     * @throws \Exception
     */
    public function add($offer){
        $em = $this->services->get('doctrine')->getManager();
        $em->getConnection()->beginTransaction();

        try {
            $this->save($offer);

            $em->getRepository('AppOfferBundle:OfferImage')
               ->updateOfferId($offer->getTmpId(),$offer->getId());
            $em->refresh($offer);
            $offer->setFirstAsMainPhoto();
            $this->save($offer);
            $em->getConnection()->commit();
        }catch(\Exception $e){
            $em->getConnection()->rollback();
            throw $e;
        }
    }
    /**
     * Remove offer
     *
     * @param int $id offer id
     * @throws \Exception
     */
    public function remove($id){
        $em = $this->services->get('doctrine')->getManager();
        $em->getConnection()->beginTransaction();

        try {
            $image = $this->findById($id);
            $em->remove(image);
            $em->flush();

            $em->getRepository('AppOfferBundle:Offer')
               ->updateOrderingAfterDelete($image->getOrdering(),
                                           $image->getCategory()->getId());
            $em->getConnection()->commit();
        }catch(\Exception $e){
            $em->getConnection()->rollback();
            throw $e;
        }
    }
    /**
     * Save image
     * @param OfferImage $image offe image
     * @param bool $tmp is tmp offer
     * @throws Exception
     */
    public function saveImage($image,$tmp = true)
    {
        $em = $this->services->get('doctrine')->getManager();
        $repo = $em->getRepository('AppOfferBundle:OfferImage');
        $em->getConnection()->beginTransaction();
        try {
            if($tmp){
                $idOffer = $image->getTmpIdOffer();
                $image->setOrdering($repo->getMaxOrdering($idOffer)+1);
                $em->persist($image);
                $em->flush();
            }else {
                $image->setOrdering($repo->getMaxOrdering($image->getOffer()->getId(),
                                                          false)+1);
                $em->persist($image);
                $em->flush();

                $offer = $image->getOffer();
                $offer->setFirstAsMainPhoto();
                $this->save($offer);
            }

            $em->getConnection()->commit();
        } catch (\Exception $e) {

            $em->getConnection()->rollback();
            throw $e;
        }
    }
    /**
     * Find all offer images
     *
     * @param int $id offer id
     * @return type
     */
    public function findAllOfferImages(int $id){
        $em = $this->services->get('doctrine')->getManager();

        $query = $em->createQueryBuilder('i')
            ->select('i.name')
            ->from('AppOfferBundle:OfferImage', 'i')
            ->where("i.offer = :id")
            ->setParameter('id', $id)
            ->getQuery()
        ;

        return $query->getResult();
    }

    /**
     * Fild add offer images
     *
     * @param int $id offer id
     * @return type
     */
    public function findAllImages($id){
        $em = $this->services->get('doctrine')->getManager();
        $repo = $em->getRepository('AppOfferBundle:OfferImage');
        $query = $repo->createQueryBuilder('i')
                          ->join('i.article', 'o')
                          ->where('o.id = :id')
                          ->orderBy('i.ordering','ASC')
                          ->setParameter('id', $id)
                          ->getQuery();

        return $query->getResult();
    }
    /**
     * Find image
     *
     * @param int $id offer image id
     * @return OfferImage
     */
    public function findImage($id)
    {
        $repo = $this->services->get('doctrine')->getManager()
                     ->getRepository('AppOfferBundle:OfferImage');
        return $repo->findOneById($id);
    }
    /**
     * Remove image
     *
     * @param int $id offer image id
     * @param bool $tmp is tmp offer
     * @return boolean
     * @throws Exception
     */
    public function removeImage($id,$tmp=true){
        $em = $this->services->get('doctrine')->getManager();
        $repo = $em->getRepository('AppOfferBundle:OfferImage');
        $em->getConnection()->beginTransaction();
        try {
            $image = $this->findImage($id);

            $em->remove($image);
            $em->flush();

            if($tmp){
                $idOffer = $image->getTmpIdOffer();
                $repo->updateOrderingAfterDelete($image->getOrdering(),
                                                 $image->getTmpIdOffer());
            }else {
                $idOffer = $image->getOffer()->getId();
                $repo->updateOrderingAfterDelete($image->getOrdering(),
                                                 $image->getOffer()->getId(),
                                                 false
                        );
                $offer = $image->getOffer();
                $offer->setFirstAsMainPhoto();
                $this->save($offer);
            }

            $em->getConnection()->commit();
        } catch (Exception $e) {

            $em->getConnection()->rollback();
            throw $e;
        }
        return true;
    }
    /**
     * Update Images ordering
     *
     * @param int $idOffer offer id
     * @param array $ids image ids
     * @param bool $tmp is temp offer
     *
     * @throws Exception
     */
    public function updateImagesOrdering($idOffer, $ids,$tmp=true){
        $em = $this->services->get('doctrine')->getManager();
        $repo = $em->getRepository('AppOfferBundle:OfferImage');
        $em->getConnection()->beginTransaction();
        try {
            foreach($ids as $key=>$value){
              $repo->updateOrdering($value, $key+1);
            }
            if(!$tmp){
                $offer = $this->findById($idOffer);
                $offer->setFirstAsMainPhoto();
                $this->save($offer);
            }
            $em->getConnection()->commit();
        } catch (\Exception $e) {
            $em->getConnection()->rollback();
            throw $e;
        }
    }
    /**
     * Get offers by user width pagination
     * @return Paginator
     */
    public function getByUserWidthPagination(){
        $userManager = $this->services->get('user.manager');
        $user = $userManager->getCurrentLogged();
        $request = $this->services->get('request');
        $repo = $this->services->get('doctrine')->getRepository('AppOfferBundle:Offer');
        $qb = $repo->createQueryBuilder('o')
                      ->join('o.user', 'u')
                      ->join('o.currency','c')
                      ->where('u.id = :id')
                      ->andWhere('o.isDelete != 1')
                      ->setParameter('id',$user->getId())
                      ->orderBy('o.createDate','DESC');

        switch($request->query->get('type','active')){
            case 'unactive':
                $qb->andWhere('o.isPublish = 0 OR o.isPublish = 1')
                   ->andWhere('o.expireDate <= :now')
                   ->setParameter('now', new \DateTime());
            break;
            case 'waiting':
            $qb->andWhere('o.isPublish = 0');
            break;

            case 'active':
            default:
            $qb->andWhere('o.isPublish = 1')
               ->andWhere('o.expireDate >= :now')
               ->setParameter('now', new \DateTime());
            break;
        }
       if($request->query->get('offers') == 'inet'){
           $qb->andWhere('o.importId IS NOT NULL');
       }else {
           $qb->andWhere('o.importId IS NULL');
       }
       if($request->query->has('signature')){
           $qb->andWhere('o.signature LIKE :signature')
               ->setParameter('signature', '%'.$request->query->get('signature').'%');
        }
        $permitedColumns = array('modifyDate','priceDef','priceDefm2','squere');
        $permitedDirs = array('ASC','DESC');

        $orderParts = explode('_',$request->query->get('order','modifyDate_DESC'));

        if(in_array($orderParts[0], $permitedColumns) &&
                in_array($orderParts[1], $permitedDirs)){

            $qb->orderBy('o.'.$orderParts[0],$orderParts[1]);
        }

        $paginator  = $this->services->get('knp_paginator');

        return $paginator->paginate(
            $qb->getQuery(),
            $request->query->get('page', 1)/*page number*/,
            $request->query->get('pp', 20)/*limit per page*/
        );
    }
    /**
     * Update image ordering
     *
     * @param int $id image id
     * @param int $ordering ordering
     */
    public function updateImageOrdering($id, $ordering){
        $em = $this->services->get('doctrine')->getManager();
        $repo = $em->getRepository('AppOfferBundle:OfferImage');
        $repo->updateOrdering($id, $ordering);
    }
    /**
     * Get currencies
     *
     * @return Currency[]
     */
    public function getCurrencies(){
        return $this->services->get('doctrine')->getManager()
                    ->getRepository('AppOfferBundle:Currency')
                    ->findAll();
    }
    /**
     * Find currency by code
     * @param string $isoCode currency iso code
     * @return Currency
     */
    public function findCurrencyByCode($isoCode){
        return $this->services->get('doctrine')->getManager()
                    ->getRepository('AppOfferBundle:Currency')
                    ->findOneByIsoCode($isoCode);
    }
    /**
     * Get width pagination
     * @param BackOfficeSearch $backOfficeSearch search parameters
     * @param bool $all get all
     * @return Paginator
     */
    public function getWidthPagination(BackOfficeSearch $backOfficeSearch,$all=false){
        $request = $this->services->get('request');
        $repo = $this->services->get('doctrine')->getRepository('AppOfferBundle:Offer');
        $qb = $repo->createQueryBuilder('o')
                   ->leftJoin('o.user', 'u')
                   ->leftJoin('o.transactionType', 't')
                   ->leftJoin('o.category', 'c')
                   ->where('o.isDelete=0')
                   ->orderBy('o.createDate','DESC')
                ;

        if($backOfficeSearch->getSignature()){
           $qb->andWhere('o.signature LIKE :signature')
               ->setParameter('signature', '%'.$backOfficeSearch->getSignature().'%');
        }
        if($backOfficeSearch->getName()){
           $qb->andWhere('o.contactPerson LIKE :name')
               ->setParameter('name', '%'.$backOfficeSearch->getName().'%');
        }
        if($backOfficeSearch->getTransactionType()){
           $qb->andWhere('t.id = :transaction')
               ->setParameter('transaction', $backOfficeSearch->getTransactionType());
        }
        if($backOfficeSearch->getCategory()){
           $qb->andWhere('c.id = :category')
               ->setParameter('category', $backOfficeSearch->getCategory());
        }

        if(!is_null($backOfficeSearch->getIsPublish())){
            if($backOfficeSearch->getIsPublish()=='0'){
                 $qb->andWhere('o.isPublish = 0 OR o.expireDate < :now')
                    ->setParameter('now', new \DateTime());
            }else {
                 $qb->andWhere('o.isPublish = 1 AND o.expireDate > :now')
                    ->setParameter('now', new \DateTime());
            }
        }
        if($backOfficeSearch->getDateFrom()){
            $date = \DateTime::createFromFormat('Y-m-d', $backOfficeSearch->getDateFrom());
            $date->setTime(00,00,00);
            $qb->andWhere('o.createDate >= :dateFrom')
               ->setParameter('dateFrom', $date);
        }
        if($backOfficeSearch->getDateTo()){
            $date = \DateTime::createFromFormat('Y-m-d', $backOfficeSearch->getDateTo());
            $date->setTime(23,59,59);
            $qb->andWhere('o.createDate <= :dateTo')
               ->setParameter('dateTo', $date);
        }
        if($all){
            return $qb->getQuery()->getResult();
        }
        $paginator  = $this->services->get('knp_paginator');

        return $paginator->paginate(
            $qb->getQuery(),
            $request->query->get('page', 1)/*page number*/,
            $request->query->get('pp', 25)/*limit per page*/
        );
    }
    /**
     * Change offer publication state
     *
     * @param int $id offer id
     * @param bool $publish publication state
     */
    public function changePublish($id, $publish){

        $em = $this->services->get('doctrine')->getManager();

        $offer = $this->findById($id);
        $offer->setIsPublish($publish);
        $em->persist($offer);
        $em->flush();
    }
    /**
     * Soft delete
     *
     * @param Offer $offer offer
     */
    public function softDelete($offer){

        $em = $this->services->get('doctrine')->getManager();

        $offer->setIsDelete(true);
        $em->persist($offer);
        $em->flush();
    }
    /**
     * Find avg price m2
     * @param Offer $offer offer
     * @return float
     */
    public function getAvgPricem2(Offer $offer){
        $cache = $this->services->get('doctrine_cache.providers.main_cache');
        $cacheKey = "avg_prive_m2_offer_id_".$offer->getId();
        $avgPriceM2 = $cache->fetch($cacheKey);
        if ($avgPriceM2 === false) {
            $avgPriceM2 = $this->services
                ->get('doctrine')
                ->getManager()
                ->getRepository('AppOfferBundle:Offer')
                ->findAvgPricem2($offer)
            ;
            $cache->save($cacheKey, $avgPriceM2 , (60*60*24)+rand(1,120));
        }


        return $avgPriceM2;
    }
    /**
     * Get promo offers by office id
     *
     * @param int $id office id
     * @param int $limit  limit offers
     * @return Offer[]
     */
    public function getPromoByOfficeId($id,$limit=26){
         $repo = $this->services
                     ->get('doctrine')
                     ->getManager()
                     ->getRepository('AppOfferBundle:Offer');

         return $repo->findPromoOffersByOfficeId($id,$limit);
    }
    /**
     * Get special by office id
     *
     * @param int $id office id
     * @param int $limit limit offers
     * @return Offer[]
     */
    public function getSpecialByOfficeId($id,$limit=26){
         $repo = $this->services
                     ->get('doctrine')
                     ->getManager()
                     ->getRepository('AppOfferBundle:Offer');

         return $repo->findSpecialOffersByOfficeId($id,$limit);
    }
    /**
     * Count offers
     *
     * @param bool $active only active offers
     * @return Offer[]
     */
    public function countOffers($active) {
        $repo = $this->services
                     ->get('doctrine')
                     ->getManager()
                     ->getRepository('AppOfferBundle:Offer');

        return $repo->countOffers($active);
    }


    public function reverseCategoryOffers(Request $request)
    {

        if($request->query->has('search_mobile')){
            $search = $request->query->get('search_mobile');
        }else {
            $search = $request->query->get('search');
        }
        $location = null;

        if(isset($search['locationIndexLike'])){
            $location = $this->services->get('search.manager')
                ->findLocationByName($search['locationIndexLike']);
        }

        if(!isset($search['transactionType']) || !isset($search['category'])){
            return;
        }
        $reverseTransactionTypes = [
                1=>3,
                2=>4,
                3=>1,
                4=>2,
        ];
        $repo = $this->services
            ->get('doctrine')
            ->getManager()
            ->getRepository('AppOfferBundle:Offer');

        return $repo->findReverseCategoryOffers($search['category'],$reverseTransactionTypes[$search['transactionType']],$location);

    }
}

