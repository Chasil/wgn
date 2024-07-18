<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Model;

use App\OfferBundle\Entity\AdditionalInfo;
use App\OfferBundle\Entity\Media;
use App\OfferBundle\Entity\Neighborhood;
use App\OfferBundle\Entity\Office;
use App\UserBundle\Entity\User;
use App\OfferBundle\Entity\Country;
use App\OfferBundle\Entity\Type;
use App\OfferBundle\Entity\Roof;

/**
 * Class Search
 *
 * @author wojciech przygoda
 */
class Search {

    /**
     *
     * @var int market
     */
    protected $market;

    /**
     *
     * @var int distance
     */
    protected $distance;
    /**
     *
     * @var int category
     */
    protected $category;

    /**
     *
     * @var int transaction type
     */
    protected $transactionType;

    /**
     *
     * @var Country country
     */
    protected $country;
    /**
     *
     * @var string locationIndex like
     */
    protected $locationIndexLike;

    /**
     *
     * @var float priceDef From
     */
    protected $priceDefFrom;

    /**
     *
     * @var float priceDef To
     */
    protected $priceDefTo;

    /**
     *
     * @var int currency
     */
    protected $currency;

    /**
     *
     * @var int squere from
     */
    protected $squereFrom;

    /**
     *
     * @var int squere to
     */
    protected $squereTo;

    /**
     *
     * @var int squere plot from
     */
    protected $squerePlotFrom;

    /**
     *
     * @var int squere plot to
     */
    protected $squerePlotTo;

    /**
     *
     * @var int rooms from
     */
    protected $roomsFrom;

    /**
     *
     * @var int rooms to
     */
    protected $roomsTo;

    /**
     *
     * @var int floor
     */
    protected $floor;

    /**
     *
     * @var string description like
     */
    protected $descriptionLike;

    /**
     *
     * @var Type type
     */
    protected $type;

    /**
     *
     * @var int storeys
     */
    protected $storeys;

    /**
     *
     * @var string signature like
     */
    protected $signatureLike;

    /**
     *
     * @var float price Def m2 From
     */
    protected $priceDefm2From;

    /**
     *
     * @var float price Def m2 To
     */
    protected $priceDefm2To;

    /**
     *
     * @var AdditionalInfo[] additional info
     */
    protected $additionalInfo;

    /**
     *
     * @var string year of building from
     */
    protected $yearOfBuildingFrom;

    /**
     *
     * @var string year of building to
     */
    protected $yearOfBuildingTo;

    /**
     *
     * @var bool has main Photo
     */
    protected $mainPhotoHas;

    /**
     *
     * @var bool is exclusive
     */
    protected $isExclusive;

    /**
     *
     * @var bool is direct
     */
    protected $isDirect;
    /**
     *
     * @var Roof roof
     */
    protected $roof;

    /**
     *
     * @var TechnicalCondition technical condition
     */
    protected $technicalCondition;

    /**
     *
     * @var Media[] media
     */
    protected $media;

    /**
     *
     * @var Neighborhood neighborhood
     */
    protected $neighborhood;

    /**
     *
     * @var Office office
     */
    protected $office;

    /**
     *
     * @var User user
     */
    protected $user;

    /**
     *
     * Get market
     *
     * @return int
     */
    public function getMarket() {
        return $this->market;
    }

    /**
     *
     * Set market
     *
     * @param int $market market
     * @return Search
     */
    public function setMarket($market) {
        $this->market = $market;
        return $this;
    }

    /**
     *
     * Get distance
     *
     * @return int
     */
    public function getDistance() {
        return $this->distance;
    }

    /**
     *
     * Set distance
     *
     * @param int $distance distance
     * @return Search
     */
    public function setDistance($distance) {
        $this->distance = $distance;
        return $this;
    }

    /**
     *
     * Get category
     *
     * @return int
     */
    public function getCategory() {
        return $this->category;
    }

    /**
     *
     * Set category
     *
     * @param int $category category
     * @return Search
     */
    public function setCategory($category) {
        $this->category = $category;
        return $this;
    }

    /**
     *
     * Get transactionType
     *
     * @return int
     */
    public function getTransactionType() {
        return $this->transactionType;
    }

    /**
     *
     * Set transactionType
     *
     * @param id $transactionType transaction type
     * @return Search
     */
    public function setTransactionType($transactionType) {
        $this->transactionType = $transactionType;
        return $this;
    }

    /**
     *
     * Get country
     *
     * @return Country
     */
    public function getCountry() {
        return $this->country;
    }

    /**
     *
     * Set country
     *
     * @param Country $country country
     * @return Search
     */
    public function setCountry($country) {
        $this->country = $country;
        return $this;
    }

    /**
     *
     * Get locationIndexLike
     *
     * @return string
     */
    public function getLocationIndexLike() {
        return $this->locationIndexLike;
    }

    /**
     *
     * Set locationIndexLike
     *
     * @param string $locationIndexLike location index
     * @return Search
     */
    public function setLocationIndexLike($locationIndexLike) {
        $this->locationIndexLike = $locationIndexLike;
        return $this;
    }

    /**
     *
     * Get priceDefFrom
     *
     * @return float
     */
    public function getPriceDefFrom() {
        return $this->priceDefFrom;
    }

    /**
     *
     * Set priceDefFrom
     *
     * @param float $priceDefFrom price def from
     * @return Search
     */
    public function setPriceDefFrom($priceDefFrom) {
        $priceDefFrom = str_replace(',', '.', $priceDefFrom);
        $this->priceDefFrom = floatval(preg_replace("/[^-0-9\.]/","",$priceDefFrom));
        return $this;
    }
    /**
     *
     * Get priceDefTo
     *
     * @return float
     */
    public function getPriceDefTo() {
        return $this->priceDefTo;
    }
    /**
     *
     * Set priceDefTo
     *
     * @param float $priceDefTo price def to
     * @return Search
     */
    public function setPriceDefTo($priceDefTo) {
        $priceDefTo = str_replace(',', '.', $priceDefTo);
        $this->priceDefTo = floatval(preg_replace("/[^-0-9\.]/","",$priceDefTo));
        return $this;
    }

    /**
     *
     * Get priceDefm2From
     *
     * @return float
     */
    public function getPriceDefm2From() {
        return $this->priceDefm2From;
    }
    /**
     *
     * Set priceDefm2From
     *
     * @param float $priceDefm2From price def m2 from
     * @return Search
     */
    public function setPriceDefm2From($priceDefm2From) {
        $priceDefm2From = str_replace(',', '.', $priceDefm2From);
        $this->priceDefm2From = floatval(preg_replace("/[^-0-9\.]/","",$priceDefm2From));
        return $this;
    }

    /**
     *
     * Get priceDef2To
     *
     * @return float
     */
    public function getPriceDefm2To() {
        return $this->priceDefm2To;
    }

    /**
     *
     * Set priceDefm2To
     *
     * @param float $priceDefm2To price def m2 to
     * @return Search
     */
    public function setPriceDefm2To($priceDefm2To) {
        $this->priceDefm2To = $priceDefm2To;
        return $this;
    }

    /**
     *
     * Get currency
     *
     * @return int
     */
    public function getCurrency() {
        return $this->currency;
    }

    /**
     *
     * Set currency
     *
     * @param int $currency currency
     * @return Search
     */
    public function setCurrency($currency) {
        $this->currency = $currency;
        return $this;
    }
    /**
     *
     * Get squereFrom
     *
     * @return float
     */
    public function getSquereFrom() {

        return $this->squereFrom;
    }

    /**
     *
     * Set squereFrom
     *
     * @param float $squereFrom squere from
     * @return Search
     */
    public function setSquereFrom($squereFrom) {
        $squereFrom = str_replace(',', '.', $squereFrom);
        $this->squereFrom = floatval(preg_replace("/[^-0-9\.]/","",$squereFrom));

        return $this;
    }
    /**
     *
     * Get squereTo
     *
     * @return float
     */
    public function getSquereTo() {
        return $this->squereTo;
    }

    /**
     *
     * Set squereTo
     *
     * @param float $squereTo squere to
     * @return Search
     */
    public function setSquereTo($squereTo) {
        $squereTo = str_replace(',', '.', $squereTo);
        $this->squereTo = floatval(preg_replace("/[^-0-9\.]/","",$squereTo));
        return $this;
    }

    /**
     *
     * Get squerePlotFrom
     *
     * @return float
     */
    public function getSquerePlotFrom() {
        return $this->squerePlotFrom;
    }

    /**
     *
     * Set squerePlotFrom
     *
     * @param float $squerePlotFrom squere plot from
     * @return Search
     */
    public function setSquerePlotFrom($squerePlotFrom) {
        $squerePlotFrom = str_replace(',', '.', $squerePlotFrom);
        $this->squerePlotFrom = floatval(preg_replace("/[^-0-9\.]/","",$squerePlotFrom));

        return $this;
    }

    /**
     *
     * Get squerePlotTo
     *
     * @return float
     */
    public function getSquerePlotTo() {
        return $this->squerePlotTo;
    }

    /**
     *
     * Set squerePlotTo
     *
     * @param float $squerePlotTo squere plot to
     * @return Search
     */
    public function setSquerePlotTo($squerePlotTo) {
        $squerePlotTo = str_replace(',', '.', $squerePlotTo);
        $this->squerePlotTo = floatval(preg_replace("/[^-0-9\.]/","",$squerePlotTo));

        return $this;
    }

    /**
     *
     * Get roomsFrom
     *
     * @return int
     */
    public function getRoomsFrom() {
        return $this->roomsFrom;
    }

    /**
     *
     * Set roomsFrom
     *
     * @param float $roomsFrom rooms from
     * @return Search
     */
    public function setRoomsFrom($roomsFrom) {
        $this->roomsFrom = intval(preg_replace("/[^-0-9]/","",$roomsFrom));
        return $this;
    }

    /**
     *
     * Get roomsTo
     *
     * @return int
     */
    public function getRoomsTo() {
        return $this->roomsTo;
    }

    /**
     *
     * Set roomsTo
     *
     * @param int $roomsTo rooms to
     * @return Search
     */
    public function setRoomsTo($roomsTo) {
        $this->roomsTo = intval(preg_replace("/[^-0-9]/","",$roomsTo));
        return $this;
    }

    /**
     *
     * Get floor
     *
     * @return int
     */
    public function getFloor() {
        return $this->floor;
    }

    /**
     *
     * Set floor
     *
     * @param int $floor floor
     * @return Search
     */
    public function setFloor($floor) {
        $this->floor = $floor;
        return $this;
    }

    /**
     *
     * Get descriptionLike
     *
     * @return string
     */
    public function getDescriptionLike() {
        return $this->descriptionLike;
    }

    /**
     *
     * Set descriptionLike
     *
     * @param float $descriptionLike description
     * @return Search
     */
    public function setDescriptionLike($descriptionLike) {
        $this->descriptionLike = $descriptionLike;
        return $this;
    }

    /**
     *
     * Get type
     *
     * @return Type
     */
    public function getType() {
        return $this->type;
    }

    /**
     *
     * Set type
     *
     * @param Type $type type
     * @return Search
     */
    public function setType($type) {
        $this->type = $type;
        return $this;
    }


    /**
     *
     * Get storeys
     *
     * @return int
     */
    public function getStoreys() {
        return $this->storeys;
    }

    /**
     *
     * Set storeys
     *
     * @param int $storeys storeys
     * @return Search
     */
    public function setStoreys($storeys) {
        $this->storeys = $storeys;
        return $this;
    }

    /**
     *
     * Get signatureLike
     *
     * @return string
     */
    public function getSignatureLike() {
        return $this->signatureLike;
    }

    /**
     *
     * Set signatureLike
     *
     * @param string $signatureLike signature
     * @return Search
     */
    public function setSignatureLike($signatureLike) {
        $this->signatureLike = $signatureLike;
        return $this;
    }

    /**
     *
     * Get pricem2From
     *
     * @return float
     */
    public function getPricem2From() {
        return $this->pricem2From;
    }

    /**
     *
     * Set pricem2From
     *
     * @param float $pricem2From price m2 from
     * @return Search
     */
    public function setPricem2From($pricem2From) {
        $this->pricem2From = $pricem2From;
        return $this;
    }

    /**
     *
     * Get pricem2To
     *
     * @return float
     */
    public function getPricem2To() {
        return $this->pricem2To;
    }

    /**
     *
     * Set pricem2To
     *
     * @param float $pricem2To price m2 to
     * @return Search
     */
    public function setPricem2To($pricem2To) {
        $this->pricem2To = $pricem2To;
        return $this;
    }

    /**
     *
     * Get AdditionalInfo
     *
     * @return AdditionalInfo[]
     */
    public function getAdditionalInfo() {
        return $this->additionalInfo;
    }

    /**
     *
     * Set additionalInfo
     *
     * @param AdditionalInfo[] $additionalInfo additional info
     * @return Search
     */
    public function setAdditionalInfo($additionalInfo) {
        $this->additionalInfo = $additionalInfo;
        return $this;
    }

    /**
     *
     * Get yearOfBuildingFrom
     *
     * @return string
     */
    public function getYearOfBuildingFrom() {
        return $this->yearOfBuildingFrom;
    }

    /**
     *
     * Set yearOfBuildingFrom
     *
     * @param string $yearOfBuildingFrom year of building from
     * @return Search
     */
    public function setYearOfBuildingFrom($yearOfBuildingFrom) {
        $this->yearOfBuildingFrom = $yearOfBuildingFrom;
        return $this;
    }

    /**
     *
     * Get yearOfBuildingTo
     *
     * @return string
     */
    public function getYearOfBuildingTo() {
        return $this->yearOfBuildingTo;
    }

    /**
     *
     * Set yearOfBuildingTo
     *
     * @param string $yearOfBuildingTo year of building to
     * @return Search
     */
    public function setYearOfBuildingTo($yearOfBuildingTo) {
        $this->yearOfBuildingTo = $yearOfBuildingTo;
        return $this;
    }

    /**
     *
     * Get mainPhotoHas
     *
     * @return bool
     */
    public function getMainPhotoHas() {
        return $this->mainPhotoHas;
    }

    /**
     *
     * Set mainPhotoHas
     *
     * @param bool $mainPhotoHas has main photo
     * @return Search
     */
    public function setMainPhotoHas($mainPhotoHas) {
        $this->mainPhotoHas = $mainPhotoHas;
        return $this;
    }

    /**
     *
     * Get isExclusive
     *
     * @return bool
     */
    public function getIsExclusive() {
        return $this->isExclusive;
    }

    /**
     *
     * Set isExclusive
     *
     * @param bool $isExclusive is exclusive
     * @return Search
     */
    public function setIsExclusive($isExclusive) {
        $this->isExclusive = $isExclusive;
        return $this;
    }

    /**
     *
     * Get isDirect
     *
     * @return bool
     */
    public function getIsDirect() {
        return $this->isDirect;
    }

    /**
     *
     * Set isDirect
     *
     * @param bool $isDirect is direct
     * @return Search
     */
    public function setIsDirect($isDirect) {
        $this->isDirect = $isDirect;
        return $this;
    }

    /**
     *
     * Get roof
     *
     * @return int
     */
    public function getRoof() {
        return $this->roof;
    }

    /**
     *
     * Set roof
     *
     * @param int $roof roof
     * @return Search
     */
    public function setRoof($roof) {
        $this->roof = $roof;
        return $this;
    }

    /**
     *
     * Get technicalCondition
     *
     * @return int
     */
    public function getTechnicalCondition() {
        return $this->technicalCondition;
    }

    /**
     *
     * Set technicalCondition
     *
     * @param int $technicalCondition technical condition
     * @return Search
     */
    public function setTechnicalCondition($technicalCondition) {
        $this->technicalCondition = $technicalCondition;
        return $this;
    }

    /**
     *
     * Get media
     *
     * @return Media[]
     */
    public function getMedia() {
        return $this->media;
    }

    /**
     *
     * Set media
     *
     * @param Media[] $media media
     * @return Search
     */
    public function setMedia($media) {
        $this->media = $media;
        return $this;
    }

    /**
     *
     * Get neighborhood
     *
     * @return Neighborhood[]
     */
    public function getNeighborhood() {
        return $this->neighborhood;
    }

    /**
     *
     * Set neighborhood
     *
     * @param Neighborhood[] $neighborhood neighborhood
     * @return Search
     */
    public function setNeighborhood($neighborhood) {
        $this->neighborhood = $neighborhood;
        return $this;
    }
    /**
     *
     * Get office
     *
     * @return Office
     */
    public function getOffice() {
        return $this->office;
    }

    /**
     *
     * Set office
     *
     * @param Office $office office
     * @return Search
     */
    public function setOffice($office) {
        $this->office = $office;
        return $this;
    }

    /**
     *
     * Get user
     *
     * @return User
     */
    public function getUser() {
        return $this->user;
    }

    /**
     *
     * Set user
     *
     * @param User $user user
     * @return Search
     */
    public function setUser($user) {
        $this->user = $user;
        return $this;
    }

    /**
     *
     * Get locationMobileIndexLike
     *
     * @return string
     */
    public function getLocationMobileIndexLike() {
        return $this->locationMobileIndexLike;
    }

    /**
     *
     * Set locationMobileIndexLike
     *
     * @param string $locationMobileIndexLike location mobile index
     * @return Search
     */
    public function setLocationMobileIndexLike($locationMobileIndexLike) {
        $this->locationMobileIndexLike = $locationMobileIndexLike;
        return $this;
    }

    /**
     *
     * Get filters
     *
     * @return array
     */
    public function getFilters(){
        $vars = get_object_vars($this);

        $fields = array();

        foreach($vars as $key=>$value){

          if(!is_object($value)){
             $name = $this->getFieldName($key);
             $fields[$name] = $value;
          }else {
              if(is_a($value,'Doctrine\\Common\\Collections\\ArrayCollection')){
                  foreach($value as $v){
                    $name = $this->getFieldName($key,true);
                     $fields[$name][] = $v->getId();
                  }
              }else{
                $name = $this->getFieldName($key,true);
                $fields[$name] = $value->getId();
              }
          }
        }
        return $this->prepereFilters($fields);
    }
    /**
     *
     * Get field name
     *
     * @param string $field field
     * @param bool $isObject is object
     * @return string
     */
    private function getFieldName($field, $isObject=false){
        if($isObject){
            return substr($field, 0,2) . '.id';
        }
        return 'o'. '.' . $field;
    }

    /**
     *
     * Prepere filters
     *
     * @param array $fields filelds
     * @return array
     */
    private function prepereFilters($fields){
        $filters = array();
        foreach($fields as $field=>$value){
            $name = str_replace(array('From','To','Like','Has'), "", $field);

            if($value!='' && !is_array($value)){
                $filters[] = array('field'=>$name,'value'=>$value,'condition'=>$this->getCondition($field));
            }elseif(is_array($value)){
                $inVal = '';
                foreach($value as $val){
                    $inVal .= $val .',';
                }
                $filters[] = array('field'=>$name,'value'=>trim($inVal,','),'condition'=>'IN');
            }elseif(is_int($value)){
                $filters[] = array('field'=>$name,'value'=>$value,'condition'=>$this->getCondition($field));
            }

            if($name == 'o.signature' && $value !=''){
                $filters = array();
                $filters[] = array('field'=>$name,'value'=>$value,'condition'=>$this->getCondition($field));
                break;
            }
        }
        return $filters;
    }

    /**
     * Get condition
     *
     * @param string $field field
     * @return string
     */
    private function getCondition($field){
        if(preg_match('/From/',$field)){
            return '>=';
        }
        if(preg_match('/To/',$field)){
            return '<=';
        }
        if(preg_match('/Like/',$field)){
            return 'LIKE';
        }
        if(preg_match('/Has/',$field)){
            return 'IS NOT NULL';
        }
        return '=';
    }
}
