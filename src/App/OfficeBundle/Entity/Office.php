<?php
/**
 * This file is part of the AppOfficeBundle package.
 *
 */
namespace App\OfficeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use App\UserBundle\Entity\Address;
use App\OfficeBundle\Entity\AdditionalService;
/**
 * Class Office
 *
 * @author wojciech przygoda
 *
 * @ORM\Table(name="office")
 * @ORM\Entity(repositoryClass="App\OfficeBundle\Entity\OfficeRepository")
 */
class Office
{
    /**
     * @const office type properties
     */
    const TYPE_PROPERTIES = 1;
    /**
     * @const office type credit
     */
    const TYPE_CREDIT = 2;
    /**
     * @const office type credit and properties
     */
    const TYPE_PROPERTIES_CREDIT = 3;
    /**
     * @const office type other
     */
    const TYPE_PROPERTIES_OTHER = 4;
    /**
     * @var int office id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string name
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;
    /**
     * @var string display name
     *
     * @ORM\Column(name="displayName", type="string", length=255, nullable=true)
     */
    private $displayName;
    /**
     * @var string display street
     *
     * @ORM\Column(name="displayStreet", type="string", length=255, nullable=true)
     */
    private $displayStreet;
    /**
     * @var string signature
     *
     * @ORM\Column(name="signature", type="string", length=255, nullable=true)
     */
    private $signature;
    /**
     * @var string subdomain
     *
     * @ORM\Column(name="subdomain", type="string", length=255, nullable=true)
     */
    private $subdomain;
    /**
     * @var string lat
     *
     * @ORM\Column(name="lat", type="string", length=255, nullable=true)
     */
    private $lat;
    /**
     * @var string lng
     *
     * @ORM\Column(name="lng", type="string", length=255, nullable=true)
     */
    private $lng;
    /**
     * @var string phone
     *
     * @ORM\Column(name="phone", type="string", length=20, nullable=true)
     */
    private $phone;

    /**
     * @var string mobile
     *
     * @ORM\Column(name="mobile", type="string", length=20, nullable=true)
     */
    private $mobile;

    /**
     * @var string fax
     *
     * @ORM\Column(name="fax", type="string", length=20, nullable=true)
     */
    private $fax;

    /**
     * @var string email
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var string www
     *
     * @ORM\Column(name="www", type="string", length=255, nullable=true)
     */
    private $www;
    /**
     * @var string credit offer
     *
     * @ORM\Column(name="creditOffer", type="string", length=255, nullable=true)
     */
    private $creditOffer;
    /**
     * @var string credit offer url
     *
     * @ORM\Column(name="creditOfferUrl", type="string", length=200, nullable=true)
     */
    private $creditOfferUrl;
    /**
     * @var string credit offer url
     *
     * @ORM\Column(name="developmentOfferUrl", type="string", length=200, nullable=true)
     */
    private $developmentOfferUrl;
    /**
     * @var \DateTime create date
     *
     * @ORM\Column(name="createDate", type="datetime")
     */
    private $createDate;

    /**
     * @var \DateTime modifification date
     *
     * @ORM\Column(name="modifyDate", type="datetime")
     */
    private $modifyDate;

    /**
     * @var int import id
     *
     * @ORM\Column(name="importId", type="integer", nullable=true)
     */
    private $importId;
    /**
     * @var int legacy id
     *
     * @ORM\Column(name="legacyId", type="integer", nullable=true)
     */
    private $legacyId;
    /**
     * @var int type
     *
     * @ORM\Column(name="type", type="integer", nullable=true)
     */
    private $type;
    /**
     * @var bool publication state
     *
     * @ORM\Column(name="isPublish", type="boolean")
     */
    private $isPublish;

    /**
     * @var bool soft delete state
     *
     * @ORM\Column(name="isDelete", type="boolean")
     */
    private $isDelete;
    /**
     * @var string meta description
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;
    /**
     * @var User[] collection of agents
     *
     * @ORM\OneToMany(targetEntity="App\UserBundle\Entity\User", mappedBy="office",cascade={"persist", "remove"})
     * @ORM\OrderBy({"ordering" = "DESC"})
     */
    protected $agents;
    /**
     * @var Address[] collection of addresses
     *
     * @ORM\OneToMany(targetEntity="App\UserBundle\Entity\Address", mappedBy="office",cascade={"persist", "remove"})
     */
    protected $addresses;
    /**
     * @var AdditionalService[] collection off additional services
     * @ORM\OneToMany(targetEntity="AdditionalService", mappedBy="office",cascade={"persist", "remove"})
     */
    protected $additionalServices;
    /**
     * @var OfficeImage collection of office images
     *
     * @ORM\OneToMany(targetEntity="OfficeImage", mappedBy="office",cascade={"persist", "remove"})
     * @ORM\OrderBy({"ordering" = "ASC"})
     */
    protected $images;

    /**
     * @var Link[]
     *
     * @ORM\OneToMany(targetEntity="Link", mappedBy="office",cascade={"persist", "remove"})
     */
    protected $links;

    /**
     * Constructor
     */
    public function __construct() {
        $this->agents = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->addresses  = new ArrayCollection();
        $this->additionalServices = new ArrayCollection();
        $this->links = new ArrayCollection();
        $this->createDate = new \DateTime();
        $this->modifyDate = new \DateTime();
        $this->isDelete = 0;
        $this->isPublish = 1;
        $this->type = self::TYPE_PROPERTIES;
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
     * Set name
     *
     * @param string $name name
     *
     * @return Office
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
    /**
     * Get type
     *
     * @return int
     */
    public function getType() {
        return $this->type;
    }
    /**
     * Set type
     *
     * @param int $type type
     * @return Office
     */
    public function setType($type) {
        $this->type = $type;
        return $this;
    }

    /**
     * Get displayName
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }
    /**
     * Set displayName
     *
     * @param string $displayName display name
     *
     * @return Office
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;

        return $this;
    }
    /**
     * Get displayStreet
     *
     * @return string
     */
    public function getDisplayStreet()
    {
        return $this->displayStreet;
    }
    /**
     * Set displayStreet
     *
     * @param string $displayStreet display street
     *
     * @return Office
     */
    public function setDisplayStreet($displayStreet)
    {
        $this->displayStreet = $displayStreet;

        return $this;
    }
    /**
     * Get signature
     *
     * @return string
     */
    public function getSignature() {
        return $this->signature;
    }
    /**
     * Set signature
     *
     * @param string $signature signature
     *
     * @return Office
     */
    public function setSignature($signature) {
        $this->signature = $signature;
        return $this;
    }
    /**
     * Get lat
     *
     * @return string
     */
    public function getLat() {
        return $this->lat;
    }
    /**
     * Set lat
     *
     * @param string $lat lat
     *
     * @return Office
     */
    public function setLat($lat) {
        $this->lat = $lat;
        return $this;
    }
    /**
     * Get lng
     *
     * @return string
     */
    public function getLng() {
        return $this->lng;
    }
    /**
     * Set lng
     *
     * @param string $lng lng
     *
     * @return Office
     */
    public function setLng($lng) {
        $this->lng = $lng;
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
     * Set phone
     *
     * @param string $phone phone
     *
     * @return Office
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
     * Set mobile
     *
     * @param string $mobile mobile
     *
     * @return Office
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;

        return $this;
    }

    /**
     * Get mobile
     *
     * @return string
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * Set fax
     *
     * @param string $fax fax
     *
     * @return Office
     */
    public function setFax($fax)
    {
        $this->fax = $fax;

        return $this;
    }

    /**
     * Get fax
     *
     * @return string
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * Set email
     *
     * @param string $email email
     *
     * @return Office
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
     * Set www
     *
     * @param string $www www
     *
     * @return Office
     */
    public function setWww($www)
    {
        $this->www = $www;

        return $this;
    }

    /**
     * Get www
     *
     * @return string
     */
    public function getWww()
    {
        return $this->www;
    }

    /**
     * Set createDate
     *
     * @param \DateTime $createDate create date
     *
     * @return Office
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
     * @return Office
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
     * Set importId
     *
     * @param integer $importId import id
     *
     * @return Office
     */
    public function setImportId($importId)
    {
        $this->importId = $importId;

        return $this;
    }

    /**
     * Get importId
     *
     * @return int
     */
    public function getImportId()
    {
        return $this->importId;
    }
    /**
     * Get legacyId
     *
     * @return string
     */
    public function getLegacyId() {
        return $this->legacyId;
    }
    /**
     * Set legacyId
     *
     * @param string $legacyId legacy id
     *
     * @return Office
     */
    public function setLegacyId($legacyId) {
        $this->legacyId = $legacyId;
        return $this;
    }

    /**
     * Set isPublish
     *
     * @param boolean $isPublish publication state
     *
     * @return Office
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
     * Set isDelete
     *
     * @param boolean $isDelete soft delte state
     *
     * @return Office
     */
    public function setIsDelete($isDelete)
    {
        $this->isDelete = $isDelete;

        return $this;
    }

    /**
     * Get isDelete
     *
     * @return bool
     */
    public function getIsDelete()
    {
        return $this->isDelete;
    }
    /**
     * Get agents
     *
     * @return User[]
     */
    public function getAgents() {
        $agents = new ArrayCollection();

        foreach ($this->agents as $agent){
            if($agent->isAgent()){
                $agents->add($agent);
            }
        }
        return $agents;
    }
    /**
     * Set agents
     *
     * @param User[] $agents collection of agents
     *
     * @return Office
     */
    public function setAgents($agents) {


        $this->agents = $agents;
        return $this;
    }
    /**
     * Get addresses
     *
     * @return Address[]
     */
    public function getAddresses() {
        return $this->addresses;
    }
    /**
     * Set addresses
     *
     * @param Address[] $addresses collection of addresses
     *
     * @return Office
     */
    public function setAddresses($addresses) {
        $this->addresses = $addresses;
        return $this;
    }
    /**
     * Set address
     *
     * @param string $address address
     *
     * @return Office
     */
    public function addAddress(Address $address){
        $address->setOffice($this);
        $this->getAddresses()->add($address);
    }
    /**
     * Get creditOfferUrl
     *
     * @return string
     */
    public function getCreditOfferUrl() {
        return $this->creditOfferUrl;
    }
    /**
     * Set creditOfferUrl
     *
     * @param string $creditOfferUrl credit offer url
     *
     * @return Office
     */
    public function setCreditOfferUrl($creditOfferUrl) {
        $this->creditOfferUrl = $creditOfferUrl;
        return $this;
    }
    /**
     * Get default address
     *
     * @return Address
     */
    public function getDefaultAddress(){
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq("isDefault", true))
            ->setMaxResults(1)
        ;

        $addresses = $this->getAddresses()->matching($criteria);
        return $addresses[0];
    }
    /**
     * Office to string
     *
     * @return string
     */
    public function __toString() {
        return $this->name . '/'. $this->getDefaultAddress()->getStreet();
    }
    /**
     * Get creditOffer
     *
     * @return string
     */
    public function getCreditOffer() {
        return $this->creditOffer;
    }
    /**
     * Set creditOffer
     *
     * @param string $creditOffer credit offer
     *
     * @return Office
     */
    public function setCreditOffer($creditOffer) {
        $this->creditOffer = $creditOffer;
        return $this;
    }
    /**
     * Get additionalServices
     *
     * @return AdditionalService[]
     */
    public function getAdditionalServices() {
        return $this->additionalServices;
    }
    /**
     * Set additionalServices
     *
     * @param AdditionalService[] $additionalServices additional services
     *
     * @return Office
     */
    public function setAdditionalServices($additionalServices) {
        $this->additionalServices = $additionalServices;
        return $this;
    }
    /**
     * Get images
     *
     * @return OfficeImage[]
     */
    public function getImages() {
        return $this->images;
    }
    /**
     * Set images
     *
     * @param OfficeImage[] $images office images
     *
     * @return Office
     */
    public function setImages($images) {
        $this->images = $images;
        return $this;
    }
    /**
     * Set additionalServices
     *
     * @param AdditionalService[] $additionalService additional services
     *
     * @return Office
     */
    public function addAdditionalService(AdditionalService $additionalService) {
        $this->additionalServices->add($additionalService);
        $additionalService->setOffice($this);
        return $this;
    }
    /**
     * Set additionalServices
     *
     * @param AdditionalService[] $additionalService additional services
     *
     * @return Office
     */
    public function removeAdditionalService(AdditionalService $additionalService)
    {
        $this->additionalServices->removeElement($additionalService);
        $additionalService->setOffice(null);
        return $this;
    }
    /**
     * Get subdomain
     *
     * @return string
     */
    public function getSubdomain() {
        return $this->subdomain;
    }
    /**
     * Set subdomain
     *
     * @param string $subdomain office subdomain
     *
     * @return Office
     */
    public function setSubdomain($subdomain) {
        $this->subdomain = $subdomain;
        return $this;
    }
    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getDevelopmentOfferUrl()
    {
        return $this->developmentOfferUrl;
    }

    /**
     * @param string $developmentOfferUrl
     * @return Office
     */
    public function setDevelopmentOfferUrl($developmentOfferUrl)
    {
        $this->developmentOfferUrl = $developmentOfferUrl;

        return $this;
    }

    /**
     * @return Link[]
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     * @param Link[] $links
     * @return Office
     */
    public function setLinks($links)
    {
        $this->links = $links;

        return $this;
    }

}

