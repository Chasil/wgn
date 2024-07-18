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
 * Class CategoryOfferDescriptionImage
 *
 * @author wojciech przygoda
 *
 * @ORM\Table(name="category_offer_description_image")
 * @ORM\Entity(repositoryClass="App\OfferBundle\Entity\CategoryOfferDescriptionImageRepository")
 * @ORM\HasLifecycleCallbacks
 */
class CategoryOfferDescriptionImage
{
    /**
     * @var integer category offer description image id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @var string tmp category offer description id
     *
     * @ORM\Column(name="tmpIdCategoryOfferDescription", type="string", length=255,nullable=true)
     */
    private $tmpIdCategoryOfferDescription;
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
     * @var CategoryOfferDescription category offer description
     * @ORM\ManyToOne(targetEntity="CategoryOfferDescription", inversedBy="images")
     */
    protected $categoryOfferDescription;
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
     * @return CategoryOfferDescriptionImage
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
     * Get tmpIdCategoryOfferDescription
     *
     * @return string
     */
    function getTmpIdCategoryOfferDescription() {
        return $this->tmpIdCategoryOfferDescription;
    }
    /**
     * Set tmpIdCategoryOfferDescription
     *
     * @param string $tmpIdCategoryOfferDescription tmp categoryOfferDescription id
     * @return CategoryOfferDescriptionImage
     */
    function setTmpIdCategoryOfferDescription($tmpIdCategoryOfferDescription) {
        $this->tmpIdCategoryOfferDescription = $tmpIdCategoryOfferDescription;
        return $this;
    }

    /**
     * Set ordering
     *
     * @param integer $ordering ordering
     * @return CategoryOfferDescriptionImage
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
     * @return CategoryOfferDescription
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
     * Get categoryOfferDescription
     *
     * @return CategoryOfferDescription
     */
    public function getCategoryOfferDescription() {
        return $this->categoryOfferDescription;
    }
    /**
     * Set categoryOfferDescription
     *
     * @param CategoryOfferDescription $categoryOfferDescription categoryOfferDescription
     */
    public function setCategoryOfferDescription($categoryOfferDescription) {
        $this->categoryOfferDescription = $categoryOfferDescription;
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
        if(isset($this->tmpIdCategoryOfferDescription)){
            return $this->getTmpUploadRootDir().$this->tmpIdCategoryOfferDescription."/";
        }
        return $this->getTmpUploadRootDir().$this->getCategoryOfferDescription()->getId()."/";
    }
    /**
     * Get tmp upload root dir
     * @return string
     */
    protected function getTmpUploadRootDir() {
        // the absolute directory path where uploaded documents should be saved
        return __DIR__ . '/../../../../web/uploads/categoryOffersDescription/';
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