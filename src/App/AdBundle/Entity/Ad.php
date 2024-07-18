<?php
/**
 * This file is part of the AppAdBundle package.
 *
 */
namespace App\AdBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Class Ad
 *
 * @author wojciech przygoda
 *
 * @ORM\Table(name="ad")
 * @ORM\Entity(repositoryClass="App\AdBundle\Entity\AdRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Ad
{
    /**
     * @var int ad id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string ad name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string ad code
     *
     * @ORM\Column(name="code", type="text", nullable=true)
     */
    private $code;

    /**
     * @var string ad file name
     *
     * @ORM\Column(name="file", type="string", length=255, nullable=true)
     */
    private $file;

    /**
     * @var string  ad mobileFile name
     *
     * @ORM\Column(name="mobileFile", type="string", length=255, nullable=true)
     */
    private $mobileFile;
    /**
     * @var int ad clicks
     *
     * @ORM\Column(name="clicks", type="integer")
     */
    private $clicks;
    /**
     * @var int ad hits
     *
     * @ORM\Column(name="hits", type="integer")
     */
    private $hits;
    /**
     * @var int ad clickLimit
     *
     * @ORM\Column(name="clickLimit", type="integer", nullable=true)
     */
    private $clickLimit;

    /**
     * @var int ad displayLimit
     *
     * @ORM\Column(name="displayLimit", type="integer", nullable=true)
     */
    private $displayLimit;

    /**
     * @var \DateTime ad startDate
     *
     * @ORM\Column(name="startDate", type="datetime", nullable=true)
     */
    private $startDate;

    /**
     * @var \DateTime ad endDate
     *
     * @ORM\Column(name="endDate", type="datetime", nullable=true)
     */
    private $endDate;

    /**
     * @var string ad url
     *
     * @ORM\Column(name="url", type="text", nullable=true)
     */
    private $url;

    /**
     * @var int ad weight
     *
     * @ORM\Column(name="weight", type="integer", nullable=true)
     */
    private $weight;

    /**
     * @var int ad height
     *
     * @ORM\Column(name="height", type="integer", nullable=true)
     */
    private $height;

    /**
     * @var integer ad ordering
     *
     * @ORM\Column(name="ordering", type="integer")
     */
    private $ordering;
    /**
     * @var bool ad isPublish
     *
     * @ORM\Column(name="isPublish", type="boolean")
     */
    private $isPublish;

    /**
     * @var AdPosition ad position
     *
     * @ORM\ManyToOne(targetEntity="AdPosition", inversedBy="ads")
     */
    protected $position;
    /**
     * @var Offer ad offer
     *
     * @ORM\ManyToOne(targetEntity="App\OfferBundle\Entity\Offer", inversedBy="ads")
     */
    protected $offer;
    /**
     * @var Office ad office
     *
     * @ORM\ManyToOne(targetEntity="App\OfficeBundle\Entity\Office", inversedBy="ads")
     */
    protected $office;
    /**
     * @var string  ad file
     * @Assert\File( maxSize = "10240k", mimeTypesMessage = "Please upload a valid Image")
     */
    private $adFile;
    /**
     * @var string  ad mobilefile
     * @Assert\File( maxSize = "10240k", mimeTypesMessage = "Please upload a valid Image")
     */
    private $adMobileFile;
    /**
     * @var string  ad tmpAdFile
     */
    private $tmpAdFile;
    /**
     * @var string  ad tmpAdMobileFile;
     */
    private $tmpAdMobileFile;
    /**
     * @var array list of changes
     */
    private $changes = array();

    /**
     * Constructor
     */
    function __construct() {
        $this->hits = 0;
        $this->clicks = 0;
        $this->isPublish = true;
        $this->height = 0;
        $this->weight = 0;
        $this->clickLimit = 0;
        $this->displayLimit = 0;

        $this->startDate = new \DateTime();
        $this->endDate = new \DateTime();
        $this->endDate->modify('+ 1 month');
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
     * @param string $name
     *
     * @return Ad
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
     * Set code
     *
     * @param string $code
     *
     * @return Ad
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set file
     *
     * @param string $file
     *
     * @return Ad
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set mobileFile
     *
     * @param string $mobileFile
     *
     * @return Ad
     */
    public function setMobileFile($mobileFile)
    {
        $this->mobileFile = $mobileFile;

        return $this;
    }

    /**
     * Get mobileFile
     *
     * @return string
     */
    public function getMobileFile()
    {
        return $this->mobileFile;
    }
    /**
     * Get clicks
     *
     * @return int
     */
    public function getClicks() {
        return $this->clicks;
    }
    /**
     * Set clicks
     *
     * @param string $clicks
     *
     * @return Ad
     */
    public function setClicks($clicks) {
        $this->clicks = $clicks;
        return $this;
    }
    /**
     * Get hits
     *
     * @return int
     */
    public function getHits() {
        return $this->hits;
    }
    /**
     * Set hits
     *
     * @param string $hits
     *
     * @return Ad
     */
    public function setHits($hits) {
        $this->hits = $hits;
    }

    /**
     * Set clickLimit
     *
     * @param integer $clickLimit
     *
     * @return Ad
     */
    public function setClickLimit($clickLimit)
    {
        $this->clickLimit = $clickLimit;

        return $this;
    }

    /**
     * Get clickLimit
     *
     * @return int
     */
    public function getClickLimit()
    {
        return $this->clickLimit;
    }

    /**
     * Set displaylimit
     *
     * @param integer $displayLimit
     *
     * @return Ad
     */
    public function setDisplayLimit($displayLimit)
    {
        $this->displayLimit = $displayLimit;

        return $this;
    }

    /**
     * Get displaylimit
     *
     * @return int
     */
    public function getDisplayLimit()
    {
        return $this->displayLimit;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     *
     * @return Ad
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     *
     * @return Ad
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return Ad
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set weight
     *
     * @param integer $weight
     *
     * @return Ad
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Get weight
     *
     * @return int
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set height
     *
     * @param integer $height
     *
     * @return Ad
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Get height
     *
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
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
     * Set ordering
     *
     * @param integer $ordering
     *
     * @return Ad
     */
    public function setOrdering($ordering) {
        $this->ordering = $ordering;
        return $this;
    }
    /**
     * Get height
     *
     * @return bool
     */
    public function getIsPublish() {
        return $this->isPublish;
    }
    /**
     * Set isPublish
     *
     * @param integer $isPublish
     *
     * @return Ad
     */
    public function setIsPublish($isPublish) {
        $this->isPublish = $isPublish;
        return $this;
    }
    /**
     * Set position
     *
     * @param integer $position
     *
     * @return Ad
     */
    public function setPosition($position) {
        $this->changes['position'][] = $this->position;
        $this->changes['position'][] = $position;
        $this->position = $position;
        return $this;
    }
    /**
     * Get position
     *
     * @return AdPosition
     */
    public function getPosition() {
        return $this->position;
    }
    /**
     * Increment clicks
     *
     * @return Ad
     */
    public function incrementClicks(){
        $this->clicks++;
        return $this;
    }
    /**
     * Increment hits
     *
     * @return Ad
     */
    public function incrementHits(){
        $this->hits++;
        return $this;
    }
    /**
     * Increment ordering
     *
     * @return Ad
     */
    public function incrementOrdering(){
        $this->ordering++;
        return $this;
    }
    /**
     * Decrement ordering
     *
     * @return Ad
     */
    public function decrementOrdering(){
        $this->ordering--;
        return $this;
    }
    /**
     * Get adFile
     *
     * @return string
     */
    public function getAdFile() {
        return $this->adFile;
    }
    /**
     * Set adFile
     *
     * @param integer $adFile
     *
     * @return Ad
     */
    public function setAdFile(UploadedFile $adFile = null) {
        $this->adFile = $adFile;

        if($this->adFile !==null){
            $this->tmpAdFile = $this->file;
            $this->setFile(uniqid());
        }

        return $this;
    }
    /**
     * Get adMobileFile
     *
     * @return string
     */
    public function getAdMobileFile() {
        return $this->adMobileFile;
    }
    /**
     * Set adMobileFile
     *
     * @param integer $adMobileFile
     *
     * @return Ad
     */
    public function setAdMobileFile(UploadedFile $adMobileFile = null) {
        $this->adMobileFile = $adMobileFile;
        if($this->adMobileFile !==null){
            $this->tmpAdMobileFile = $this->mobileFile;
            $this->setMobileFile(uniqid());
        }
        return $this;
    }
    /**
     * Get tmpAdFile
     *
     * @return string
     */
    public function getTmpAdFile() {
        return $this->tmpAdFile;
    }
    /**
     * Set tmpAdFile
     *
     * @param integer $tmpAdFile
     *
     * @return Ad
     */
    public function setTmpAdFile($tmpAdFile) {
        $this->tmpAdFile = $tmpAdFile;
        return $this;
    }
    /**
     * Get tmpAdMobileFile
     *
     * @return string
     */
    public function getTmpAdMobileFile() {
        return $this->tmpAdMobileFile;
    }
    /**
     * Set tmpAdMobileFile
     *
     * @param integer $tmpAdMobileFile
     *
     * @return Ad
     */
    public function setTmpAdMobileFile($tmpAdMobileFile) {
        $this->tmpAdMobileFile = $tmpAdMobileFile;
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
     * @param integer $office
     *
     * @return Ad
     */
    public function setOffice($office) {
        $this->office = $office;
        return $this;
    }
    /**
     * Get full file path
     *
     * @return null|string
     */
    public function getFullFilePath() {
        return null === $this->file ? null : $this->getUploadRootDir(). $this->file;
    }
    /**
     * Get full mobile file path
     *
     * @return null|string
     */
    public function getFullMobileFilePath() {
        return null === $this->mobileFile ? null : $this->getUploadRootDir(). $this->mobileFile;
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
        return __DIR__ . '/../../../../web/uploads/ads/';
    }

    /**
     * Upload file
     *
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function uploadFile() {
        // the file property can be empty if the field is not required

        $adFile = $this->moveUploadFile($this->adFile, $this->tmpAdFile);
        $adMobileFile = $this->moveUploadFile($this->adMobileFile, $this->tmpAdMobileFile);

        if($adFile){
            $this->setFile($adFile);
        }
        if($adMobileFile){
            $this->setMobileFile($adMobileFile);
        }

    }
    /**
     * Get offer
     *
     * @return Offier
     */
    public function getOffer() {
        return $this->offer;
    }
    /**
     * Set offer
     *
     * @param integer $offer
     *
     * @return Ad
     */
    public function setOffer($offer) {
        $this->offer = $offer;
        return $this;
    }

    /**
     * Move files
     *
     * @ORM\PostPersist()
     *
     */
    public function moveFiles()
    {
        if(!is_dir($this->getUploadRootDir())){
            mkdir($this->getUploadRootDir());
        }

        if (null !== $this->adFile) {
            copy($this->getTmpUploadRootDir().$this->file, $this->getFullFilePath());
            $this->unlinkFile($this->getTmpUploadRootDir().$this->file);
        }

        if (null !== $this->adMobileFile) {
            copy($this->getTmpUploadRootDir().$this->mobileFile, $this->getFullMobileFilePath());
            $this->unlinkFile($this->getTmpUploadRootDir().$this->mobileFile);
        }
    }
    /**
     * Remove files
     *
     * @ORM\PreRemove()
     */
    public function removeFiles()
    {
        if($this->file != null){
            $this->unlinkFile($this->getFullFilePath());
        }
        if($this->mobileFile != null){
            $this->unlinkFile($this->getFullMobileFilePath());
        }
        @rmdir($this->getUploadRootDir());
    }

    /**
     *
     * Move upload file
     *
     * @param UploadedFile $file
     * @param string $tmpFile
     * @return string|null
     */
    private function moveUploadFile($file,$tmpFile){

        if (null !== $file) {
            if(!$this->id){
                $file->move($this->getTmpUploadRootDir(), $file->getClientOriginalName());
            }else{
                $this->unlinkFile($this->getUploadRootDir().$tmpFile);
                $file->move($this->getUploadRootDir(), $file->getClientOriginalName());
            }
            return $file->getClientOriginalName();
        }
        return null;
    }
    /**
     * Unlink file
     *
     * @param string $filePath
     */
    private function unlinkFile($filePath){
        if(is_file($filePath)){
            @unlink($filePath);
        }
    }

    public function isActive() {

        return $this->startDate <= new \DateTime()
            && $this->endDate > new \DateTime()
            && ($this->clickLimit > $this->clicks || $this->clickLimit == 0)
            && ($this->displayLimit > $this->hits || $this->displayLimit == 0)
            ;
    }
}

