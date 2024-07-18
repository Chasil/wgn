<?php
/**
 * This file is part of the AppPaymentBundle package.
 *
 */
namespace App\PaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * Class Payment
 *
 * @author wojciech przygoda
 *
 * @ORM\Table(name="payment")
 * @ORM\Entity(repositoryClass="App\PaymentBundle\Entity\PaymentRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Payment
{
    /**
     * @const payment type card
     */
    const TYPE_CARD = 'card';
    /**
     * @const payment type sms
     */
    const TYPE_SMS = 'sms';
    /**
     * @const payment type subscription
     */
    const TYPE_SUBSCRIPTION = 'subscription';
    /**
     * @const payment state started
     */
    const STATE_STARTED = 0;
    /**
     * @const payment state faild
     */
    const STATE_FAILD = -1;
    /**
     * @const payment state success
     */
    const STATE_SUCCESS = 1;
	/**
	 * @const payment type person
	 */
	const TYPE_PERSON = 'person';
	/**
	 * @const payment type company
	 */
	const TYPE_COMPANY = 'company';

    /**
     * @var int payment id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime create date
     *
     * @ORM\Column(name="createDate", type="datetime")
     */
    private $createDate;

    /**
     * @var \DateTime modification date
     *
     * @ORM\Column(name="modifyDate", type="datetime")
     */
    private $modifyDate;

    /**
     * @var float value
     *
     * @ORM\Column(name="value", type="float")
     */
    private $value;
    /**
     * @var string session id
     *
     * @ORM\Column(name="sessionId", type="string", length=255,nullable=true)
     */
    private $sessionId;
    /**
     * @var string transaction id
     *
     * @ORM\Column(name="transactionId", type="string", length=255,nullable=true)
     */
    private $transactionId;
    /**
     * @var string token
     *
     * @ORM\Column(name="token", type="string", length=255,nullable=true)
     */
    private $token;

    /**
     * @var int state
     *
     * @ORM\Column(name="state", type="integer")
     */
    private $state;
    /**
     * @var bool promo
     *
     * @ORM\Column(name="promo", type="boolean")
     */
    protected $promo;
    /**
     * @var string payment method
     *
     * @ORM\Column(name="paymentMethod", type="string", length=255)
     */
    protected $paymentMethod;
    /**
     * @var string transfer method
     *
     * @ORM\Column(name="transferMethod", type="string", length=255,nullable=true)
     */
    protected $transferMethod;
    /**
     * @var string sms code
     *
     * @ORM\Column(name="smsCode", type="string", length=20,nullable=true)
     */
    protected $smsCode;
    /**
     * @var string first name
     *
     * @ORM\Column(name="firstName", type="string", length=100,nullable=true)
     */
    protected $firstName;
    /**
     * @var string last name
     *
     * @ORM\Column(name="lastName", type="string", length=20,nullable=true)
     */
    protected $lastName;
    /**
     * @var string address
     *
     * @ORM\Column(name="address", type="string", length=20,nullable=true)
     */
    protected $address;
    /**
     * @var string city
     *
     * @ORM\Column(name="city", type="string", length=20,nullable=true)
     */
    protected $city;
    /**
     * @var string zip code
     *
     * @ORM\Column(name="zipCode", type="string", length=10,nullable=true)
     */
    protected $zipCode;
    /**
     * @var Country country
     * @ORM\ManyToOne(targetEntity="App\OfferBundle\Entity\Country", inversedBy="payments")
     * @ORM\JoinColumn(name="country_id", referencedColumnName="id", nullable=true)
     */
    protected $country;
    /**
     * @var Offer offer
     * @ORM\ManyToOne(targetEntity="App\OfferBundle\Entity\Offer", inversedBy="payments")
     * @ORM\JoinColumn(name="offer_id", referencedColumnName="id")
     */
    protected $offer;
    /**
     * @var User user
     * @ORM\ManyToOne(targetEntity="App\UserBundle\Entity\User", inversedBy="payments")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id",nullable=true)
     */
    protected $user;
    /**
     * @var string email
     *
     * @ORM\Column(name="email", type="string", length=200,nullable=true)
     */
    protected $email;
    /**
     * @var string phone
     *
     * @ORM\Column(name="phone", type="string", length=20,nullable=true)
     */
    protected $phone;
    /**
     * @var PaymentItem[] collection of payment items
     * @ORM\OneToMany(targetEntity="PaymentItem", mappedBy="payment",cascade={"persist", "remove"})
     */
    protected $paymentItems;
    /**
     * @var Invoice invoice
     * @ORM\OneToOne(targetEntity="Invoice", mappedBy="payment")
     */
    private $invoice;
    /**
     *
     * @var bool agree regulations
     */
    protected $agreeRegulations;
    /**
     *
     * @var bool agree processing data
     */
    protected $agreeProcessingData;

	/**
	 * @var string name
	 *
	 * @ORM\Column(name="name", type="string", length=30,nullable=true)
	 */
	protected $name;
	/**
	 * Get name
	 *
	 * @return string
	 */
	function getName() {
		return $this->name;
	}
	/**
	 * Set name
	 *
	 * @param string $name name
	 *
	 * @return Payment
	 */
	function setName($name) {
		$this->name = $name;
		return $this;
	}

	/**
	 * @var string NIP
	 *
	 * @ORM\Column(name="NIP", type="string", length=12, nullable=true)
	 */
	protected $NIP;
	/**
	 * Get NIP
	 *
	 * @return string
	 */
	function getNIP() {
		return $this->NIP;
	}
	/**
	 * Set NIP
	 *
	 * @param string $NIP NIP
	 *
	 * @return Payment
	 */
	function setNIP($NIP) {
		$this->NIP = $NIP;
		return $this;
	}

	/**
	 * @var string payment method
	 *
	 * @ORM\Column(name="legalPersonality", type="string", length=255)
	 */
	protected $legalPersonality;
	/**
	 * Set value
	 *
	 * @param boolean $value value
	 *
	 * @return Payment
	 */
	public function setPersonality($value)
	{
		$this->legalPersonality = $value;
		return $this;
	}
	/**
	 * Get legalPersonality
	 *
	 * @return string
	 */
	function getLegalPersonality() {
		return $this->legalPersonality;
	}
	/**
	 * Set legalPersonality
	 *
	 * @param string $legalPersonality payment method
	 *
	 * @return Payment
	 */
	function setLegalPersonality($legalPersonality) {
		$this->legalPersonality = $legalPersonality;
		return $this;
	}

	/**
	 * @var boolean payment method
	 *
	 * @ORM\Column(name="VAT", type="boolean", nullable=true)
	 */
	protected $VAT;
	/**
	 * Get VAT
	 *
	 * @return boolean
	 */
	function getVAT() {
		return $this->VAT;
	}
	/**
	 * Set VAT
	 *
	 * @param boolean $VAT payment method
	 *
	 * @return Payment
	 */
	function setVAT($VAT) {
		$this->VAT = $VAT;
		return $this;
	}

    /**
     * Constructor
     */
    public function __construct() {
        $this->state = self::STATE_STARTED;
        $this->publication = true;
        $this->createDate = new \DateTime();
        $this->modifyDate = new \DateTime();
        $this->paymentMethod = self::TYPE_CARD;
		$this->legalPersonality = self::TYPE_PERSON;
		$this->VAT = true;
        $this->promo = false;
        $this->paymentItems = new ArrayCollection();
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set createDate
     *
     * @param \DateTime $createDate create date
     *
     * @return Payment
     */
    public function setCreateDate($createDate)
    {
        $this->createDate = $createDate;

        return $this;
    }

    /**
     * Get createDate
     *
     * @return \DateTime
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }

    /**
     * Set modifyDate
     *
     * @param \DateTime $modifyDate modification date
     *
     * @return Payment
     */
    public function setModifyDate($modifyDate)
    {
        $this->modifyDate = $modifyDate;

        return $this;
    }

    /**
     * Get modifyDate
     *
     * @return \DateTime
     */
    public function getModifyDate()
    {
        return $this->modifyDate;
    }

    /**
     * Set value
     *
     * @param float $value value
     *
     * @return Payment
     */
    public function setValue($value)
    {
        if($this->paymentMethod == self::TYPE_SMS){
            $this->value = $this->calculateSmsValue($value);
        }else {
        $this->value = $value;
        }

        return $this;
    }

    /**
     * Get value
     *
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set method
     *
     * @param string $method method
     *
     * @return Payment
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Get method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Set sessionId
     *
     * @param string $sessionId session id
     *
     * @return Payment
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;

        return $this;
    }

    /**
     * Get sessionId
     *
     * @return string
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }
    /**
     * Set transactionId
     *
     * @param string $transactionId trasaction id
     *
     * @return Payment
     */
    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;

        return $this;
    }

    /**
     * Get transactionId
     *
     * @return string
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }
    /**
     * Set token
     *
     * @param string $token token
     *
     * @return Payment
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set state
     *
     * @param integer $state state
     *
     * @return Payment
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return int
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Get promo
     *
     * @return bool
     */
    function getPromo() {
        return $this->promo;
    }
    /**
     * Set promo
     *
     * @param bool $promo promo
     *
     * @return Payment
     */
    function setPromo($promo) {
        $this->promo = $promo;
        return $this;
    }

    /**
     * Get paymentMethod
     *
     * @return string
     */
    function getPaymentMethod() {
        return $this->paymentMethod;
    }
    /**
     * Set paymentMethod
     *
     * @param string $paymentMethod payment method
     *
     * @return Payment
     */
    function setPaymentMethod($paymentMethod) {
        $this->paymentMethod = $paymentMethod;
        return $this;
    }

    /**
     * Get agreeRegulations
     *
     * @return bool
     */
    function getAgreeRegulations() {
        return $this->agreeRegulations;
    }
    /**
     * Set agreeRegulations
     *
     * @param bool $agreeRegulations agree regulations
     *
     * @return Payment
     */
    function setAgreeRegulations($agreeRegulations) {
        $this->agreeRegulations = $agreeRegulations;
        return $this;
    }

    /**
     * Get agreeProcessingData
     *
     * @return bool
     */
    function getAgreeProcessingData() {
        return $this->agreeProcessingData;
    }
    /**
     * Set agreeProcessingData
     *
     * @param bool $agreeProcessingData agree processing data
     *
     * @return Payment
     */
    function setAgreeProcessingData($agreeProcessingData) {
        $this->agreeProcessingData = $agreeProcessingData;
        return $this;
    }

    /**
     * Get string
     *
     * @return string
     */
    function getSmsCode() {
        return $this->smsCode;
    }
    /**
     * Set smsCode
     *
     * @param string $smsCode sms code
     *
     * @return Payment
     */
    function setSmsCode($smsCode) {
        $this->smsCode = $smsCode;
        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    function getFirstName() {
        return $this->firstName;
    }
    /**
     * Set firstName
     *
     * @param string $firstName first name
     *
     * @return Payment
     */
    function setFirstName($firstName) {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    function getLastName() {
        return $this->lastName;
    }
    /**
     * Set lastName
     *
     * @param string $lastName last name
     *
     * @return Payment
     */
    function setLastName($lastName) {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    function getAddress() {
        return $this->address;
    }
    /**
     * Set address
     *
     * @param string $address address
     *
     * @return Payment
     */
    function setAddress($address) {
        $this->address = $address;
        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    function getCity() {
        return $this->city;
    }
    /**
     * Set city
     *
     * @param string $city city
     *
     * @return Payment
     */
    function setCity($city) {
        $this->city = $city;
        return $this;
    }

    /**
     * Get zipCode
     *
     * @return string
     */
    function getZipCode() {
        return $this->zipCode;
    }
    /**
     * Set zipCode
     *
     * @param string $zipCode zip code
     *
     * @return Payment
     */
    function setZipCode($zipCode) {
        $this->zipCode = $zipCode;
        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    function getCountry() {
        return $this->country;
    }
    /**
     * Set country
     *
     * @param string $country country
     *
     * @return Payment
     */
    function setCountry($country) {
        $this->country = $country;
        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    function getEmail() {
        return $this->email;
    }
    /**
     * Set email
     *
     * @param integer $email email
     *
     * @return string
     */
    function setEmail($email) {
        $this->email = $email;
        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    function getPhone() {
        return $this->phone;
    }
    /**
     * Set phone
     *
     * @param string $phone phone
     *
     * @return Payment
     */
    function setPhone($phone) {
        $this->phone = $phone;
        return $this;
    }

    /**
     * Get offer
     *
     * @return Offer
     */
    public function getOffer() {
        return $this->offer;
    }
    /**
     * Set offer
     *
     * @param Offer $offer offer
     *
     * @return Payment
     */
    public function setOffer($offer) {
        $this->offer = $offer;
        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser() {
        return $this->user;
    }
    /**
     * Set user
     *
     * @param User $user user
     *
     * @return Payment
     */
    public function setUser($user) {
        $this->user = $user;
        return $this;
    }

    /**
     * Get transferMethod
     *
     * @return string
     */
    public function getTransferMethod() {
        return $this->transferMethod;
    }
    /**
     * Set transferMethod
     *
     * @param string $transferMethod transfer method
     *
     * @return Payment
     */
    public function setTransferMethod($transferMethod) {
        $this->transferMethod = $transferMethod;
        return $this;
    }

    /**
     * Get paymentItems
     *
     * @return PaymentItem[]
     */
    public function getPaymentItems() {
        return $this->paymentItems;
    }
    /**
     * Set paymentItems
     *
     * @param PaymentItem[] $paymentItems payment items
     *
     * @return Payment
     */
    public function setPaymentItems($paymentItems) {
        $this->paymentItems = $paymentItems;
        return $this;
    }

    /**
     * Get invoice
     *
     * @return Invoice
     */
    public function getInvoice() {
        return $this->invoice;
    }
    /**
     * Set invoice
     *
     * @param integer $invoice invoice
     *
     * @return Payment
     */
    public function setInvoice($invoice) {
        $this->invoice = $invoice;
        return $this;
    }
    /**
     * Add paymentItem
     *
     * @param PaymentItem $paymentItem payment item
     *
     * @return Payment
     */
    public function addPaymentItem(PaymentItem $paymentItem){
        $paymentItem->setPayment($this);
        $this->getPaymentItems()->add($paymentItem);
    }
    /**
    * Calculate values
    *
    * @ORM\PrePersist()
    */
     public function calculateValues(){
         $value = 0;
         foreach($this->getPaymentItems() as $item){
             $value += $item->getValue();
         }

         $this->setValue($value);
     }
    /**
     * Calculate sms value
     *
     * @param integer $value value
     *
     * @return Payment
     */
     public function calculateSmsValue($value){

          if($value >0 && $value <=11.07){
              return 11.07;
          }
          return 30.75;

     }
     /**
      * Add promo items
      */
     public function addPromoItems(){
         if($this->isPromo()){
             foreach($this->getPaymentItems() as $item){
                 if($item->getType()==PaymentItem::TYPE_PUBLICATION){
                    $paymentItem = new PaymentItem(PaymentItem::TYPE_PROMO);
                    $paymentItem->setOffer($item->getOffer());
                    $this->addPaymentItem($paymentItem);
                 }
             }
         }
     }

    /**
     * Check is promo invoice
     *
     * @return bool
     */
     public function isPromo() {
         return $this->promo;
     }
    /**
     * Copy data form user
     *
     * @return Payment
     */
     public function copyDataFormUser(){
         if($this->user){
            $this->firstName = $this->user->getFirstName();
            $this->lastName = $this->user->getLastName();
            $address = $this->user->getDefaultAddress();

            if($address){
                $this->address = $address->getStreet();
                $this->zipCode = $address->getZipCode();
                $this->city = $address->getCity();
                $this->country = $address->getCountry();
            }
         }

         return $this;
     }
}

