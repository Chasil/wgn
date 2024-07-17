<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Class OfferImage
 *
 * @author wojciech przygoda
 *
 * @ORM\Table(name="offer_image")
 * @ORM\Entity(repositoryClass="App\OfferBundle\Entity\OfferImageRepository")
 * @ORM\HasLifecycleCallbacks
 */
class OfferImage
{
    /**
     * @var integer offer image id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @var string tmp offer id
     *
     * @ORM\Column(name="tmpIdOffer", type="string", length=255,nullable=true)
     */
    private $tmpIdOffer;
    /**
     * @var string name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var integer ordering
     *
     * @ORM\Column(name="ordering", type="integer")
     */
    private $ordering;

   /**
    *
    * @var Offer offer
    * @ORM\ManyToOne(targetEntity="Offer", inversedBy="images")
    */
    protected $offer;
    /**
     * @var \DateTime modification date
     *
     * @ORM\Column(name="modifyDate", type="datetime",nullable=true)
     */
    private $modifyDate;

    /**
     *
     * @var bool auto upload
     */
    private $autoUpload;

    /**
     * Constructor
     */
    public function __construct() {
        $this->autoUpload = true;
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
     * Set name
     *
     * @param string $name name
     * @return OfferImage
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
     * Get tmpIdOffer
     *
     * @return string
     */
    function getTmpIdOffer() {
        return $this->tmpIdOffer;
    }
    /**
     * Set tmpIdOffer
     *
     * @param string $tmpIdOffer tmp offer id
     * @return OfferImage
     */
    function setTmpIdOffer($tmpIdOffer) {
        $this->tmpIdOffer = $tmpIdOffer;
        return $this;
    }

    /**
     * Set ordering
     *
     * @param integer $ordering ordering
     * @return OfferImage
     */
    public function setOrdering($ordering)
    {
        $this->ordering = $ordering;

        return $this;
    }

    /**
     * Get ordering
     *
     * @return integer
     */
    public function getOrdering()
    {
        return $this->ordering;
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
     */
    public function setOffer($offer) {
        $this->offer = $offer;
    }
    /**
     * Get autoUpload
     * @return bool
     */
    public function getAutoUpload() {
        return $this->autoUpload;
    }
    /**
     * Set autoUpload
     *
     * @param bool $autoUpload auto upload
     */
    public function setAutoUpload($autoUpload) {
        $this->autoUpload = $autoUpload;
        return $this;
    }
    /**
     * Get full image path
     * @return null|string
     */
    public function getFullImagePath() {
        return null === $this->name ? null : $this->getUploadRootDir(). $this->name;
    }
    /**
     * Get upload root dir
     * @return string
     */
    protected function getUploadRootDir() {
        // the absolute directory path where uploaded documents should be saved
        if(isset($this->tmpIdOffer)){
            return $this->getTmpUploadRootDir().$this->tmpIdOffer."/";
        }
        return $this->getTmpUploadRootDir().$this->getOffer()->getId()."/";
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
     * Upload image
     *
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function uploadImage() {

        // the file property can be empty if the field is not required
        if (!$this->autoUpload || null === $this->name) {
            return;
        }
        $filename = sha1(uniqid(mt_rand(), true)).'.'.$this->name->guessExtension();
        if(!$this->id){
            $this->name->move($this->getTmpUploadRootDir(), $filename);
        }else{
            $this->name->move($this->getUploadRootDir(), $filename);
        }

        $this->setName($filename);
    }

    /**
     * Move image
     *
     * @ORM\PostPersist()
     */
    public function moveImage()
    {
        if (!$this->autoUpload || null === $this->name) {
            return;
        }
        if(!is_dir($this->getUploadRootDir())){
            mkdir($this->getUploadRootDir());
        }
        copy($this->getTmpUploadRootDir().$this->name, $this->getFullImagePath());
        unlink($this->getTmpUploadRootDir().$this->name);
    }

    /**
     *
     * Remove image
     * @ORM\PreRemove()
     */
    public function removeImage()
    {
        if($this->name != null){
          @unlink($this->getFullImagePath());
        }
    }
}

