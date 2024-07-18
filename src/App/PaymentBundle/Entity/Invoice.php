<?php
/**
 * This file is part of the AppPaymentBundle package.
 *
 */
namespace App\PaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Invoice
 *
 * @author wojciech przygoda
 *
 * @ORM\Table(name="invoice")
 * @ORM\Entity(repositoryClass="App\PaymentBundle\Entity\InvoiceRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Invoice
{
    const TYPE_SUBSCRIPTION = 'ABO';
    const TYPE_PUBLICATION = 'OGL';
    const TYPE_PROMO = 'PROMO';
    /**
     * @var int invoice id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string number
     *
     * @ORM\Column(name="number", type="string", length=255)
     */
    private $number;
    /**
     * @var string number type
     *
     * @ORM\Column(name="numberType", type="string", length=255)
     */
    private $numberType;
    /**
     * @var string counter
     *
     * @ORM\Column(name="counter", type="integer")
     */
    private $counter;
    /**
     * @var float net value
     *
     * @ORM\Column(name="netValue", type="float")
     */
    private $netValue;
    /**
     * @var float gross value
     *
     * @ORM\Column(name="grossValue", type="float")
     */
    private $grossValue;
    /**
     * @var \DateTime issue date
     *
     * @ORM\Column(name="issueDate", type="datetime")
     */
    private $issueDate;
    /**
     * @var \DateTime sale date
     *
     * @ORM\Column(name="saleDate", type="datetime")
     */
    private $saleDate;
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
     * @var string company name
     *
     * @ORM\Column(name="companyName", type="string", length=255)
     */
    private $companyName;

    /**
     * @var string country
     *
     * @ORM\Column(name="country", type="string", length=255)
     */
    private $country;

    /**
     * @var string city
     *
     * @ORM\Column(name="city", type="string", length=255)
     */
    private $city;
    /**
     * @var string zip code
     *
     * @ORM\Column(name="zipCode", type="string", length=10)
     */
    private $zipCode;
    /**
     * @var string street
     *
     * @ORM\Column(name="street", type="string", length=255)
     */
    private $street;

    /**
     * @var string nip
     *
     * @ORM\Column(name="nip", type="string", length=11)
     */
    private $nip;

    /**
     * @var string client company name
     *
     * @ORM\Column(name="clientCompanyName", type="string", length=255)
     */
    private $clientCompanyName;

    /**
     * @var string client first name
     *
     * @ORM\Column(name="clientFirstName", type="string", length=255,nullable=true)
     */
    private $clientFirstName;

    /**
     * @var string client last name
     *
     * @ORM\Column(name="clientLastName", type="string", length=255,nullable=true)
     */
    private $clientLastName;

    /**
     * @var string client nip
     *
     * @ORM\Column(name="clientNip", type="string", length=11)
     */
    private $clientNip;

    /**
     * @var string client country
     *
     * @ORM\Column(name="clientCountry", type="string", length=255,nullable=true)
     */
    private $clientCountry;
    /**
     * @var string client city
     *
     * @ORM\Column(name="clientCity", type="string", length=255,nullable=true)
     */
    private $clientCity;
    /**
     * @var string client zip codet
     *
     * @ORM\Column(name="clientZipCodet", type="string", length=10,nullable=true)
     */
    private $clientZipCode;
    /**
     * @var string  client street
     *
     * @ORM\Column(name="clientStreet", type="string", length=255,nullable=true)
     */
    private $clientStreet;
    /**
     * @var InvoiceItem[] collection of invoice items
     * @ORM\OneToMany(targetEntity="InvoiceItem", mappedBy="invoice",cascade={"persist", "remove"})
     */
    protected $items;
    /**
     * @var Payment payment
     *
     * @ORM\OneToOne(targetEntity="App\PaymentBundle\Entity\Payment", inversedBy="invoice")
     * @ORM\JoinColumn(name="payment_id", referencedColumnName="id",nullable=true)
     */
    protected $payment;
    /**
     * @var User user
     *
     * @ORM\ManyToOne(targetEntity="App\UserBundle\Entity\User", inversedBy="invoices")
     */
    protected $user;

    /**
     * Constructor
     */
    public function __construct() {
        $this->createDate = new \DateTime();
        $this->modifyDate = new \DateTime();
        $this->issueDate = new \DateTime();
        $this->saleDate = new \DateTime();

        $this->items = new ArrayCollection();
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
     * Set number
     *
     * @param string $number number
     *
     * @return Invoice
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set createDate
     *
     * @param \DateTime $createDate create date
     *
     * @return Invoice
     */
    public function setCreateDate($createDate)
    {
        $this->createDate = $createDate;

        return $this;
    }

    /**
     * Get createDate
     *
     * @return \DateTime create date
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }
    /**
     * Set issueDate
     *
     * @param \DateTime $issueDate issue date
     *
     * @return Invoice
     */
    public function setIssueDate($issueDate)
    {
        $this->issueDate = $issueDate;

        return $this;
    }

    /**
     * Get issueDate
     *
     * @return \DateTime
     */
    public function getIssueDate()
    {
        return $this->issueDate;
    }
    /**
     * Set saleDate
     *
     * @param \DateTime $saleDate sale date
     *
     * @return Invoice
     */
    public function setSaleDate($saleDate)
    {
        $this->saleDate = $saleDate;

        return $this;
    }

    /**
     * Get saleDate
     *
     * @return \DateTime
     */
    public function getSaleDate()
    {
        return $this->saleDate;
    }
    /**
     * Set modifyDate
     *
     * @param \DateTime $modifyDate modification date
     *
     * @return Invoice
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
     * Set companyName
     *
     * @param string $companyName company name
     *
     * @return Invoice
     */
    public function setCompanyName($companyName)
    {
        $this->companyName = $companyName;

        return $this;
    }

    /**
     * Get companyName
     *
     * @return string
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     * Set country
     *
     * @param string $country country
     *
     * @return Invoice
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set city
     *
     * @param string $city city
     *
     * @return Invoice
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set street
     *
     * @param string $street street
     *
     * @return Invoice
     */
    public function setStreet($street)
    {
        $this->street = $street;

        return $this;
    }

    /**
     * Get street
     *
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Set nip
     *
     * @param string $nip nip
     *
     * @return Invoice
     */
    public function setNip($nip)
    {
        $this->nip = $nip;

        return $this;
    }

    /**
     * Get nip
     *
     * @return string
     */
    public function getNip()
    {
        return $this->nip;
    }

    /**
     * Set user
     *
     * @param User $user user
     *
     * @return Invoice
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set clientCompanyName
     *
     * @param string $clientCompanyName client company name
     *
     * @return Invoice
     */
    public function setClientCompanyName($clientCompanyName)
    {
        $this->clientCompanyName = $clientCompanyName;

        return $this;
    }

    /**
     * Get clientCompanyName
     *
     * @return string
     */
    public function getClientCompanyName()
    {
        return $this->clientCompanyName;
    }

    /**
     * Set clientFirstName
     *
     * @param string $clientFirstName client first name
     *
     * @return Invoice
     */
    public function setClientFirstName($clientFirstName)
    {
        $this->clientFirstName = $clientFirstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getClientFirstName()
    {
        return $this->clientFirstName;
    }

    /**
     * Set clientLastName
     *
     * @param string $clientLastName client last name
     *
     * @return Invoice
     */
    public function setClientLastName($clientLastName)
    {
        $this->clientLastName = $clientLastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set clientNip
     *
     * @param string $clientNip client nip
     *
     * @return Invoice
     */
    public function setClientNip($clientNip)
    {
        $this->clientNip = $clientNip;

        return $this;
    }

    /**
     * Get clientNip
     *
     * @return string
     */
    public function getClientNip()
    {
        return $this->clientNip;
    }

    /**
     * Set clientCountry
     *
     * @param string $clientCountry client country
     *
     * @return Invoice
     */
    public function setClientCountry($clientCountry)
    {
        $this->clientCountry = $clientCountry;

        return $this;
    }

    /**
     * Get clientCountry
     *
     * @return string
     */
    public function getClientCountry()
    {
        return $this->clientCountry;
    }

    /**
     * Set clientCity
     *
     * @param string $clientCity client city
     *
     * @return Invoice
     */
    public function setClientCity($clientCity)
    {
        $this->clientCity = $clientCity;

        return $this;
    }

    /**
     * Get clientCity
     *
     * @return string
     */
    public function getClientCity()
    {
        return $this->clientCity;
    }
    /**
     * Get counter
     *
     * @return int
     */
    public function getCounter() {
        return $this->counter;
    }
    /**
     * Set counter
     *
     * @param int $counter
     * @return Invoice
     */
    public function setCounter($counter) {
        $this->counter = $counter;
        return $this;
    }
    /**
     * Get counter
     *
     * @return int
     */
    public function getZipCode() {
        return $this->zipCode;
    }
    /**
     * Set zipCode
     *
     * @param string $zipCode zip code
     *
     * @return Invoice
     */
    public function setZipCode($zipCode) {
        $this->zipCode = $zipCode;
        return $this;
    }
    /**
     * Get payment
     *
     * @return Payment
     */
    public function getPayment() {
        return $this->payment;
    }
    /**
     * Set payment
     *
     * @param Payment $payment payment
     *
     * @return Invoice
     */
    public function setPayment($payment) {
        $this->payment = $payment;
        return $this;
    }
    /**
     * Get clientZipCode
     *
     * @return string
     */
    public function getClientZipCode() {
        return $this->clientZipCode;
    }
    /**
     * Set clientZipCode
     *
     * @param string $clientZipCode client zip code
     *
     * @return Invoice
     */
    public function setClientZipCode($clientZipCode) {
        $this->clientZipCode = $clientZipCode;
        return $this;
    }
    /**
     * Get clientStreet
     *
     * @return string
     */
    public function getClientStreet() {
        return $this->clientStreet;
    }
    /**
     * Set clientStreet
     *
     * @param string $clientStreet client street
     *
     * @return Invoice
     */
    public function setClientStreet($clientStreet) {
        $this->clientStreet = $clientStreet;
        return $this;
    }
    /**
     * Get items
     *
     * @return InvoiceItem[]
     */
    public function getItems() {
        return $this->items;
    }
    /**
     * Set items
     *
     * @param InvoiceItem[] $items invoice items
     *
     * @return Invoice
     */
    public function setItems($items) {
        $this->items = $items;
        return $this;
    }
    /**
     * Get numberType
     *
     * @return string
     */
    public function getNumberType() {
        return $this->numberType;
    }
    /**
     * Set numberType
     *
     * @param string $numberType number type
     *
     * @return Invoice
     */
    public function setNumberType($numberType) {
        $this->numberType = $numberType;
        return $this;
    }
    /**
     * Get netValue
     *
     * @return float
     */
    public function getNetValue() {
        return $this->netValue;
    }
    /**
     * Set netValue
     *
     * @param float $netValue net value
     *
     * @return Invoice
     */
    public function setNetValue($netValue) {
        $this->netValue = $netValue;
        return $this;
    }
    /**
     * Get grossValue
     *
     * @return float
     */
    public function getGrossValue() {
        return $this->grossValue;
    }
    /**
     * Set grossValue
     *
     * @param float $grossValue gross value
     *
     * @return Invoice
     */
    public function setGrossValue($grossValue) {
        $this->grossValue = $grossValue;
        return $this;
    }
    /**
     * Add invoice item
     *
     * @param InvoiceItem $item invoice item
     *
     * @return Invoice
     */
    public function addItem(InvoiceItem $item) {
        $item->setInvoice($this);
        $this->getItems()->add($item);

        return $this;
    }
    /**
     * Prepere invoice number
     *
     * @return Invoice
     */
    public function prepereNumber() {
        $number = '';

        switch($this->getNumberType()){
            case self::TYPE_PUBLICATION:
                $number .= self::TYPE_PUBLICATION;
            break;
            case self::TYPE_SUBSCRIPTION:
                $number .= self::TYPE_SUBSCRIPTION;
            break;
            case self::TYPE_PROMO:
                $number .= self::TYPE_PROMO;
            break;
        }

        $now = new \DateTime();
        $year = $now->format('Y');
        $month = $now->format('m');
        $number .='/'.$this->getCounter().'/'.$month.'/'.$year;

        $this->setNumber($number);
        return $this;
    }
    /**
     * Calculate total values
     *
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
     public function calculateTotalValues() {
         foreach($this->getItems() as $item){
             $this->netValue += $item->getNetprice()*$item->getQuantity();
             $this->grossValue += $item->getGrossPrice()*$item->getQuantity();
         }
     }
     /**
      * Get taxes
      *
      * @return array
      */
     public function getTaxes() {
         $taxRates = array();

         foreach($this->getItems() as $item){
             if(isset($taxRates[$item->getTax()])){
                 $taxRates[$item->getTax()]['value'] += $item->getNetprice() * $item->getQuantity();
             }else {
                 $taxRates[$item->getTax()] = array('rate'=>$item->getTax(),'value'=>$item->getNetprice() * $item->getQuantity());
             }
         }

         return $taxRates;
     }
}

