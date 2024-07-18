<?php
/**
 * This file is part of the AppArticeBundle package.
 *
 */
namespace App\ArticleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Class ArticleImage
 *
 * @author wojciech przygoda
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\ArticleBundle\Entity\ArticleImageRepository")
 * @ORM\HasLifecycleCallbacks
 */
class ArticleImage
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
     * @var integer legacy id
     *
     * @ORM\Column(name="legacyId", type="integer",nullable=true)
     */
    private $legacyId;
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
     * @var datetime create date
     *
     * @ORM\Column(name="createDate", type="datetime",nullable=true)
     */
    private $createDate;
    /**
     * @var datetime modification date
     *
     * @ORM\Column(name="modifyDate", type="datetime",nullable=true)
     */
    private $modifyDate;
   /**
    * @var Article article
    *
     * @ORM\ManyToOne(targetEntity="Article", inversedBy="images")
     */
    protected $article;
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
     * @param string $name image name
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
     * Get legacyId
     *
     * @return integer
     */
    public function getLegacyId() {
        return $this->legacyId;
    }
     /**
     * Set legacyId
     *
     * @param int $legacyId legacy id
     */
    public function setLegacyId($legacyId) {
        $this->legacyId = $legacyId;
        return $this;
    }
    /**
     * Get article
     *
     * @return Article
     */
    public function getArticle() {
        return $this->article;
    }
    /**
     * Set article
     *
     * @param Article $article article
     */
    public function setArticle($article) {
        $this->article = $article;
        return $this;
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
    public function setCreateDate($createDate) {
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
     * @param \DateTime $modifyDate modification Date
     */
    public function setModifyDate($modifyDate) {
        $this->modifyDate = $modifyDate;
        return $this;
    }
    /**
     * Get full Image path
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
        return $this->getTmpUploadRootDir().$this->getArticle()->getId()."/";
    }
    /**
     * Get tmp upload root dir
     * @return string
     */
    protected function getTmpUploadRootDir() {
        // the absolute directory path where uploaded documents should be saved
        return __DIR__ . '/../../../../web/uploads/articles/';
    }

    /**
     * Upload image
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
     * Move image to article directory
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

