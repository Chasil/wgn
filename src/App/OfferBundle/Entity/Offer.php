<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
/**
 * Class Offer
 *
 * @author wojciech przygoda
 *
 * @ORM\Table(name="offer",indexes={@ORM\Index(name="domain_idx", columns={"subdomain"})})
 * @ORM\Entity(repositoryClass="App\OfferBundle\Entity\OfferRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Offer
{
    /**
     * @var int offer id
     *
     * @ORM\Column(name="id", type="bigint", options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string name
     *
     * @ORM\Column(name="subdomain", type="string", length=255,nullable=true)
     */
    private $subdomain;
    /**
     * @var string name
     *
     * @ORM\Column(name="old_name", type="string", length=255,nullable=true)
     */
    private $oldName;
    /**
     * @var string signature
     *
     * @ORM\Column(name="signature", type="string", length=255)
     */
    private $signature;
    /**
     * @var string tmp id
     *
     * @ORM\Column(name="tmpId", type="string", length=255)
     */
    private $tmpId;
    /**
     * @var integer import id
     *
     * @ORM\Column(name="importId", type="string", length=255, nullable=true)
     */
    private $importId;
    /**
     * @var integer import id
     *
     * @ORM\Column(name="oldImportId", type="integer", nullable=true)
     */
    private $oldImportId;
    /**
     * @var integer agent import id
     *
     * @ORM\Column(name="agentImportId", type="integer", nullable=true)
     */
    private $agentImportId;
    /**
     * @var integer legacy id
     *
     * @ORM\Column(name="legacyId", type="string", length=255, nullable=true)
     */
    private $legacyId;
    /**
     * @var string description
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;
    /**
     * @var string locationIndex
     *
     * @ORM\Column(name="locationIndex", type="text")
     */
    private $locationIndex;
    /**
     * @var string main photo file name
     *
     * @ORM\Column(name="mainPhoto", type="string", length=255,nullable=true)
     */
    private $mainPhoto;

    /**
     * @var string video url
     *
     * @ORM\Column(name="video", type="string", length=255, nullable=true)
     */
    private $video;

    /**
     * @var float price
     *
     * @ORM\Column(name="price", type="float",nullable=true)
     */
    private $price;
    /**
     * @var float priceDef
     *
     * @ORM\Column(name="priceDef", type="float",nullable=true)
     */
    private $priceDef;
    /**
     * @var float price m2
     *
     * @ORM\Column(name="pricem2", type="float",nullable=true)
     */
    private $pricem2;
    /**
     * @var float priceDef m2
     *
     * @ORM\Column(name="priceDefm2", type="float",nullable=true)
     */
    private $priceDefm2;
    /**
     * @var float squere
     *
     * @ORM\Column(name="squere", type="float",nullable=true)
     */
    private $squere;
    /**
     * @var float squere plot
     *
     * @ORM\Column(name="squerePlot", type="float",nullable=true)
     */
    private $squerePlot;
    /**
     * @var string year of building
     *
     * @ORM\Column(name="yearOfBuilding", type="string", length=255, nullable=true)
     */
    private $yearOfBuilding;
    /**
     * @var bool is special
     *
     * @ORM\Column(name="isSpecial", type="boolean")
     */
    private $isSpecial;
    /**
     * @var bool is exclusive
     *
     * @ORM\Column(name="isExclusive", type="boolean")
     */
    private $isExclusive;

    /**
     * @var bool is direct
     *
     * @ORM\Column(name="isDirect", type="boolean")
     */
    private $isDirect;

    /**
     * @var string region
     *
     * @ORM\Column(name="region", type="string", length=255,nullable=true)
     */
    private $region;
    /**
     * @var string district
     *
     * @ORM\Column(name="district", type="string", length=255,nullable=true)
     */
    private $district;
    /**
     * @var string city
     *
     * @ORM\Column(name="city", type="string", length=255)
     */
    private $city;

    /**
     * @var string street
     *
     * @ORM\Column(name="street", type="string", length=255,nullable=true)
     */
    private $street;
    /**
     * @var string section
     *
     * @ORM\Column(name="section", type="string", length=255,nullable=true)
     */
    private $section;
    /**
     * @var string subSection
     *
     * @ORM\Column(name="sub_section", type="string", length=255,nullable=true)
     */
    private $subSection;

    /**
     * @var string lat
     *
     * @ORM\Column(name="lat", type="string", length=255,nullable=true)
     */
    private $lat;

    /**
     * @var string lng
     *
     * @ORM\Column(name="lng", type="string", length=255,nullable=true)
     */
    private $lng;

    /**
     * @var string contact person
     *
     * @ORM\Column(name="contactPerson", type="string", length=255,nullable=true)
     */
    private $contactPerson;

    /**
     * @var string email
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var string phone
     *
     * @ORM\Column(name="phone", type="string", length=20,nullable=true)
     */
    private $phone;
	
	/**
	 * @var int
	 *
	 * @ORM\Column(name="phone_counter", type="integer")
	 */
    private $phoneCounter = 0;
    
    /**
     * @var \DateTime create date
     *
     * @ORM\Column(name="createDate", type="datetime")
     */
    private $createDate;
    /**
     * @var \DateTime modification date
     *
     * @ORM\Column(name="modifyDate", type="datetime",nullable=true)
     */
    private $modifyDate;
    /**
     * @var \DateTime update date
     *
     * @ORM\Column(name="updateDate", type="datetime",nullable=true)
     */
    private $updateDate;
    /**
     * @var \DateTime update date
     *
     * @ORM\Column(name="deleteDate", type="datetime",nullable=true)
     */
    private $deleteDate;
    /**
     * @var int publication days
     *
     * @ORM\Column(name="days", type="integer")
     */
    private $days;
    /**
     * @var int promotion days
     *
     * @ORM\Column(name="promoDays", type="integer",nullable=true)
     */
    private $promoDays;
    /**
     * @var \DateTime expire date
     *
     * @ORM\Column(name="expireDate", type="datetime")
     */
    private $expireDate;

    /**
     * @var bool is publish
     *
     * @ORM\Column(name="isPublish", type="boolean")
     */
    private $isPublish;
    /**
     * @var bool is tmp
     *
     * @ORM\Column(name="isTmp", type="boolean")
     */
    private $isTmp;
    /**
     * @var bool is delete
     *
     * @ORM\Column(name="isDelete", type="boolean")
     */
    private $isDelete;
    /**
     * @var int hits
     *
     * @ORM\Column(name="hits", type="integer")
     */
    private $hits;

    /**
     * @var int payment state
     *
     * @ORM\Column(name="paymentState", type="integer")
     */
    private $paymentState;
    /**
     * @var string activation code
     *
     * @ORM\Column(name="activationCode", type="string", length=255)
     */
    private $activationCode;
    /**
     * @var int rooms
     *
     * @ORM\Column(name="rooms", type="integer",nullable=true)
     */
    private $rooms;

    /**
     * @var int floor
     *
     * @ORM\Column(name="floor", type="integer",nullable=true)
     */
    private $floor;

    /**
     * @var int storeys
     *
     * @ORM\Column(name="storeys", type="integer",nullable=true)
     */
    private $storeys;

    /**
     * @var Property property
     *
     * @ORM\ManyToOne(targetEntity="Property", inversedBy="offers")
     * @ORM\JoinColumn(name="property_id", referencedColumnName="id", nullable=true)
     */
    private $property;

    /**
     * @var TechnicalCondition technical condition
     *
     * @ORM\ManyToOne(targetEntity="TechnicalCondition", inversedBy="offers")
     * @ORM\JoinColumn(name="technical_condition_id", referencedColumnName="id", nullable=true)
     */
    private $technicalCondition;

    /**
     * @var float month payments
     *
     * @ORM\Column(name="monthPayments", type="float",nullable=true)
     */
    private $monthPayments;
    /**
     * @var Currency currency
     *
     * @ORM\ManyToOne(targetEntity="Currency", inversedBy="orrersMonthPaymentsCurrency")
     * @ORM\JoinColumn(name="month_payments_currency_id", referencedColumnName="id", nullable=true)
     */
    protected $monthPaymentsCurrency;
    /**
     * @var ExhibitionWindows exhibition windows
     *
     * @ORM\ManyToOne(targetEntity="ExhibitionWindows", inversedBy="orrers")
     * @ORM\JoinColumn(name="exhibition_windows_id", referencedColumnName="id", nullable=true)
     */
    protected $exhibitionWindows;
    /**
     * @var \DateTime available from
     *
     * @ORM\Column(name="availableFrom", type="datetime", nullable=true)
     */
    private $availableFrom;

    /**
     * @var string ability to wath
     *
     * @ORM\Column(name="abilityToWath", type="string", length=255, nullable=true)
     */
    private $abilityToWath;
    /**
     * @var bool has fence
     *
     * @ORM\Column(name="hasFence", type="boolean")
     */
    private $hasFence;
    /**
     * @var string dimensions
     *
     * @ORM\Column(name="dimensions", type="string", length=200,nullable=true)
     */
    private $dimensions;
    /**
     * @var bool is long publish
     *
     * @ORM\Column(name="isLongPublish", type="boolean")
     */
    private $isLongPublish;

    /**
     * @var bool is promo
     *
     * @ORM\Column(name="isPromo", type="boolean")
     */
    private $isPromo;

    /**
     * @var \DateTime promo expire date
     *
     * @ORM\Column(name="promoExpire", type="datetime")
     */
    private $promoExpire;
    /**
     * @var TransactionType transaction type
     * @ORM\ManyToOne(targetEntity="TransactionType", inversedBy="offers")
     */
    protected $transactionType;
    /**
     * @var Category category
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="offers")
     */
    protected $category;
    /**
     * @var User user
     *
     * @ORM\ManyToOne(targetEntity="App\UserBundle\Entity\User", inversedBy="offers")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id",  onDelete="SET NULL")
     */
    protected $user;
   /**
    * @var Type type
     * @ORM\ManyToOne(targetEntity="Type", inversedBy="offers")
     */
    protected $type;
    /**
     * @var Country country
     * @ORM\ManyToOne(targetEntity="Country", inversedBy="offers")
     */
    protected $country;
    /**
     * @var Currency currency
     * @ORM\ManyToOne(targetEntity="Currency", inversedBy="offers")
     * @ORM\JoinColumn(name="currency_id", referencedColumnName="id", nullable=true)
     */
    protected $currency;
    /**
     * @var Market market
     * @ORM\ManyToOne(targetEntity="Market", inversedBy="offers")
     */
    protected $market;
    /**
     * @var Windows windows
     * @ORM\ManyToOne(targetEntity="Windows", inversedBy="offers")
     */
    protected $windows;
    /**
     * @var RoofCover roof cover
     * @ORM\ManyToOne(targetEntity="RoofCover", inversedBy="offers")
     */
    protected $roofCover;
    /**
     * @var Roof roof
     * @ORM\ManyToOne(targetEntity="Roof", inversedBy="offers")
     */
    protected $roof;

    /**
     * @var Localization localization
     * @ORM\ManyToOne(targetEntity="Localization", inversedBy="offers")
     */
    protected $localization;

    /**
     * @var AccessRoad access road
     * @ORM\ManyToOne(targetEntity="AccessRoad", inversedBy="offers")
     */
    protected $accessRoad;

    /**
     * @var LegalStatus legal status
     * @ORM\ManyToOne(targetEntity="LegalStatus", inversedBy="offers")
     */
    protected $legalStatus;

    /**
     * @var Media media
     * @ORM\ManyToMany(targetEntity="Media")
     * @ORM\JoinTable(name="offer_media",
     *      joinColumns={@ORM\JoinColumn(name="offer_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="media_id", referencedColumnName="id")}
     *      )
     */
    private $media;

    /**
     * @var AdditionalInfo additional info
     * @ORM\ManyToMany(targetEntity="AdditionalInfo")
     * @ORM\JoinTable(name="offer_additional_info",
     *      joinColumns={@ORM\JoinColumn(name="offer_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="additional_info_id", referencedColumnName="id")}
     *      )
     */
    private $additionalInfo;

    /**
     *
     * @var Neighborhood neighborhood
     *
     * @ORM\ManyToMany(targetEntity="Neighborhood")
     * @ORM\JoinTable(name="offer_neighborhood",
     *      joinColumns={@ORM\JoinColumn(name="offer_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="neighborhood_id", referencedColumnName="id")}
     *      )
     */
    private $neighborhood;
    /**
     *
     * @var OfferImage offer image
     *
     * @ORM\OneToMany(targetEntity="OfferImage", mappedBy="offer",cascade={"persist", "remove"})
     * @ORM\OrderBy({"ordering" = "ASC"})
     */
    protected $images;
    /**
     * @var Payment payment
     * @ORM\OneToMany(targetEntity="App\PaymentBundle\Entity\Payment", mappedBy="offer")
     */
    protected $payments;
    /**
     *
     * @var Message message
     * @ORM\OneToMany(targetEntity="Message", mappedBy="offer")
     */
    protected $messages;
    /**
     * @var Observed observed
     * @ORM\OneToMany(targetEntity="Observed", mappedBy="offer")
     */
    protected $observed;
   /**
    * @var Office office
     * @ORM\ManyToOne(targetEntity="App\OfficeBundle\Entity\Office", inversedBy="offers")
     */
    protected $office;
    /**
     *
     * @var string allowed html tags
     */
    protected $allowedTags = "";

    /**
     * @var Link[]
     *
     * @ORM\OneToMany(targetEntity="Link", mappedBy="office",cascade={"persist", "remove"})
     */
    protected $links;
    /**
     * @var string virtualWalkUrl
     *
     * @ORM\Column(name="virtualWalkUrl", type="string", length=255,nullable=true)
     */
    protected $virtualWalkUrl;
    /**
     * Constructor
     */
    public function __construct() {

        $this->createDate = new \DateTime();
        $this->modifyDate = new \DateTime();
        $this->updateDate = new \DateTime();
        $this->expireDate = new \DateTime();
        $this->promoExpire = new \DateTime();
        $this->isTmp = true;
        $this->isDelete = false;
        $this->paymentState = -1;
        $this->isPublish = false;
        $this->activationCode = sha1(uniqid(mt_rand(), true));
        $this->tmpId = sha1(uniqid(mt_rand(), true));
        $this->isLongPublish = false;
        $this->isExclusive = false;
        $this->isSpecial = false;
        $this->isDirect = false;
        $this->hasFence = false;
        $this->isPromo = false;
        $this->days = 90;
        $this->hits = 0;
        $this->media = new ArrayCollection();
        $this->additionalInfo = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->payments = new ArrayCollection();
        $this->messages = new ArrayCollection();
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
     * Get tmpId
     *
     * @return string
     */
    function getTmpId() {
        return $this->tmpId;
    }
    /**
     * Set tmpId
     *
     * @param string $tmpId tmpId
     *
     * @return Offer
     */
    function setTmpId($tmpId) {
        $this->tmpId = $tmpId;
        return $this;
    }
    /**
     * Set importId
     *
     * @param string $importId import id
     *
     * @return Offer
     */
    public function setImportId($importId) {
        $this->importId = $importId;
        return $this;
    }
    /**
     * Get importId
     *
     * @return string
     */
    public function getImportId() {
        return $this->importId;
    }
    /**
     * Set agentImportId
     *
     * @param string $agentImportId agent import id
     *
     * @return Offer
     */
    public function setAgentImportId($agentImportId) {
        $this->agentImportId = $agentImportId;
        return $this;
    }
    /**
     * Get agentImportId
     *
     * @return string
     */
    public function getAgentImportId() {
        return $this->agentImportId;
    }
    /**
     * Set legacyId
     *
     * @param string $legacyId legacy id
     *
     * @return Offer
     */
    public function setLegacyId($legacyId) {
        $this->legacyId = $legacyId;
        return $this;
    }
    /**
     * Get importId
     *
     * @return string
     */
    public function getLegacyId() {
        return $this->legacyId;
    }
    /**
     * Set name
     *
     * @param string $name name
     *
     * @return Offer
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * Set signature
     *
     * @param string $signature signature
     *
     * @return Offer
     */
    public function setSignature($signature)
    {
        $this->signature = $signature;

        return $this;
    }

    /**
     * Get signature
     *
     * @return string
     */
    public function getSignature()
    {
        return $this->signature;
    }
    /**
     * Set description
     *
     * @param string $description description
     *
     * @return Offer
     */
    public function setDescription($description)
    {
        $text = strip_tags($description, $this->allowedTags);
        $this->description = $text;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
       return strip_tags($this->description, $this->allowedTags);

    }
    /**
     * Set locationIndex
     *
     * @param string $locationIndex locationIndex
     *
     * @return Offer
     */
    public function setLocationIndex($locationIndex)
    {
        $this->locationIndex = $locationIndex;

        return $this;
    }

    /**
     * Get locationIndex
     *
     * @return string
     */
    public function getLocationIndex()
    {
        return $this->locationIndex;
    }
    /**
     * Set mainPhoto
     *
     * @param string $mainPhoto main photo file
     *
     * @return Offer
     */
    public function setMainPhoto($mainPhoto)
    {
        $this->mainPhoto = $mainPhoto;

        return $this;
    }
    /**
     * Set first image as main photo
     *
     * @return Offer
     */
    public function setFirstAsMainPhoto() {
        $image = $this->getFirstImage();
        if($image){
            $this->setMainPhoto($image->getName());
        }

        return $this;
    }
    /**
     * Get mainPhoto
     *
     * @return string
     */
    public function getMainPhoto()
    {
        return $this->mainPhoto;
    }

    /**
     * Set video
     *
     * @param string $video video
     *
     * @return Offer
     */
    public function setVideo($video)
    {
        $this->video = $video;

        return $this;
    }

    /**
     * Get video
     *
     * @return string
     */
    public function getVideo()
    {
        return $this->video;
    }

    /**
     * Set price
     *
     * @param float $price price
     *
     * @return Offer
     */
    public function setPrice($price)
    {

        $this->price = floatval(preg_replace("/[^-0-9\.]/","",$price));

        return $this;
    }

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set pricem2
     *
     * @param float $pricem2 price m2
     *
     * @return Offer
     */
    public function setPricem2($pricem2)
    {
        $pricem2 = str_replace(',','.', $pricem2);
        $this->pricem2 = floatval(preg_replace("/[^-0-9\.]/","",$pricem2));

        return $this;
    }

    /**
     * Get pricem2
     *
     * @return float
     */
    public function getPricem2()
    {
        return $this->pricem2;
    }
    /**
     * Set priceDef
     *
     * @param float $priceDef priceDef
     *
     * @return Offer
     */
    public function setPriceDef($priceDef)
    {

        $this->priceDef = floatval(preg_replace("/[^-0-9\.]/","",$priceDef));

        return $this;
    }

    /**
     * Get priceDef
     *
     * @return float
     */
    public function getPriceDef()
    {
        return $this->priceDef;
    }

    /**
     * Set priceDefm2
     *
     * @param float $priceDefm2 priceDef m2
     *
     * @return Offer
     */
    public function setPriceDefm2($priceDefm2)
    {
        $priceDefm2 = str_replace(',','.', $priceDefm2);
        $this->priceDefm2 = floatval(preg_replace("/[^-0-9\.]/","",$priceDefm2));

        return $this;
    }

    /**
     * Get priceDefm2
     *
     * @return float
     */
    public function getPriceDefm2()
    {
        return $this->priceDefm2;
    }
    /**
     * Set squere
     *
     * @param float $squere squere
     *
     * @return Offer
     */
    public function setSquere($squere)
    {
        $this->squere = floatval(preg_replace("/[^-0-9\.]/","",$squere));

        return $this;
    }

    /**
     * Get squere
     *
     * @return float
     */
    public function getSquere()
    {
        return $this->squere;
    }
    /**
     * Get squerePlot
     *
     * @return float
     */
    public function getSquerePlot() {
        return $this->squerePlot;
    }
    /**
     * Set squerePlot
     *
     * @param float $squerePlot squere plot
     *
     * @return Offer
     */
    public function setSquerePlot($squerePlot) {
        $this->squerePlot = floatval(preg_replace("/[^-0-9\.]/","",$squerePlot));
        return $this;
    }

        /**
     * Set market
     *
     * @param integer $market market
     *
     * @return Offer
     */
    public function setMarket($market)
    {
        $this->market = $market;

        return $this;
    }

    /**
     * Get market
     *
     * @return int
     */
    public function getMarket()
    {
        return $this->market;
    }

    /**
     * Set yearOfBuilding
     *
     * @param string $yearOfBuilding year of building
     *
     * @return Offer
     */
    public function setYearOfBuilding($yearOfBuilding)
    {
        $this->yearOfBuilding = $yearOfBuilding;

        return $this;
    }

    /**
     * Get yearOfBuilding
     *
     * @return string
     */
    public function getYearOfBuilding()
    {
        return $this->yearOfBuilding;
    }

    /**
     * Set isExclusive
     *
     * @param boolean $isExclusive isExclusive
     *
     * @return Offer
     */
    public function setIsExclusive($isExclusive)
    {
        $this->isExclusive = $isExclusive;

        return $this;
    }

    /**
     * Get isExclusive
     *
     * @return bool
     */
    public function getIsExclusive()
    {
        return $this->isExclusive;
    }

    /**
     * Set isDirect
     *
     * @param boolean $isDirect isDirect
     *
     * @return Offer
     */
    public function setIsDirect($isDirect)
    {
        $this->isDirect = $isDirect;

        return $this;
    }

    /**
     * Get isDirect
     *
     * @return bool
     */
    public function getIsDirect()
    {
        return $this->isDirect;
    }

    /**
     * Set region
     *
     * @param string $region region
     *
     * @return Offer
     */
    public function setRegion($region)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * Get region
     *
     * @return string region
     */
    public function getRegion()
    {
        return $this->region;
    }
    /**
     * Set region
     *
     * @param string $district district
     *
     * @return Offer
     */
    public function setDistrict($district)
    {
        $this->district = $district;

        return $this;
    }

    /**
     * Get region
     *
     * @return string
     */
    public function getDistrict()
    {
        return $this->district;
    }
    /**
     * Set city
     *
     * @param string $city city
     *
     * @return Offer
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
     * @return Offer
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
     * Set lat
     *
     * @param string $lat lat
     *
     * @return Offer
     */
    public function setLat($lat)
    {
        $this->lat = $lat;

        return $this;
    }

    /**
     * Get lat
     *
     * @return string
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * Set lng
     *
     * @param string $lng lng
     *
     * @return Offer
     */
    public function setLng($lng)
    {
        $this->lng = $lng;

        return $this;
    }

    /**
     * Get lng
     *
     * @return string
     */
    public function getLng()
    {
        return $this->lng;
    }

    /**
     * Set contactPerson
     *
     * @param string $contactPerson contactPerson
     *
     * @return Offer
     */
    public function setContactPerson($contactPerson)
    {
        $this->contactPerson = $contactPerson;

        return $this;
    }

    /**
     * Get contactPerson
     *
     * @return string
     */
    public function getContactPerson()
    {
        return $this->contactPerson;
    }

    /**
     * Set email
     *
     * @param string $email email
     *
     * @return Offer
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set phone
     *
     * @param string $phone phone
     *
     * @return Offer
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set createDate
     *
     * @param \DateTime $createDate create date
     *
     * @return Offer
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
     * Set createDate
     *
     * @param \DateTime $modifyDate modification date
     *
     * @return Offer
     */
    public function setModifyDate($modifyDate)
    {
        $this->modifyDate = $modifyDate;

        return $this;
    }

    /**
     * Get imagesModifyDate
     *
     * @return \DateTime
     */
    public function getModifyDate()
    {
        return $this->modifyDate;
    }

    /**
     * Set createDate
     *
     * @param \DateTime $updateDate update date
     *
     * @return Offer
     */
    public function setUpdateDate($updateDate)
    {
        $this->updateDate = $updateDate;

        return $this;
    }

    /**
     * Get updateDate
     *
     * @return \DateTime
     */
    public function getUpdateDate()
    {
        return $this->updateDate;
    }
    /**
     * Get deleteDate
     *
     * @return \DateTime
     */
    public function getDeleteDate() {
        return $this->deleteDate;
    }
    /**
     * Set deleteDate
     *
     * @param \DateTime $deleteDate update date
     *
     * @return Offer
     */
    public function setDeleteDate(\DateTime $deleteDate) {
        $this->deleteDate = $deleteDate;
        return $this;
    }


    /**
     * Set days
     *
     * @param integer $days days
     *
     * @return Offer
     */
    public function setDays($days)
    {
        $this->days = $days;

        return $this;
    }

    /**
     * Get days
     *
     * @return int
     */
    public function getDays()
    {
        return $this->days;
    }
    /**
     * Set promoDays
     *
     * @param integer $promoDays promo days
     *
     * @return Offer
     */
    public function setPromoDays($promoDays)
    {
        $this->promoDays = $promoDays;

        return $this;
    }

    /**
     * Get days
     *
     * @return int
     */
    public function getPromoDays()
    {
        return $this->promoDays;
    }
    /**
     * Set expireDate
     *
     * @param \DateTime $expireDate expire date
     *
     * @return Offer
     */
    public function setExpireDate($expireDate)
    {
        $this->expireDate = $expireDate;

        return $this;
    }

    /**
     * Get expireDate
     *
     * @return \DateTime
     */
    public function getExpireDate()
    {
        return $this->expireDate;
    }

    /**
     * Set isPublish
     *
     * @param boolean $isPublish publication state
     *
     * @return Offer
     */
    public function setIsPublish($isPublish)
    {
        $this->isPublish = $isPublish;

        return $this;
    }

    /**
     * Get isPublish
     *
     * @return bool
     */
    public function getIsPublish()
    {
        return $this->isPublish;
    }
    /**
     * Set isTmp
     *
     * @param boolean $isTmp is tmp
     *
     * @return Offer
     */
    public function setIsTmp($isTmp)
    {
        $this->isTmp = $isTmp;

        return $this;
    }

    /**
     * Get isPublish
     *
     * @return bool
     */
    public function getIsTmp()
    {
        return $this->isTmp;
    }
    /**
     * Set isTmp
     *
     * @param boolean $isDelete is delete
     *
     * @return Offer
     */
    public function setIsDelete($isDelete)
    {
        $this->isDelete = $isDelete;

        return $this;
    }

    /**
     * Get isPublish
     *
     * @return bool
     */
    public function getIsDelete()
    {
        return $this->isDelete;
    }
     /**
     * Get hits
     *
     * @return integer
     */
    function getHits() {
        return $this->hits;
    }
    /**
     * Set hits
     *
     * @param integer $hits hits
     *
     * @return Offer
     */
    function setHits($hits) {
        $this->hits = $hits;
        return $this;
    }

     /**
     * Get paymentState
     *
     * @return integer
     */
    function getPaymentState() {
        return $this->paymentState;
    }
    /**
     * Set paymentState
     *
     * @param integer $paymentState payment state
     *
     * @return Offer
     */
    function setPaymentState($paymentState) {
        $this->paymentState = $paymentState;
        return $this;
    }

    /**
     * Get activationCode
     *
     * @return string
     */
    function getActivationCode() {
        return $this->activationCode;
    }
    /**
     * Set phone
     *
     * @param string $activationCode activation code
     *
     * @return Offer
     */
    function setActivationCode($activationCode) {
        $this->activationCode = $activationCode;
        return $this;
    }


    /**
     * Set rooms
     *
     * @param integer $rooms rooms
     *
     * @return Offer
     */
    public function setRooms($rooms)
    {
        $this->rooms = $rooms;

        return $this;
    }

    /**
     * Get rooms
     *
     * @return int
     */
    public function getRooms()
    {
        return $this->rooms;
    }

    /**
     * Set floor
     *
     * @param integer $floor floor
     *
     * @return Offer
     */
    public function setFloor($floor)
    {
        $this->floor = $floor;

        return $this;
    }

    /**
     * Get floor
     *
     * @return int
     */
    public function getFloor()
    {
        return $this->floor;
    }

    /**
     * Set storeys
     *
     * @param integer $storeys storeys
     *
     * @return Offer
     */
    public function setStoreys($storeys)
    {
        $this->storeys = $storeys;

        return $this;
    }

    /**
     * Get storeys
     *
     * @return int
     */
    public function getStoreys()
    {
        return $this->storeys;
    }

    /**
     * Set property
     *
     * @param Property $property property
     *
     * @return Offer
     */
    public function setProperty($property)
    {
        $this->property = $property;

        return $this;
    }

    /**
     * Get property
     *
     * @return Property
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * Set technicalCondition
     *
     * @param TechnicalCondition $technicalCondition technical condition
     *
     * @return Offer
     */
    public function setTechnicalCondition($technicalCondition)
    {
        $this->technicalCondition = $technicalCondition;

        return $this;
    }

    /**
     * Get technicalCondition
     *
     * @return TechnicalCondition
     */
    public function getTechnicalCondition()
    {
        return $this->technicalCondition;
    }

    /**
     * Set monthPayments
     *
     * @param float $monthPayments month payments
     *
     * @return Offer
     */
    public function setMonthPayments($monthPayments)
    {
        $this->monthPayments = $monthPayments;

        return $this;
    }

    /**
     * Get monthPayments
     *
     * @return float
     */
    public function getMonthPayments()
    {
        return $this->monthPayments;
    }
     /**
     * Get monthPaymentsCurrency
     *
     * @return Carrency
     */
    function getMonthPaymentsCurrency() {
        return $this->monthPaymentsCurrency;
    }
    /**
     * Set monthPaymentsCurrency
     *
     * @param Carrency $monthPaymentsCurrency month payments currency
     * @return Offer
     */
    function setMonthPaymentsCurrency($monthPaymentsCurrency) {
        $this->monthPaymentsCurrency = $monthPaymentsCurrency;
        return $this;
    }
    /**
     * Get exhibitionWindows
     *
     * @return ExhibitionWindows
     */
    function getExhibitionWindows() {
        return $this->exhibitionWindows;
    }
    /**
     * Set exhibition windows
     *
     * @param ExhibitionWindows $exhibitionWindows exhibition windows
     * @return Offer
     */
    function setExhibitionWindows($exhibitionWindows) {
        $this->exhibitionWindows = $exhibitionWindows;
        return $this;
    }

    /**
     * Set availableFrom
     *
     * @param string $availableFrom available from
     *
     * @return Offer
     */
    public function setAvailableFrom($availableFrom)
    {
        $this->availableFrom = $availableFrom;

        return $this;
    }

    /**
     * Get availableFrom
     *
     * @return string
     */
    public function getAvailableFrom()
    {
        return $this->availableFrom;
    }

    /**
     * Set abilityToWath
     *
     * @param string $abilityToWath ability to wath
     *
     * @return Offer
     */
    public function setAbilityToWath($abilityToWath)
    {
        $this->abilityToWath = $abilityToWath;

        return $this;
    }

    /**
     * Get abilityToWath
     *
     * @return string
     */
    public function getAbilityToWath()
    {
        return $this->abilityToWath;
    }

    /**
     * Set isLongPublish
     *
     * @param boolean $isLongPublish is long publish
     *
     * @return Offer
     */
    public function setIsLongPublish($isLongPublish)
    {
        $this->isLongPublish = $isLongPublish;

        return $this;
    }

    /**
     * Get isLongPublish
     *
     * @return bool
     */
    public function getIsLongPublish()
    {
        return $this->isLongPublish;
    }

    /**
     * Set isPromo
     *
     * @param boolean $isPromo is promo
     *
     * @return Offer
     */
    public function setIsPromo($isPromo)
    {
        $this->isPromo = $isPromo;

        return $this;
    }

    /**
     * Get isPromo
     *
     * @return bool
     */
    public function getIsPromo()
    {
        return $this->isPromo;
    }

    /**
     * Get hasFence
     *
     * @return bool has fence
     */
    function getHasFence() {
        return $this->hasFence;
    }

    /**
     * Set hasFence
     *
     * @param boolean $hasFence
     *
     * @return Offer
     */
    function setHasFence($hasFence) {
        $this->hasFence = $hasFence;
        return $this;
    }

    /**
     * Set promoExpire
     *
     * @param \DateTime $promoExpire promo expire
     *
     * @return Offer
     */
    public function setPromoExpire($promoExpire)
    {
        $this->promoExpire = $promoExpire;

        return $this;
    }

    /**
     * Get promoExpire
     *
     * @return \DateTime
     */
    public function getPromoExpire()
    {
        return $this->promoExpire;
    }
    /**
     * Get country
     *
     * @return Country
     */
    public function getCountry() {
        return $this->country;
    }
    /**
     * Set country
     *
     * @param Country $country country
     * @return Offer
     */
    public function setCountry($country) {
        $this->country = $country;
        return $this;
    }
    /**
     * Get category
     *
     * @return Category
     */
    public function getCategory() {
        return $this->category;
    }
    /**
     * Set category
     *
     * @param Category $category category
     * @return Offer
     */
    public function setCategory($category) {
        $this->category = $category;
        return $this;
    }
    /**
     * Get category
     *
     * @return Category
     */
    function getTransactionType() {
        return $this->transactionType;
    }
    /**
     * Set transactionType
     *
     * @param TransactionType $transactionType transaction type
     * @return Offer
     */
    function setTransactionType($transactionType) {
        $this->transactionType = $transactionType;
        return $this;
    }
    /**
     * Get type
     *
     * @return Type
     */
    function getType() {
        return $this->type;
    }
    /**
     * Set transactionType
     *
     * @param Type $type type
     * @return Offer
     */
    function setType($type) {
        $this->type = $type;
        return $this;
    }

    /**
     * Get currency
     *
     * @return Currency
     */
    public function getCurrency() {
        return $this->currency;
    }
    /**
     * Set currency
     *
     * @param Currency $currency currency
     * @return Offer
     */
    public function setCurrency($currency) {
        $this->currency = $currency;
        return $this;
    }
    /**
     * Get windows
     *
     * @return Windows
     */
    function getWindows() {
        return $this->windows;
    }
    /**
     * Set windows
     *
     * @param Windows $windows windows
     * @return Offer
     */
    function setWindows($windows) {
        $this->windows = $windows;
        return $this;
    }
    /**
     * Get roofCover
     *
     * @return RoofCover
     */
    function getRoofCover() {
        return $this->roofCover;
    }
    /**
     * Set roofCover
     *
     * @param RoofCover $roofCover roof cover
     * @return Offer
     */
    function setRoofCover($roofCover) {
        $this->roofCover = $roofCover;
        return $this;
    }
    /**
     * Get roof
     *
     * @return Roof
     */
    function getRoof() {
        return $this->roof;
    }
    /**
     * Set roof
     *
     * @param Roof $roof roof
     * @return Offer
     */
    function setRoof($roof) {
        $this->roof = $roof;
        return $this;
    }
    /**
     * Get localization
     *
     * @return Localization localization
     */
    function getLocalization() {
        return $this->localization;
    }
    /**
     * Set localization
     *
     * @param Localization $localization localization
     * @return Offer
     */
    function setLocalization($localization) {
        $this->localization = $localization;
        return $this;
    }
    /**
     * Get accessRoad
     *
     * @return AccessRoad
     */
    function getAccessRoad() {
        return $this->accessRoad;
    }
    /**
     * Set accessRoad
     *
     * @param AccessRoad $accessRoad access road
     * @return Offer
     */
    function setAccessRoad($accessRoad) {
        $this->accessRoad = $accessRoad;
        return $this;
    }
    /**
     * Get legalStatus
     *
     * @return LegalStatus
     */
    function getLegalStatus() {
        return $this->legalStatus;
    }
    /**
     * Set legalStatus
     *
     * @param LegalStatus $legalStatus legal status
     * @return Offer
     */
    function setLegalStatus($legalStatus) {
        $this->legalStatus = $legalStatus;
        return $this;
    }
    /**
     * Get dimensions
     *
     * @return string
     */
    function getDimensions() {
        return $this->dimensions;
    }
    /**
     * Set dimensions
     *
     * @param string $dimensions dimensions
     * @return Offer
     */
    function setDimensions($dimensions) {
        $this->dimensions = $dimensions;
        return $this;
    }
    /**
     * Get media
     *
     * @return Media
     */
    function getMedia() {
        return $this->media;
    }
    /**
     * Set media
     *
     * @param Media $media media
     * @return Offer
     */
    function setMedia($media) {
        $this->media = $media;
        return $this;
    }
    /**
     * Get additionalInfo
     *
     * @return AdditionalInfo
     */
    function getAdditionalInfo() {
        return $this->additionalInfo;
    }
    /**
     * Set additionalInfo
     *
     * @param AdditionalInfo $additionalInfo additional info
     * @return Offer
     */
    function setAdditionalInfo($additionalInfo) {
        $this->additionalInfo = $additionalInfo;
        return $this;
    }
    /**
     * Get neighborhood
     *
     * @return Neighborhood
     */
    function getNeighborhood() {
        return $this->neighborhood;
    }
    /**
     * Set neighborhood
     *
     * @param Neighborhood $neighborhood neighborhood
     * @return Offer
     */
    function setNeighborhood($neighborhood) {
        $this->neighborhood = $neighborhood;
        return $this;
    }
    /**
     * Get images
     *
     * @return OfferImage[]
     */
    function getImages() {
        return $this->images;
    }
    /**
     * Set images
     *
     * @param OfferImage[] $images collection of images
     * @return Offer
     */
    function setImages($images) {
        $this->images = $images;
        return $this;
    }
    /**
     * Get payments
     *
     * @return flat
     */
    public function getPayments() {
        return $this->payments;
    }
    /**
     * Set payments
     *
     * @param float $payments payments
     * @return Offer
     */
    public function setPayments($payments) {
        $this->payments = $payments;
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
     * @return Offer
     */
    public function setUser($user) {
        $this->user = $user;
        return $this;
    }
    /**
     * Get messages
     *
     * @return Message[]
     */
    public function getMessages() {
        return $this->messages;
    }
    /**
     * Set messages
     *
     * @param Message[] $messages collection of messages
     * @return Offer
     */
    public function setMessages($messages) {
        $this->messages = $messages;
        return $this;
    }
    /**
     * Get observed
     *
     * @return Observed[]
     */
    public function getObserved() {
        return $this->observed;
    }
    /**
     * Get office
     *
     * @return Office
     */
    public function getOffice() {
        return $this->office;
    }
    /**
     * Get allowedTags
     *
     * @return string
     */
    public function getAllowedTags() {
        return $this->allowedTags;
    }
    /**
     * Set observed
     *
     * @param observed[] $observed collection of observed offers
     * @return Offer
     */
    public function setObserved($observed) {
        $this->observed = $observed;
        return $this;
    }
    /**
     * Set office
     *
     * @param Office $office office
     * @return Offer
     */
    public function setOffice($office) {
        $this->office = $office;
        return $this;
    }
    /**
     * Set allowedTags
     *
     * @param string $allowedTags allowed html tags
     * @return Offer
     */
    public function setAllowedTags($allowedTags) {
        $this->allowedTags = $allowedTags;
        return $this;
    }
    /**
     * Get first image
     *
     * @return OfferImage
     */
    public function getFirstImage(){
        $criteria = Criteria::create()
            ->orderBy(array("ordering" => Criteria::ASC))
            ->setMaxResults(1)
        ;
        $images = $this->getImages()->matching($criteria);
        return $images[0];
    }
    /**
     * Generate signature
     *
     * @ORM\PrePersist()
     */
    public function generateSignature() {

        $type = mb_substr( $this->getTransactionType()->getName(), 0, 1,'UTF8');
        $category = mb_substr( $this->getCategory()->getName(), 0, 1,'UTF8');

        $user = $this->getUser();
        if(is_object($user) && in_array('ROLE_AGENT',$user->getRoles())){
            $userType = $user->getOffice()->getImportId();
        }elseif(is_object($user) && in_array('ROLE_BUISNESS',$user->getRoles())){
            $userType = 'ABO';
        }else {
        $userType = 'SMS';

        }
        $number = $this->generatNumberPartSignature();

        $this->signature = $type.$category.$userType.'-'.$number;

    }
     /**
      * Generate location index
      *
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function generateLocationIndex() {

        $this->locationIndex = strtolower($this->region . ' ' . $this->city . ' ' .$this->street);

    }
    /**
     * Change dir name
     *
     * @ORM\PostPersist()
     */
    public function changeDirName() {

        $oldPath = $this->getTmpUploadRootDir().$this->getTmpId();
        $newPath = $this->getTmpUploadRootDir().$this->getId();

        if(file_exists($oldPath)){
            rename($oldPath, $newPath);
        }

    }
    /**
     * Generate number part of signature
     * @param type $length
     * @return string
     */
    protected function generatNumberPartSignature($length=6){
        $characters = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $number = '';

        for ($p = 0; $p < $length; $p++) {
            $number .= $characters[mt_rand(0, strlen($characters)-1)];
        }

        return $number;
    }
    /**
     * Get tmp upload root dir
     * @return string
     */
    protected function getTmpUploadRootDir() {
        // the absolute directory path where uploaded documents should be saved
        return __DIR__ . '/../../../../web/uploads/offers/';
    }
    /**
     * Offer to string
     * @return string
     */
    public function __toString() {
        return $this->signature;
    }

    /**
     * Set oldImportId
     *
     * @param integer $oldImportId
     *
     * @return Offer
     */
    public function setOldImportId($oldImportId)
    {
        $this->oldImportId = $oldImportId;

        return $this;
    }

    /**
     * Get oldImportId
     *
     * @return integer
     */
    public function getOldImportId()
    {
        return $this->oldImportId;
    }

    /**
     * Set isSpecial
     *
     * @param boolean $isSpecial
     *
     * @return Offer
     */
    public function setIsSpecial($isSpecial)
    {
        $this->isSpecial = $isSpecial;

        return $this;
    }

    /**
     * Get isSpecial
     *
     * @return boolean
     */
    public function getIsSpecial()
    {
        return $this->isSpecial;
    }

    /**
     * Set phoneCounter
     *
     * @param integer $phoneCounter
     *
     * @return Offer
     */
    public function setPhoneCounter($phoneCounter)
    {
        $this->phoneCounter = $phoneCounter;

        return $this;
    }

    /**
     * Get phoneCounter
     *
     * @return integer
     */
    public function getPhoneCounter()
    {
        return $this->phoneCounter;
    }

    public function increasesPhoneCounter()
	{
		if(!$this->phoneCounter )
		{
			$this->phoneCounter = 1;
		}
		else
		{
			$this->phoneCounter++;
		}
		
		return $this;
	}
    
    /**
     * Add medium
     *
     * @param \App\OfferBundle\Entity\Media $medium
     *
     * @return Offer
     */
    public function addMedia(\App\OfferBundle\Entity\Media $medium)
    {
        $this->media[] = $medium;

        return $this;
    }

    /**
     * Remove medium
     *
     * @param \App\OfferBundle\Entity\Media $medium
     */
    public function removeMedia(\App\OfferBundle\Entity\Media $medium)
    {
        $this->media->removeElement($medium);
    }

    /**
     * Add additionalInfo
     *
     * @param \App\OfferBundle\Entity\AdditionalInfo $additionalInfo
     *
     * @return Offer
     */
    public function addAdditionalInfo(\App\OfferBundle\Entity\AdditionalInfo $additionalInfo)
    {
        $this->additionalInfo[] = $additionalInfo;

        return $this;
    }

    /**
     * Remove additionalInfo
     *
     * @param \App\OfferBundle\Entity\AdditionalInfo $additionalInfo
     */
    public function removeAdditionalInfo(\App\OfferBundle\Entity\AdditionalInfo $additionalInfo)
    {
        $this->additionalInfo->removeElement($additionalInfo);
    }

    /**
     * Add neighborhood
     *
     * @param \App\OfferBundle\Entity\Neighborhood $neighborhood
     *
     * @return Offer
     */
    public function addNeighborhood(\App\OfferBundle\Entity\Neighborhood $neighborhood)
    {
        $this->neighborhood[] = $neighborhood;

        return $this;
    }

    /**
     * Remove neighborhood
     *
     * @param \App\OfferBundle\Entity\Neighborhood $neighborhood
     */
    public function removeNeighborhood(\App\OfferBundle\Entity\Neighborhood $neighborhood)
    {
        $this->neighborhood->removeElement($neighborhood);
    }

    /**
     * Add image
     *
     * @param \App\OfferBundle\Entity\OfferImage $image
     *
     * @return Offer
     */
    public function addImage(\App\OfferBundle\Entity\OfferImage $image)
    {
        $this->images[] = $image;

        return $this;
    }

    /**
     * Remove image
     *
     * @param \App\OfferBundle\Entity\OfferImage $image
     */
    public function removeImage(\App\OfferBundle\Entity\OfferImage $image)
    {
        $this->images->removeElement($image);
    }

    /**
     * Add payment
     *
     * @param \App\PaymentBundle\Entity\Payment $payment
     *
     * @return Offer
     */
    public function addPayment(\App\PaymentBundle\Entity\Payment $payment)
    {
        $this->payments[] = $payment;

        return $this;
    }

    /**
     * Remove payment
     *
     * @param \App\PaymentBundle\Entity\Payment $payment
     */
    public function removePayment(\App\PaymentBundle\Entity\Payment $payment)
    {
        $this->payments->removeElement($payment);
    }

    /**
     * Add message
     *
     * @param \App\OfferBundle\Entity\Message $message
     *
     * @return Offer
     */
    public function addMessage(\App\OfferBundle\Entity\Message $message)
    {
        $this->messages[] = $message;

        return $this;
    }

    /**
     * Remove message
     *
     * @param \App\OfferBundle\Entity\Message $message
     */
    public function removeMessage(\App\OfferBundle\Entity\Message $message)
    {
        $this->messages->removeElement($message);
    }

    /**
     * Add observed
     *
     * @param \App\OfferBundle\Entity\Observed $observed
     *
     * @return Offer
     */
    public function addObserved(\App\OfferBundle\Entity\Observed $observed)
    {
        $this->observed[] = $observed;

        return $this;
    }

    /**
     * Remove observed
     *
     * @param \App\OfferBundle\Entity\Observed $observed
     */
    public function removeObserved(\App\OfferBundle\Entity\Observed $observed)
    {
        $this->observed->removeElement($observed);
    }

    /**
     * @return string
     */
    public function getSection(): string
    {
        return $this->section;
    }

    /**
     * @param string $section
     * @return Offer
     */
    public function setSection(string $section): Offer
    {
        $this->section = $section;

        return $this;
    }

    /**
     * @return string
     */
    public function getSubSection(): string
    {
        return $this->subSection;
    }

    /**
     * @param string $subSection
     * @return Offer
     */
    public function setSubSection(string $subSection): Offer
    {
        $this->subSection = $subSection;

        return $this;
    }

    /**
     * @return string
     */
    public function getOldName()
    {
        return $this->oldName;
    }

    /**
     * @param string $oldName
     * @return Offer
     */
    public function setOldName(string $oldName): Offer
    {
        $this->oldName = $oldName;

        return $this;
    }

    /**
     * Is Archive offer
     */
    public function isArchivOffer()
    {
        return $this->expireDate < (new \DateTime()) || $this->getIsPublish() == false;
    }

    /**
     * @return string
     */
    public function getSubdomain()
    {
        return $this->subdomain;
    }

    /**
     * @param string $subdomain
     * @return Offer
     */
    public function setSubdomain($subdomain): Offer
    {
        $this->subdomain = $subdomain;

        return $this;
    }

    /**
     * @return string
     */
    public function getVirtualWalkUrl()
    {
        return $this->virtualWalkUrl;
    }

    /**
     * @param string $virtualWalkUrl
     * @return Offer
     */
    public function setVirtualWalkUrl(string $virtualWalkUrl)
    {
        $this->virtualWalkUrl = $virtualWalkUrl;

        return $this;
    }

    
}
