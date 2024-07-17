<?php
/**
 * This file is part of the AppUserBundle package.
 *
 */
namespace App\UserBundle\Entity;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class User
 *
 * @author wojciech przygoda
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\UserBundle\Entity\UserRepository")
 * @ORM\HasLifecycleCallbacks
 */
class User implements AdvancedUserInterface, \Serializable
{
    /**
     * @const type users
     */
    const TYPE_USERS = 'user';
    /**
     * @const type agents
     */
    const TYPE_AGENTS = 'agent';
    /**
     * @const type clients
     */
    const TYPE_CLIENTS = 'client';
    /**
     * @const type office managers
     */
    const TYPE_OFFICE_MANAGER = 'officeManager';
    /**
     *
     * @var array client roles
     */
    public static $clientRoles = array('ROLE_USER',
                                       'ROLE_BUISNESS');
    /**
     *
     * @var array agent roles
     */
    public static $agentRoles = array('ROLE_AGENT');
    /**
     *
     * @var array user roles
     */
    public static $userRoles = array('ROLE_SUPER_ADMIN',
                                       'ROLE_ADMIN',
                                       'ROLE_MANAGER',
                                       'ROLE_AUTHOR',
                                       'ROLE_WRITER',
                                    );
    /**
     *
     * @var array  office manager roles
     */
    public static $officeManagerRoles = array(
                                       'ROLE_OFFICE',
                                    );
    /**
     * @var integer user id
     *
     * @ORM\Column(name="id", type="bigint", options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @var string username
     *
     * @ORM\Column(name="username", type="string", length=200,unique=true)
     */
    private $username;
    /**
     * @var string email
     *
     * @ORM\Column(name="email", type="string", length=200,nullable=true)
     */
    private $email;

    /**
     * @var string password
     *
     * @ORM\Column(name="password", type="string", length=200)
     */
    private $password;
    /**
     * @var string plain password
     *
     */
    private $plainPassword;
    /**
     * @var string salt
     *
     * @ORM\Column(name="salt", type="string", length=255)
     */
    private $salt;

    /**
     * @var string security hash
     *
     * @ORM\Column(name="security_hash", type="string", length=255, nullable=true)
     */
    private $securityHash;

    /**
     * @var boolean enabled state
     *
     * @ORM\Column(name="isEnabled", type="boolean")
     */
    private $isEnabled;
    /**
     * @var boolean office manager enabled state
     *
     * @ORM\Column(name="isOfficeManager", type="boolean")
     */
    private $isOfficeManager;
    /**
     * @var boolean is vompany
     *
     * @ORM\Column(name="isCompany", type="boolean")
     */
    private $isCompany;
    /**
     * @var integer import id
     *
     * @ORM\Column(name="importId", type="integer", nullable=true)
     */
    private $importId;
    /**
     * @var string legacy id
     *
     * @ORM\Column(name="legacyId", type="string", length=255, nullable=true)
     */
    private $legacyId;
    /**
     * @var integer import office id
     *
     * @ORM\Column(name="importOfficeId", type="integer", nullable=true)
     */
    private $importOfficeId;
    /**
     * @var integer state
     *
     * @ORM\Column(name="state", type="integer")
     */
    private $state;

    /**
     * @var \DateTime create date
     *
     * @ORM\Column(name="create_date", type="datetime")
     */
    private $createDate;

    /**
     * @var \DateTime modifification date
     *
     * @ORM\Column(name="modify_date", type="datetime", nullable=true)
     */
    private $modifyDate;
    /**
     * @var \DateTime change password request date
     *
     * @ORM\Column(name="passwordRequestDate", type="datetime", nullable=true)
     */
    private $passwordRequestDate;
    /**
     * @var \DateTime last activity
     *
     * @ORM\Column(name="lastActivity", type="datetime", nullable=true)
     */
    private $lastActivity;
    /**
     * @var \DateTime last login
     *
     * @ORM\Column(name="lastLogin", type="datetime", nullable=true)
     */
    private $lastlogin;
    /**
     * @var string last login ip
     *
     * @ORM\Column(name="lastLoginIp", type="string", length=255, nullable=true)
     */
    private $lastLoginIp;
    /**
     * @var string first name
     *
     * @ORM\Column(name="firstName", type="string", length=255, nullable=true)
     */
    private $firstName;
    /**
     * @var string last name
     *
     * @ORM\Column(name="lastName", type="string", length=255, nullable=true)
     */
    private $lastName;
    /**
     * @var string position
     *
     * @ORM\Column(name="position", type="string", length=255, nullable=true)
     */
    private $position;
    /**
     * @var string phone
     *
     * @ORM\Column(name="phone", type="string", length=20, nullable=true)
     */
    private $phone;
    /**
     * @var string mobile
     *
     * @ORM\Column(name="mobile", type="string", length=255, nullable=true)
     */
    private $mobile;
    /**
     * @var string licence
     *
     * @ORM\Column(name="licence", type="string", length=255, nullable=true)
     */
    private $licence;
    /**
     * @var string signature
     *
     * @ORM\Column(name="signature", type="string", length=255, nullable=true)
     */
    private $signature;
    /**
     * @var string avatar
     *
     * @ORM\Column(name="avatar", type="string", length=255, nullable=true)
     */
    private $avatar;
    /**
     * @var array roles
     *
     * @ORM\Column(name="roles", type="array")
     */
    private $roles;
    /**
     * @var Address[] collection of addresses
     *
     * @ORM\OneToMany(targetEntity="Address", mappedBy="user",cascade={"persist", "remove"})
     */
    protected $addresses;
    /**
     * @var Subscription[] collection of subscriptions
     *
     * @ORM\OneToMany(targetEntity="App\SubscriptionBundle\Entity\Subscription", mappedBy="user",cascade={"persist", "remove"})
     */
    protected $subscriptions;
   /**
    * @var Office office
    * @ORM\ManyToOne(targetEntity="App\OfficeBundle\Entity\Office", inversedBy="agents")
    */
    protected $office;
    /**
     * @var CompanyData company data
     *
     * @ORM\ManyToOne(targetEntity="CompanyData",cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="company_data_id", referencedColumnName="id")
     */
    private $companyData;
    /**
     * @var Offer[] collection of offers
     *
     * @ORM\OneToMany(targetEntity="App\OfferBundle\Entity\Offer", mappedBy="user")
     */
    protected $offers;
     /**
     * @var string $image image file name
     */
    private $file;
    /**
     *
     * @var string tmp file name
     */
    private $tmp;
    /**
     *
     * @var string user role
     */
    private $role;
    /**
     * @var integer ordering
     *
     * @ORM\Column(name="ordering", type="integer", nullable=true)
     */
    private $ordering;

    /**
     *
     * @var array list of changes
     */
    private $changes = array();
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->roles = array('ROLE_ADMIN');
        $this->addresses = new ArrayCollection();
        $this->subscriptions = new ArrayCollection();
        $this->isEnabled = true;
        $this->isCompany = false;
        $this->state = 1;
        $this->addresses = new ArrayCollection();
        $this->salt = md5(uniqid(null, true));
        $this->createDate = new \DateTime('now');
        $this->modifyDate = new \DateTime('now');
        $this->getAddresses()->add(new Address());
        $this->isOfficeManager = false;
    }
    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * Set username
     *
     * @param string $username username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }
    /**
     * Set email
     *
     * @param string $email email
     * @return User
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
     * Set password
     *
     * @param string $password password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }
    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }
    /**
     * Set plainPassword
     *
     * @param string $plainPassword plain password
     * @return User
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }
    /**
     * Get plainPassword
     *
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }
    /**
     * Set salt
     *
     * @param string $salt salt
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Get salt
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set securityHash
     *
     * @param string $securityHash security hash
     * @return User
     */
    public function setSecurityHash($securityHash)
    {
        $this->securityHash = $securityHash;

        return $this;
    }

    /**
     * Get securityHash
     *
     * @return string
     */
    public function getSecurityHash()
    {
        return $this->securityHash;
    }

    /**
     * Set isEnabled
     *
     * @param boolean $isEnabled enabled state
     * @return User
     */
    public function setIsEnabled($isEnabled)
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }

    /**
     * Get isEnabled
     *
     * @return boolean
     */
    public function getisEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * Set state
     *
     * @param integer $state state
     * @return User
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return integer
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set createDate
     *
     * @param \DateTime $createDate create date
     * @return User
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
     * @return User
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
     * Get passwordRequestDate
     *
     * @return \DateTime
     */
    public function getPasswordRequestDate() {
        return $this->passwordRequestDate;
    }
    /**
     * Set passwordRequestDate
     *
     * @param \DateTime $passwordRequestDate change password request date
     * @return User
     */
    public function setPasswordRequestDate($passwordRequestDate) {
        $this->passwordRequestDate = $passwordRequestDate;
        return $this;
    }
    /**
     * Get lastActivity
     *
     * @return \DateTime
     */
    public function getLastActivity() {
        return $this->lastActivity;
    }
    /**
     * Set lastActivity
     *
     * @param string $lastActivity last activity
     * @return User
     */
    public function setLastActivity(\DateTime $lastActivity) {
        $this->lastActivity = $lastActivity;
        return $this;
    }
    /**
     * Get lastLoginIp
     *
     * @return string
     */
    public function getLastLoginIp() {
        return $this->lastLoginIp;
    }
    /**
     * Set lastLoginIp
     *
     * @param string $lastLoginIp last login ip
     * @return User
     */
    public function setLastLoginIp($lastLoginIp) {
        $this->lastLoginIp = $lastLoginIp;
        return $this;
    }
    /**
     * Check if password request non expired
     * @param int $ttl max request time
     * @return boolean
     */
    public function isPasswordRequestNonExpired($ttl){

        return $this->getPasswordRequestDate() instanceof \DateTime &&
               $this->getPasswordRequestDate()->getTimestamp() + $ttl > time();
    }
    /**
     * Set roles
     *
     * @param array $roles roles
     * @return User
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get roles
     *
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }
    /**
     * Get isCompany
     *
     * @return boolean
     */
    public function getIsCompany() {
        return $this->isCompany;
    }
    /**
     * Set isCompany
     *
     * @param boolean $isCompany is company
     * @return User
     */
    public function setIsCompany($isCompany) {
        $this->isCompany = $isCompany;
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
     * Set importId
     *
     * @param string $importId import id
     * @return User
     */
    public function setImportId($importId) {
        $this->importId = $importId;
        return $this;
    }
    /**
     * Get importOfficeId
     *
     * @return string
     */
    public function getImportOfficeId() {
        return $this->importOfficeId;
    }
    /**
     * Set importOfficeId
     *
     * @param string $importOfficeId import office id
     * @return User
     */
    public function setImportOfficeId($importOfficeId) {
        $this->importOfficeId = $importOfficeId;
        return $this;
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
     * @return User
     */
    public function setLegacyId($legacyId) {
        $this->legacyId = $legacyId;
        return $this;
    }
    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName() {
        return $this->firstName;
    }
    /**
     * Set firstName
     *
     * @param string $firstName first name
     * @return User
     */
    public function setFirstName($firstName) {
        $this->firstName = $firstName;
        return $this;
    }
    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName() {
        return $this->lastName;
    }
    /**
     * Set lastName
     *
     * @param string $lastName lastName
     * @return User
     */
    public function setLastName($lastName) {
        $this->lastName = $lastName;
        return $this;
    }
    /**
     * Get position
     *
     * @return string
     */
    public function getPosition() {
        return $this->position;
    }
    /**
     * Set position
     *
     * @param string $position position
     * @return User
     */
    public function setPosition($position) {
        $this->position = $position;
        return $this;
    }
    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone() {
        return $this->phone;
    }
    /**
     * Set phone
     *
     * @param string $phone phone
     * @return User
     */
    public function setPhone($phone) {
        $this->phone = $phone;
        return $this;
    }
    /**
     * Get mobile
     *
     * @return string
     */
    public function getMobile() {
        return $this->mobile;
    }
    /**
     * Set mobile
     *
     * @param string $mobile mobile
     * @return User
     */
    public function setMobile($mobile) {
        $this->mobile = $mobile;
        return $this;
    }
    /**
     * Get licence
     *
     * @return string
     */
    public function getLicence() {
        return $this->licence;
    }
    /**
     * Set licence
     *
     * @param string $licence licence
     * @return User
     */
    public function setLicence($licence) {
        $this->licence = $licence;
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
     * Add address
     *
     * @param Address $address address
     * @return User
     */
    public function addAddress(Address $address){
        $address->setUser($this);
        $this->getAddresses()->add($address);
    }
    /**
     * Get address
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
     * Get office
     *
     * @return Office
     */
    public function getOffice() {
        return $this->office;
    }
    /**
     * Set office
     *
     * @param Office $office office
     * @return User
     */
    public function setOffice($office) {
        $this->office = $office;
        return $this;
    }
    /**
     * Get companyData
     *
     * @return CompanyData
     */
    public function getCompanyData() {
        return $this->companyData;
    }
    /**
     * Set companyData
     *
     * @param CompanyData $companyData company data
     * @return User
     */
    public function setCompanyData($companyData) {
        $this->companyData = $companyData;
        return $this;
    }
    /**
     * Get offers
     *
     * @return Offer[]
     */
    public function getOffers() {
        return $this->offers;
    }
    /**
     * Set offers
     *
     * @param Offer[] $offers collection of offers
     * @return User
     */
    public function setOffers($offers) {
        $this->offers = $offers;
        return $this;
    }
    /**
     * Get avatar
     *
     * @return string
     */
    public function getAvatar() {
        return $this->avatar;
    }
    /**
     * Set avatar
     *
     * @param string $avatar avatar
     * @return User
     */
    public function setAvatar($avatar) {
        $this->avatar = $avatar;
        return $this;
    }
    /**
     * Get string
     *
     * @return \DateTime
     */
    public function getLastlogin() {
        return $this->lastlogin;
    }
    /**
     * Set string
     *
     * @param \DateTime $lastlogin string
     * @return User
     */
    public function setLastlogin(\DateTime $lastlogin) {
        $this->lastlogin = $lastlogin;
        return $this;
    }
    /**
     * Set ordering
     *
     * @param int $ordering ordering
     * @return User
     */
    public function setOrdering($ordering) {
        $this->ordering = $ordering;
        return $this;
    }
    /**
     * Get ordering
     *
     * @return int
     */
    public function getOrdering() {
        return $this->ordering;
    }
    /**
     * Increment ordering
     *
     * @return User
     */
    public function incrementOrdering(){
        $this->ordering++;
        return $this;
    }

    /**
     * Decrement ordering
     * @return User
     */
    public function decrementOrdering(){
        $this->ordering--;
        return $this;
    }
    /**
     *
     * Check if value of field is changed
     *
     * @param string $field
     * @return bool
     */
    public function isChange($field){
        return isset($this->changes[$field]);
    }

    /**
     * Get changes
     *
     * @return array
     */
    public function getChanges(){
        return $this->changes;
    }

    /**
     * Get role
     *
     * @return string
     */
    public function getRole() {
        return $this->roles[0];
    }
    /**
     * Set role
     *
     * @param string $role role
     * @return User
     */
    public function setRole($role) {
        $this->roles = array($role);
        return $this;
    }
    /**
     * Get isOfficeManager
     *
     * @return boolean
     */
    public function getIsOfficeManager() {
        return $this->isOfficeManager;
    }
    /**
     * Check if is user office manager
     *
     * @return boolean
     */
    public function isOfficeManager() {

        if($this->getRole()=='ROLE_OFFICE'){
            return true;
        }

        return $this->isOfficeManager;
    }
    /**
     * Set isOfficeManager
     *
     * @param boolean $isOfficeManager is office manager
     * @return User
     */
    public function setIsOfficeManager($isOfficeManager) {
        $this->isOfficeManager = $isOfficeManager;
        return $this;
    }
    /**
     * Get subscriptions
     *
     * @return Subscription[]
     */
    public function getSubscriptions() {
        return $this->subscriptions;
    }
    /**
     * Set subscriptions
     *
     * @param Subscription[] $subscriptions subscriptions
     * @return User
     */
    public function setSubscriptions($subscriptions) {
        $this->subscriptions = $subscriptions;
        return $this;
    }
    /**
     * Check if user has active subscription
     *
     * @return boolean
     */
    public function hasActiveSubscription(){
        if($this->getActiveSubscription()){
            return true;
        }
        return false;
    }
    /**
     * Get active subsription
     *
     * @return Subscription
     */
    public function getActiveSubscription(){
        $criteria = Criteria::create()
            ->where(Criteria::expr()->gte("expireDate", new \DateTime()))
        ;

        $subscriptions = $this->getSubscriptions()->matching($criteria);

        if(count($subscriptions)==0){
            return null;
        }
        if(count($subscriptions) == 1){
            $subscription = $subscriptions[0];
            if($subscription->getNumberOfUsed() < $subscription->getNumberOfAvailable() ||
                    $subscription->getNumberOfAvailable()< 0){
                return $subscription;
            }
        }

        foreach($subscriptions as $subscription){
            if($subscription->getNumberOfUsed() < $subscription->getNumberOfAvailable() ||
                    $subscription->getNumberOfAvailable()< 0){
                return $subscription;
            }
        }
        return null;
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
     * @return User
     */
    public function setSignature($signature) {
        $this->signature = $signature;
        return $this;
    }
    /**
     * Erase credentials
     *
     * @inheritDoc
     */
    public function eraseCredentials()
    {
    }
    /**
    * Serialize user data
    *
    * @see \Serializable::serialize()
    */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            $this->salt,
        ));
    }

    /**
     * Unserialize user data
     *
     * @see \Serializable::unserialize()
     * @param string $serialized serialized data
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            $this->salt
        ) = unserialize($serialized);
    }
    /**
     * Check if user account non expired
     *
     * @return boolean
     */
    public function isAccountNonExpired()
    {
        return true;
    }
    /**
     * Check if user account is non locked
     *
     * @return boolean
     */
    public function isAccountNonLocked()
    {
        return true;
    }
    /**
     * Check if user credentials non expired
     *
     * @return boolean
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }
    /**
     * Check if user is enabled
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->isEnabled;
    }
    /**
     * Get file
     *
     * @return string
     */
    public function getFile() {
        return $this->file;
    }
    /**
     * Set file
     *
     * @param string $file file
     * @return User
     */
    public function setFile(UploadedFile $file = null) {
        $this->file = $file;
        $this->tmp = $this->avatar;
        $this->avatar = uniqid();
    }
    /**
     * Get full image path
     *
     * @return string
     */
    public function getFullImagePath() {
        return null === $this->avatar ? null : $this->getUploadRootDir(). $this->avatar;
    }
    /**
     * Get upload root dir
     *
     * @return string
     */
    protected function getUploadRootDir() {
        // the absolute directory path where uploaded documents should be saved
        return $this->getTmpUploadRootDir().$this->getId()."/";
    }
    /**
     * Get tmp upload root dir
     *
     * @return string
     */
    protected function getTmpUploadRootDir() {
        // the absolute directory path where uploaded documents should be saved
        return __DIR__ . '/../../../../web/uploads/avatar/';
    }

    /**
     * Upload image
     *
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function uploadImage() {
        if (null === $this->file) {
            return;
        }
        $filename = sha1(uniqid(mt_rand(), true)).'.'.$this->file->guessExtension();
        if(!$this->id){
            $this->file->move($this->getTmpUploadRootDir(), $filename);
        }else{

            if(is_file($this->getUploadRootDir().$this->tmp)){
              unlink($this->getUploadRootDir().$this->tmp);
            }

            $this->file->move($this->getUploadRootDir(), $filename);
        }
        $this->setAvatar($filename);
    }

    /**
     * Move image
     *
     * @ORM\PostPersist()
     */
    public function moveImage()
    {
        if (null === $this->file) {
            return;
        }
        if(!is_dir($this->getUploadRootDir())){
            mkdir($this->getUploadRootDir());
        }
        copy($this->getTmpUploadRootDir().$this->avatar, $this->getFullImagePath());
        @unlink($this->getTmpUploadRootDir().$this->avatar);
    }

    /**
     * Remove image
     *
     * @ORM\PreRemove()
     */
    public function removeImage()
    {
        if($this->avatar != null){
          @unlink($this->getFullImagePath());
        }

        @rmdir($this->getUploadRootDir());
    }
    /**
     * Get type
     *
     * @return string
     */
    public function getType(){
        if(in_array($this->roles[0], self::$agentRoles)){
            return self::TYPE_AGENTS;
        }
        if(in_array($this->roles[0], self::$clientRoles)){
            return self::TYPE_CLIENTS;
        }
        if(in_array($this->roles[0], self::$userRoles)){
            return self::TYPE_USERS;
        }
        if(in_array($this->roles[0], self::$officeManagerRoles)){
            return self::TYPE_OFFICE_MANAGER;
        }
    }
    /**
     * Check if user is agent
     *
     * @return boolean
     */
    public function isAgent(){
        if(in_array('ROLE_AGENT', $this->roles)){
            return true;
        }

        return false;
    }
    /**
     * Check if user has nip
     *
     * @return boolean
     */
    public function hasNip(){
        $companyData = $this->getCompanyData();

        if(!is_object($companyData)){
            return false;
        }
        $nip = $companyData->getNip();

        if(strlen($nip)==10){
            return true;
        }
        echo 'no company data' . $nip;
        return false;
    }
    /**
     * User to string
     *
     * @return string
     */
    public function __toString() {
        if($this->firstName!='' && $this->lastName!=''){
            return $this->firstName.' '.$this->lastName;
        }
        return $this->username;
    }
}
