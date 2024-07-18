<?php
/**
 * This file is part of the AppOfficeBundle package.
 *
 */
namespace App\OfficeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Class OfficeImage
 *
 * @author wojciech przygoda
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\OfficeBundle\Entity\OfficeImageRepository")
 * @ORM\HasLifecycleCallbacks
 */
class OfficeImage
{
    /**
     * @var integer image id
     *
     * @ORM\Column(name="id", type="integer")
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
     * @var integer ordering
     *
     * @ORM\Column(name="ordering", type="integer")
     */
    private $ordering;

   /**
    * @var Office office
    *
    * @ORM\ManyToOne(targetEntity="Office", inversedBy="images")
    */
    protected $office;

    /**
     * @var \DateTime create date
     *
     * @ORM\Column(name="createDate", type="datetime",nullable=true)
     */
    private $createDate;
    /**
     * @var \DateTime modification Date
     *
     * @ORM\Column(name="modifyDate", type="datetime",nullable=true)
     */
    private $modifyDate;
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
     * Get createDate
     *
     * @return \DateTime
     */
    public function getCreateDate() {
        return $this->createDate;
    }
    /**
     * Set createDate
     *
     * @param \DateTime $createDate create date
     */
    public function setCreateDate(\DateTime $createDate) {
        $this->createDate = $createDate;
        return $this;
    }
    /**
     * Get modifyDate
     *
     * @return \DateTime
     */
    public function getModifyDate() {
        return $this->modifyDate;
    }
    /**
     * Set modifyDate
     *
     * @param \DateTime $modifyDate modification date
     */
    public function setModifyDate(\DateTime $modifyDate) {
        $this->modifyDate = $modifyDate;
        return $this;
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
     */
    public function setOffice($office) {
        $this->office = $office;
    }

    /**
     * Get image full path
     *
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
        return $this->getTmpUploadRootDir().$this->getOffice()->getId()."/";
    }
    /**
     * Get tmp upload root dir
     * @return string
     */
    protected function getTmpUploadRootDir() {
        // the absolute directory path where uploaded documents should be saved
        return __DIR__ . '/../../../../web/uploads/offices/';
    }

    /**
     * Upload Image
     *
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function uploadImage() {
        // the file property can be empty if the field is not required
        if (null === $this->name) {
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
        if (null === $this->name) {
            return;
        }
        if(!is_dir($this->getUploadRootDir())){
            mkdir($this->getUploadRootDir());
        }
        copy($this->getTmpUploadRootDir().$this->name, $this->getFullImagePath());
        unlink($this->getTmpUploadRootDir().$this->name);
    }

    /**
     * Remove image
     * 
     * @ORM\PreRemove()
     */
    public function removeImage()
    {
        if($this->name != null){
          @unlink($this->getFullImagePath());
        }
    }
}

