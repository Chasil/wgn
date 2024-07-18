<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Model;
use App\OfferBundle\Entity\Office;
use App\UserBundle\Entity\User;
use App\OfferBundle\Entity\Country;
/**
 * Class SearchMobile
 *
 * @author wojciech przygoda
 */
class SearchMobile {

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
     * @var string location
     */
    protected $locationIndexLike;

    /**
     *
     * @var float price def from
     */
    protected $priceDefFrom;

    /**
     *
     * @var float price def to
     */
    protected $priceDefTo;

    /**
     *
     * @var int currency
     */
    protected $currency;

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
     * @return SearchMobile
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
     * @return SearchMobile
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
     * @param int $transactionType transaction type
     * @return SearchMobile
     */
    public function setTransactionType($transactionType) {
        $this->transactionType = $transactionType;
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
     * @param string $locationIndexLike location
     * @return SearchMobile
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
     * @return SearchMobile
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
     * Set setPriceDefTo
     *
     * @param int $priceDefTo set price def to
     * @return SearchMobile
     */
    public function setPriceDefTo($priceDefTo) {
        $priceDefTo = str_replace(',', '.', $priceDefTo);
        $this->priceDefTo = floatval(preg_replace("/[^-0-9\.]/","",$priceDefTo));
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
     * @return SearchMobile
     */
    public function setCurrency($currency) {
        $this->currency = $currency;
        return $this;
    }
    public function getOffice() {
        return $this->office;
    }

    public function getUser() {
        return $this->user;
    }

    public function setOffice(Office $office) {
        $this->office = $office;
        return $this;
    }

    public function setUser(User $user) {
        $this->user = $user;
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
     * @param array $fields fields
     * @return array
     */
    private function prepereFilters($fields){
        $filters = array();
        foreach($fields as $field=>$value){
            if($value!='' && !is_array($value)){
                $name = str_replace(array('From','To','Like','Has'), "", $field);
                $filters[] = array('field'=>$name,'value'=>$value,'condition'=>$this->getCondition($field));
            }elseif(is_array($value)){
                $inVal = '';
                foreach($value as $val){
                    $inVal .= $val .',';
                }
                $filters[] = array('field'=>$name,'value'=>trim($inVal,','),'condition'=>'IN');
            }
        }
        return $filters;
    }

    /**
     *
     * Get Condition
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

    /**
     * @return Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param Country $country
     * @return SearchMobile
     */
    public function setCountry(Country $country): SearchMobile
    {
        $this->country = $country;

        return $this;
    }


}
