<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Entity;
use App\OfferBundle\Entity\Offer;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

/**
 * Class OfferRepository
 *
 * @author wojciech przygoda
 */
class OfferRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     *
     * @var string sql query
     */
    protected $sqlQuery;

    /**
     *
     * @var int currency id
     */
    protected $currencyId;

    /**
     *
     * @var string location
     */
    protected $location;

    /**
     *
     * @var int distance
     */
    protected $distance;

    /**
     * Search offers
     *
     * @param int $start
     * @param int $length
     * @param array $filters
     * @param array $order
     * @return array
     */
    public function search($start = 0, $length = 20,$filters = array(),$order=array()){
        $em = $this->getEntityManager();
        $now = new \DateTime();
        $this->sqlQuery = 'SELECT o,co,ma,tr,cu,ca FROM AppOfferBundle:Offer o';
        $this->sqlQuery .= ' LEFT JOIN o.country co  LEFT JOIN o.market ma  LEFT JOIN o.transactionType tr ';
        $this->sqlQuery .= ' LEFT JOIN o.technicalCondition te  LEFT JOIN o.roof ro LEFT JOIN o.neighborhood ne ';
        $this->sqlQuery .= '  LEFT JOIN o.currency cu  LEFT JOIN o.category ca  LEFT JOIN o.additionalInfo ad';
        $this->sqlQuery .= '  LEFT JOIN o.user us  LEFT JOIN o.office uo LEFT JOIN o.type ty  LEFT JOIN o.media me ';
        $this->sqlQuery .= ' WHERE o.isPublish=1 AND o.expireDate >= :now AND o.isDelete = 0';
        $this->sqlQuery .= ' AND (o.isPromo=0 OR (o.isPromo=1 AND o.promoExpire <= :now))';
        $this->addFilterFileds($filters);
        $this->sqlQuery .= ' GROUP BY o.id';
        $this->addOrder($order);
        $query = $this->addFiltersValue($em->createQuery($this->sqlQuery),$filters);
        $query = $this->addLocationIndexValues($query);
        $query->setParameter('now', $now->format('Y-m-d'));
        $query->setFirstResult($start);
        $query->setMaxResults($length);

        return $query->getArrayResult();
    }
    /**
     * Search promo offers
     *
     * @param int $start start
     * @param int $length length
     * @param array $filters filters
     * @param array $order order
     * @return array
     */
    public function searchPromo($start = 0, $length = 20,$filters = array(),$order=array()){
        $em = $this->getEntityManager();
        $now = new \DateTime();
        $this->sqlQuery = 'SELECT o,co,ma,tr,cu,ca FROM AppOfferBundle:Offer o';
        $this->sqlQuery .= ' LEFT JOIN o.country co  LEFT JOIN o.market ma  LEFT JOIN o.transactionType tr ';
        $this->sqlQuery .= ' LEFT JOIN o.technicalCondition te  LEFT JOIN o.roof ro LEFT JOIN o.neighborhood ne ';
        $this->sqlQuery .= '  LEFT JOIN o.currency cu  LEFT JOIN o.category ca  LEFT JOIN o.additionalInfo ad ';
        $this->sqlQuery .= '  LEFT JOIN o.user us  LEFT JOIN o.office uo LEFT JOIN o.type ty  LEFT JOIN o.media me ';
        $this->sqlQuery .= ' WHERE o.isPublish=1 AND o.expireDate >= :now  AND o.isDelete = 0';
        $this->sqlQuery .= ' AND (o.isPromo=1 AND o.promoExpire >= :now)';
        $this->addFilterFileds($filters);
        $this->sqlQuery .= ' GROUP BY o.id';
        $this->addOrder($order);
        $query = $this->addFiltersValue($em->createQuery($this->sqlQuery),$filters);
        $query = $this->addLocationIndexValues($query);
        $query->setParameter('now', $now->format('Y-m-d'));
        $query->setFirstResult($start);
        $query->setMaxResults($length);

        return $query->getArrayResult();
    }
    /**
     * Add order parameters
     *
     * @param array $order order columns
     */
    public function addOrder($order){
        $orderSql = '';

        if(count($order)==0){
            return ;
        }

        $orderSql .= ' ORDER BY ';

        foreach($order as $key=>$value){
            $orderSql .= $key . ' ' .$value;

        }
        $this->sqlQuery .= $orderSql;
    }

    /**
     * Add filter fields
     *
     * @param array $filters filters
     * @return string
     */
    function addFilterFileds($filters){
        $sql = '';

        foreach($filters as $key=>$filter){

            if($filter['field']=='o.currency'){
                $this->currencyId = $filter['value'];
                continue;
            }
            if($filter['field']=='o.locationIndex' && $filter['value']!=''){

                $sql .= $this->addLocationIndexFields($filter['value'],$filters);
                unset($filters[$key]);
                continue;
            }
            if($filter['field']=='o.distance'){
                continue;
            }
            $field = $this->transformField($filter['field']);
            $filedVar = $field.$this->getConditionName($filter['condition']);


            if($filter['condition'] =='LIKE'){
                $words =  $this->prepereLikeKeyWords($filter);
                for($i=0; $i<count($words);$i++){
                    $sql .= ' AND (translate(' . $field . ') ' .$filter['condition'].  ' ';
                    $sql .= ':'.str_replace('.', '', $filedVar).$i;
                    $sql .= ' OR ' . $field . ' ' .$filter['condition'].  ' ';
                    $sql .= ':'.str_replace('.', '', $filedVar).$i.' )';
                }

            }else {
                $sql .= ' AND ' . $field . ' ' .$filter['condition'].  ' ';
            }

            switch($filter['condition']){
                case 'IN':
                    $sql .= '(:'.  str_replace('.', '', $filedVar).')';
                break;
                case 'IS NOT NULL':
                case 'LIKE':
                break;
                default:
                    $sql .= ':'.  str_replace('.', '', $filedVar);
                break;
            }
        }

        $this->sqlQuery .= $sql;
    }

    /**
     * Get distance
     *
     * @param array $filters filters
     * @return int
     */
    private function getDistance($filters){
        foreach($filters as $key=>$filter){
            if($filter['field']=='o.distance'){
                return $filter['value'];
            }
        }

        return 0;
    }

    /**
     * Add location index fields
     * @param string $location location
     * @param array $filters filters
     * @return string
     */
    function addLocationIndexFields($location,$filters){
        $this->location = [];
        $query = str_replace(array('',';'),', ',trim($location));

        $em = $this->getEntityManager();

        $this->distance = $this->getDistance($filters);

        $sqlQuery = 'SELECT l.name, l.province,l.city,l.section,l.subsection, l.lat, l.lng FROM AppOfferBundle:LocationAutocomplete l';
        $sqlQuery .= ' WHERE translate(l.name) LIKE :query OR l.name LIKE :query';
        $qb = $em->createQuery($sqlQuery);
        $qb->setParameter('query', $query);
        $qb->setMaxResults(1);

        $result = $qb->getOneOrNullResult();

        if(!$result){
            $sqlQuery = "SELECT l.name,l.province,l.city,l.section,l.subsection, l.lat, l.lng, MATCH_AGAINST(l.name, :query) AS score  FROM AppOfferBundle:LocationAutocomplete l WHERE MATCH_AGAINST(l.name, :query) > 0 ORDER BY score DESC";
            $qb = $em->createQuery($sqlQuery);
            $qb->setParameter('query', $location);
            $qb->setMaxResults(1);

            $result = $qb->getOneOrNullResult();

        }
        $sql ='';
        if($result){
            $sql .= ' AND (( 1=1';
            $this->location = $result;
            if(isset($result['province']) && $result['province']!=''){
                $sql .= ' AND (translate(o.region) LIKE :region ';
                $sql .= ' OR o.region LIKE :region )';
            }
            if(isset($result['city']) && $result['city']!=''){
                $sql .= ' AND (translate(o.city) LIKE  ';
                    $sql .= ':city';
                    $sql .= ' OR o.city LIKE ';
                    $sql .= ':city)';
            }
            if((isset($result['section']) && $result['section']!='') || (isset($result['subsection']) && $result['subsection']!='')){
                $sql .= ' AND (translate(o.street) LIKE  ';
                    $sql .= ':street';
                    $sql .= ' OR o.street LIKE ';
                    $sql .= ':street )';
            }

            $sql .= ') ';

            if($this->distance>0 && isset($this->location['lat']) && isset($this->location['lng'])){
                $sql .= ' OR (';
                //$sql .=' 6368 * ACOS(SIN(RADIANS(o.lat)) * SIN(RADIANS(:lat)) + COS(RADIANS(o.lat)) * COS(RADIANS(:lat)) * COS(RADIANS(:lng) - RADIANS(o.lng))) * 1.609344 <= :distance';

                $sql .= ' 6371 * 2 * ASIN(SQRT( POWER(SIN((:lat - abs(o.lat)) * 3.14/180 / 2), 2) + COS(:lat * 3.14/180 ) * COS(abs(o.lat) * 3.14/180) * POWER(SIN((:lng - o.lng) * 3.14/180 / 2), 2) )) <= :distance ';
                $sql .= ' )';
            }
            $sql .= ' )';
        }else {
            $this->location['title'] = $query;
            $sql .= ' AND (translate(o.name)  LIKE ';
                    $sql .= ':title';
                    $sql .= ' OR o.name LIKE ';
                    $sql .= ':title )';
        }
        return $sql;
    }
    /**
     * Add location index values
     *
     * @param type $qb query builder
     * @return QueryBuilder
     */
    function addLocationIndexValues($qb){
            if(isset($this->location['province']) && $this->location['province']!=''){
                $qb->setParameter('region', $this->location['province']);
            }
            if(isset($this->location['city']) && $this->location['city']!=''){
                $qb->setParameter('city', $this->location['city']);
            }
            if(isset($this->location['section']) && $this->location['section']!=''){
                $qb->setParameter('street', '%'.$this->location['section'].'%');
            }
            if(isset($this->location['title'])&& $this->location['title']!=''){
                $qb->setParameter('title', '%'.$this->location['title'].'%');

            }
            if(isset($this->location['subsection'])&& $this->location['subsection']!=''){
                $qb->setParameter('street', $this->location['subsection'].'%');
            }
            if($this->distance>0 && isset($this->location['lat']) && isset($this->location['lng'])){
                $qb->setParameter('lat', $this->location['lat']);
                $qb->setParameter('lng', $this->location['lng']);
                $qb->setParameter('distance', $this->distance);
            }
            return $qb;
    }
    /**
     *
     * Transform field
     *
     * @param string $field field
     * @return string
     */
    function transformField($field){
        switch($field){
            case 'o.office':
                $field = 'uo.id';
            break;

            case 'o.user':
                $field = 'us.id';
            break;
        }

        return $field;
    }

    /**
     * Prepere like key words
     * @param array $filter filter
     * @return QueryBuilder
     */
    function prepereLikeKeyWords($filter){
        $keywords = str_replace(' ', ',', $filter['value']);
        return explode(',',$keywords);
    }
    /**
     * Add filters value
     *
     * @param QueryBuilder $qb
     * @param array $filters filters
     * @return QueryBuilder
     */
    function addFiltersValue($qb, $filters){
        foreach($filters as $key => $filter){
            if($filter['field']=='o.currency'){
                $this->currencyId = $filter['value'];
                continue;
            }
            if($filter['field']=='o.locationIndex'){
                continue;
            }
            if($filter['field']=='o.distance'){
                continue;
            }
            $condition = $this->getConditionName($filter['condition']);
            $paramName = str_replace('.', '', $this->transformField($filter['field']).$condition);
            switch($condition){
                case 'like':
                    $words =  $this->prepereLikeKeyWords($filter);
                    for($i=0; $i<count($words);$i++){
                        $qb->setParameter($paramName.$i, '%'.$words[$i].'%');
                    }
                break;
                case 'is not null':
                break;
                default:
                    $qb->setParameter($paramName, $this->calculatepPrice($filter, $filter['value']));
                break;
            }
        }
        return $qb;
    }
    /**
     * Calculate Price
     *
     * @param array $filter filter
     * @param float $value value
     * @return float
     */
    function calculatepPrice($filter,$value){
        $priceFields = array('o.priceDef','o.priceDefm2');


        if(in_array($filter['field'],$priceFields)){
            $id = ($this->currencyId) ? $this->currencyId : 1;
            $currency = $this->getEntityManager()
                                ->getRepository('AppOfferBundle:Currency')
                                ->findOneById($id);

            return (float)$value * $currency->getExchangeRate();
        }

        return $value;
    }

    /**
     * Get condition name
     *
     * @param string $condition
     * @return string
     */
    private function getConditionName($condition){
        if(preg_match('/>=/',$condition)){
            return 'from';
        }
        if(preg_match('/<=/',$condition)){
            return 'to';
        }
        if(preg_match('/LIKE/',$condition)){
            return 'like';
        }
        if(preg_match('/IN/',$condition)){
            return 'in';
        }
        if(preg_match('/IS NOT NULL/',$condition)){
            return 'is not null';
        }
        return '';
    }
    /**
     * Count search results
     * @param array $filters filters
     * @return array
     */
    public function countSearchResults($filters = array()){
        $em = $this->getEntityManager();
        $now = new \DateTime();
        $this->sqlQuery = 'SELECT  COUNT(DISTINCT o.id) FROM AppOfferBundle:Offer o';
        $this->sqlQuery .= ' LEFT JOIN o.country co  LEFT JOIN o.market ma  LEFT JOIN o.transactionType tr ';
        $this->sqlQuery .= ' LEFT JOIN o.technicalCondition te  LEFT JOIN o.roof ro LEFT JOIN o.neighborhood ne ';
        $this->sqlQuery .= ' LEFT JOIN o.currency cu  LEFT JOIN o.category ca  LEFT JOIN o.additionalInfo ad';
        $this->sqlQuery .= '  LEFT JOIN o.user us  LEFT JOIN o.office uo LEFT JOIN o.type ty  LEFT JOIN o.media me ';
        $this->sqlQuery .= ' WHERE o.isPublish=1 AND o.expireDate >= :now  AND o.isDelete = 0 ';
        $this->sqlQuery .= ' AND (o.isPromo=0 OR (o.isPromo=1 AND o.promoExpire <= :now))';
        $this->addFilterFileds($filters);
        //$this->sqlQuery .= ' GROUP BY o.id';

        $query = $this->addFiltersValue($em->createQuery($this->sqlQuery),$filters);
        $query = $this->addLocationIndexValues($query);
        $query->setParameter('now', $now->format('Y-m-d'));
        return $query->getSingleScalarResult();
    }

    /**
     * Count promo search results
     * @param array $filters filters
     * @return array
     */
    public function countPromoSearchResults($filters = array()){
        $em = $this->getEntityManager();
        $now = new \DateTime();
        $this->sqlQuery = 'SELECT COUNT(DISTINCT o.id) FROM AppOfferBundle:Offer o';
        $this->sqlQuery .= ' LEFT JOIN o.country co  LEFT JOIN o.market ma  LEFT JOIN o.transactionType tr ';
        $this->sqlQuery .= ' LEFT JOIN o.technicalCondition te  LEFT JOIN o.roof ro LEFT JOIN o.neighborhood ne ';
        $this->sqlQuery .= ' LEFT JOIN o.currency cu  LEFT JOIN o.category ca  LEFT JOIN o.additionalInfo ad';
        $this->sqlQuery .= '  LEFT JOIN o.user us  LEFT JOIN o.office uo LEFT JOIN o.type ty  LEFT JOIN o.media me ';
        $this->sqlQuery .= ' WHERE o.isPublish=1 AND o.expireDate >= :now AND o.isDelete = 0 ';
        $this->sqlQuery .= ' AND (o.isPromo=1 AND o.promoExpire >= :now)';
        $this->addFilterFileds($filters);
        //$this->sqlQuery .= ' GROUP BY o.id';
        $query = $this->addFiltersValue($em->createQuery($this->sqlQuery),$filters);
        $query = $this->addLocationIndexValues($query);
        $query->setParameter('now', $now->format('Y-m-d'));

        return $query->getSingleScalarResult();
    }

    /**
     * Increment hits
     * @param int $id offer id
     */
    public function incrementHits($id) {

        $this->getEntityManager()
             ->getConnection()
             ->prepare("UPDATE offer SET hits = hits + 1
                         WHERE id=:id")
             ->execute(array("id"=> $id));
    }
    public function findRandomOffersFromSubdomain($subdomain, $limit)
    {
        $sql = "Select * From offer WHERE isPublish=1 AND expireDate >= NOW()  AND isDelete = 0 AND subdomain = :subdomain ORDER BY RAND() LIMIT :limit";
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue('subdomain', $subdomain);
        $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
    public function findRandomOffersByParams($transactionId, $categoryId, $limit)
    {
        $sql = "Select * From offer WHERE isPublish=1 AND expireDate >= NOW()  AND isDelete = 0 "
                ."AND transaction_type_id = :transactionId AND category_id = :categoryId ORDER BY RAND() LIMIT :limit";

        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue('transactionId', $transactionId);
        $stmt->bindValue('categoryId', $categoryId);
        $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
    /**
     * Update prices
     */
    public function updatePrices() {

        $this->getEntityManager()
             ->getConnection()
             ->prepare("UPDATE offer SET priceDef = (SELECT exchangeRate FROM currency WHERE id = offer.currency_id LIMIT 1)* offer.price, priceDefm2 =  (SELECT exchangeRate FROM currency WHERE id = offer.currency_id LIMIT 1)* offer.pricem2")
             ->execute();
    }

    /**
     * Find similar
     *
     * @param Offer $offer offer
     * @return Offer[]
     */
    public function findSimilar(Offer $offer){
        $qb = $this->createQueryBuilder('o');
        $qb->where('o.id != :id')
           ->andWhere('o.isPublish = 1 AND o.expireDate > :now')
           ->andWhere('o.region LIKE :region')
           ->andWhere('o.priceDef > :priceFrom AND o.priceDef < :priceTo ')
           ->andWhere('o.city LIKE :city')
           ->andWhere('o.type = :type')
           ->leftJoin('o.transactionType','t')
           ->leftJoin('o.category','c')
           ->leftJoin('o.type','ty')
           ->setParameter('id', $offer->getId())
           ->setParameter('city', $offer->getCity())
           ->setParameter('region', $offer->getRegion())
           ->setParameter('type', $offer->getType())
           ->setParameter('now', new \DateTime())
           ->setParameter('priceFrom', ($offer->getPriceDef() / 1.30))
           ->setParameter('priceTo',($offer->getPriceDef() * 1.30))
           ->setMaxResults(6)
        ;

           if(is_object($offer->getCategory())){
               $qb->andWhere('c.id = :category')
                  ->setParameter('category', $offer->getCategory()->getId());
           }
            if(is_object($offer->getTransactionType())){
                $qb->andWhere('t.id = :transaction')
                   ->setParameter('transaction', $offer->getTransactionType()->getId());
            }

        return $qb->getQuery()->getResult();
    }

    /**
     * Find avg price m2
     * @param Offer $offer offer
     * @return array
     */
    public function findAvgPricem2(Offer $offer){
        $qb = $this->createQueryBuilder('o');
        $qb->select($qb->expr()->avg('o.pricem2',  true))
           ->andWhere('o.isPublish = 1 AND o.expireDate > :now')
           ->andWhere('o.region LIKE :region')
           ->andWhere('o.city LIKE :city')
           ->leftJoin('o.transactionType','t')
           ->leftJoin('o.category','c')
           ->andWhere('o.pricem2 > 0')
           ->setParameter('city', '%'.$offer->getCity().'%')
           ->setParameter('region', '%'.$offer->getRegion().'%')
           ->setParameter('now', new \DateTime());

           if(is_object($offer->getCategory())){
               $qb->andWhere('c.id = :category')
                  ->setParameter('category', $offer->getCategory()->getId());
           }
            if(is_object($offer->getTransactionType())){
                $qb->andWhere('t.id = :transaction')
                   ->setParameter('transaction', $offer->getTransactionType()->getId());
            }

        return $qb->getQuery()
                  ->useResultCache(true)
                  ->setResultCacheLifetime(60*60*24)
            ->getSingleScalarResult();
    }
    /**
     * Find promo offers by office id
     * @param int $id id
     * @param int $limit limit
     * @return Offer[]
     */
    public function findPromoOffersByOfficeId($id, $limit=26){
        return $this->createQueryBuilder('o')
            ->join('o.user','u')
            ->join('u.office','uof')
            ->where('uof.id=:id')
            ->andWhere('o.isPublish = 1 AND o.expireDate > :now')
            ->andWhere('o.isPromo = 1 AND o.promoExpire > :now')
            ->addOrderBy('o.promoExpire','DESC')
            ->setParameter('now', new \DateTime())
            ->setParameter('id', $id)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
    /**
     * Find special offers by office id
     * @param int $id id
     * @param int $limit limit
     * @return  Offer[]
     */
    public function findSpecialOffersByOfficeId($id, $limit=26){
        return $this->createQueryBuilder('o')
            ->leftJoin('o.user','u')
            ->join('o.office','uof')
            ->where('uof.id=:id')
            ->andWhere('o.isPublish = 1 AND o.isDelete = 0 AND o.expireDate > :now')
            ->andWhere('o.isSpecial = 1')
            ->addOrderBy('o.modifyDate','DESC')
            ->setParameter('now', new \DateTime())
            ->setParameter('id', $id)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
    /**
     * Update user id
     * @param User $user user
     */
    public function updateUserId($user){
         $this->getEntityManager()
             ->getConnection()
             ->prepare("UPDATE offer SET user_id = :userId
                         WHERE agentImportId = :agentImportId")
             ->execute(array(
                              "agentImportId"=> $user->getImportId(),
                              "userId"=>$user->getId()));
    }
    /**
     * Unpublish office offers
     * @param Office $office office
     */
    public function unpublishOfficeOffers($office){
         $this->getEntityManager()
             ->getConnection()
             ->prepare("UPDATE offer SET isPublish = 0
                         WHERE office_id = :officeId")
             ->execute(array(
                              "officeId"=>$office->getId()));
    }
    /**
     * Publish office offers
     * @param Office $office office
     */
    public function publishOfficeOffers($office){
         $this->getEntityManager()
             ->getConnection()
             ->prepare("UPDATE offer SET isPublish = 1
                        WHERE office_id = :officeId AND isDelete = 0
                        AND expireDate > NOW()")
             ->execute(array(
                              "officeId"=>$office->getId()));
    }
    /**
     * count offers
     * @param bool $active active
     * @return array
     */
    public function countOffers($active) {
        $qb = $this->createQueryBuilder('o');
        $qb->select($qb->expr()->count('o.id'));

        if($active){
            $qb->where('o.isPublish=1');
        }else {
            $qb->where('o.isPublish=0');
        }

        return $qb->getQuery()
            ->useResultCache(true)
            ->setResultCacheLifetime(60*60)
            ->getSingleScalarResult()
            ;
    }
    /**
     * Find by signatures
     * @param array $signatures signatures
     * @return Offer[]
     */
    public function findBySignatures(array $signatures){
        $qb = $this->createQueryBuilder('o');
        $qb->where($qb->expr()->in('o.signature', ':signatures'))
           ->andWhere('o.isPublish = 1')
           ->andWhere('o.isDelete = 0')
           ->setParameter('signatures', $signatures);


        return $qb->getQuery()->getResult();
    }
    public function findAllAsArray() {
        $qb = $this->createQueryBuilder('o');
        $qb->where('o.isDelete = 0');

        return $qb->getQuery()->getArrayResult();
    }
    public function findRandom($limit){
        $qb = $this->createQueryBuilder('o');
        $qb->where('o.isDelete = 0')
           ->andWhere('o.expireDate > :now')
           ->andWhere('o.isPublish = 1')
           ->setParameter('now', new \DateTime())
           ->addOrderBy('o.createDate', 'DESC')
           ->setMaxResults($limit);

        return $qb->getQuery()->getArrayResult();
    }

    public function findReverseCategoryOffers($category, $transactionType, $location)
    {
        $orderBy = array('o.id','o.modifyDate','o.createDate','o.expireDate','o.hits');
        $orderDir = array('ASC','DESC');

        shuffle($orderBy);
        shuffle($orderDir);

        $qb = $this->createQueryBuilder('o');
        $qb->where('o.isDelete = 0')
            ->andWhere('o.expireDate > :now')
            ->andWhere('o.transactionType = :transactionType')
            ->andWhere('o.category = :category')
            ->andWhere('o.isPublish = 1')
            ->setParameter('now', new \DateTime())
            ->setParameter('category', $category)
            ->setParameter('transactionType', $transactionType)
            ->addOrderBy($orderBy[0],$orderDir[0])
            ->setMaxResults(4);

        if(is_a($location, LocationAutocomplete::class)){
            $qb->andWhere('o.region LIKE :region')
                ->andWhere('o.district LIKE :district')
                ->andWhere('o.city LIKE :city')
                ->setParameter('region', $location->getProvince())
                ->setParameter('district', $location->getDistrict().'%')
                ->setParameter('city', $location->getCity())
            ;
        }
        return $qb->getQuery()->getArrayResult();
    }

    public function findCurrentOffers($offer, $limit = 100)
    {
        $category = $offer->getCategory();
        $transactionType = $offer->getTransactionType();
        $city = $offer->getCity();

        $qb = $this->createQueryBuilder('o');

        $qb
            ->andWhere('o.expireDate > :now')
            ->andWhere('o.transactionType = :transactionType')
            ->andWhere('o.category = :category')
            ->andWhere('o.isPublish = 1')
            ->andWhere('o.city = :city')
            ->setParameter('now', new \DateTime())
            ->setParameter('transactionType', $transactionType)
            ->setParameter('category', $category)
            ->setParameter('city', $city)
            ->addOrderBy('o.id', 'DESC')
            ->setMaxResults($limit)
            ;

        return $qb
            ->getQuery()
            ->getResult()
            ;
    }

    public function findCurrentCityOffers(string $city, int $limit = 50)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.expireDate > :now')
            ->andWhere('o.isPublish = 1')
            ->andWhere('o.city = :city')
            ->setParameter('now', new \DateTime())
            ->setParameter('city', $city)
            ->addOrderBy('o.id', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->useResultCache(true)
            ->setResultCacheLifetime(60*60*24)
            ->getResult()
        ;
    }

    public function findProvinceByCity(string $city){
        return $this->getEntityManager()
            ->createQuery("SELECT a.province, a.city FROM AppUserBundle:Address a WHERE a.city = '$city'")
            ->setMaxResults(1)
            ->getResult()
        ;
    }

    public function findCurrentProvinceOffers(string $province, int $limit = 50)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.expireDate > :now')
            ->andWhere('o.isPublish = 1')
            ->andWhere('o.region = :region')
            ->setParameter('now', new \DateTime())
            ->setParameter('region', $province)
            ->addOrderBy('o.id', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->useResultCache(true)
            ->setResultCacheLifetime(60*60*24)
            ->getResult()
        ;
    }

    private function findCurrentOffersByMap($key)
    {
        $mapping = [
            'transactionType' => 'transaction_type_id',
            'category' => 'category_id'
        ];

        if(isset($mapping[$key]))
        {
            return $mapping[$key];
        }

        return $key;
    }

    /**
     * @param array $criteria
     * @param array $order
     * @param bool $limit
     * @param bool $archived
     * @return array
     */
    public function findCurrentOffersBy(
        $criteria = [],
        $order = [],
        $limit = false,
        $archived = false
    )
    {
        $sql = 'SELECT * FROM offer o WHERE o.isPublish = 1 ';

        $mapping = $criteria;
        $criteria = [];
        foreach ($mapping as $key => $value)
        {
            $criteria[$this->findCurrentOffersByMap($key)] = $value;
        }

        if($archived)
        {
            $sql .= 'AND o.expireDate < :now ';
        }
        else
        {
            $sql .= 'AND o.expireDate > :now ';
        }

        foreach ($criteria as $key => $value )
        {
            $sql .= "AND o.$key = :$key ";
        }

        if($order == 'rand')
        {
            $sql .= 'ORDER BY RAND() ';
        }
        else
        {
            if($order)
            {
                $sql .= 'ORDER BY ';
            }


            foreach ($order as $key => $value )
            {
                $sql .= "o.$key $value ";
            }
        }

        if($limit)
        {
            $sql .= 'LIMIT '.$limit;
        }

        $rsm = new ResultSetMappingBuilder($this->_em);
        $rsm->addRootEntityFromClassMetadata(Offer::class, 'o');

        $query = $this->_em->createNativeQuery($sql, $rsm);
        $query->setParameter('now', new \DateTime());


        foreach ($criteria as $key => $value )
        {
            $query->setParameter($key, $value);
        }

        return $query->getResult();

    }
}
