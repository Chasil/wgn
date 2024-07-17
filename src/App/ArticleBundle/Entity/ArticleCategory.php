<?php
/**
 * This file is part of the AppArticeBundle package.
 *
 */
namespace App\ArticleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * Class ArticleCategory
 *
 * @author wojciech przygoda
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\ArticleBundle\Entity\ArticleCategoryRepository")
 */
class ArticleCategory
{
    /**
     * @var integer article category id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @var integer legacy id
     *
     * @ORM\Column(name="legacyId", type="integer", nullable=true)
     */
    private $legacyId;
    /**
     * @var string category name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;
    /**
     * @var string name
     *
     * @ORM\Column(name="meta_title", type="string", length=255,nullable=true)
     */
    private $metaTitle;
    /**
     * @var boolean state
     *
     * @ORM\Column(name="state", type="boolean")
     */
    private $state;
    /**
     * @var boolean is delete
     *
     * @ORM\Column(name="isDelete", type="boolean", nullable=true)
     */
    private $isDelete;
    /**
     * @var boolean
     *
     * @ORM\Column(name="disallowRobots", type="boolean")
     */
    private $disallowRobots;
    /**
     * @var string
     *
     * @ORM\Column(name="followAttribute", type="string", length=255)
     */
    private $followAttribute;
    /**
     * @var boolean is main category
     *
     * @ORM\Column(name="isMain", type="boolean")
     */
    private $isMain;
    /**
     * @var string meta description
     *
     * @ORM\Column(name="metaDesc", type="string", length=255, nullable=true)
     */
    private $metaDesc;

    /**
     * @var string meta keywords
     *
     * @ORM\Column(name="metaKeywords", type="string", length=255, nullable=true)
     */
    private $metaKeywords;
    /**
     * @var Article[] collection of articles
     *
     * @ORM\OneToMany(targetEntity="Article", mappedBy="category")
     */
    protected $articles;


    /**
     * Constructor
     */
    function __construct() {
      $this->articles = new ArrayCollection();
      $this->state = 1;
      $this->isMain = false;
      $this->isDelete = false;
      $this->disallowRobots = false;
    }

    /**
     * Article category to string
     * 
     * @return string
     */
    public function __toString() {
        return $this->name;
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
     * @param string $legacyId legacy id
     * @return OfferCategory
     */
    public function setLegacyId($legacyId) {
        $this->legacyId = $legacyId;
        return $this;
    }

    /**
     * Set name
     *
     * @param string $name category name
     * @return OfferCategory
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
     * Set state
     *
     * @param boolean $state category state
     * @return OfferCategory
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return boolean
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Get articles
     *
     * @return Article[]
     */
    public function getArticles() {
        return $this->articles;
    }
    /**
     * Set Articles
     *
     * @param Article[] $articles
     */
    public function setArticles($articles) {
        $this->articles = $articles;
    }
    /**
     * Get metaDesc
     *
     * @return string
     */
    public function getMetaDesc() {
        return $this->metaDesc;
    }
    /**
     * Get metaDesc
     *
     * @return string
     */
    public function getMetaKeywords() {
        return $this->metaKeywords;
    }
    /**
     * Set metaDesc
     *
     * @param string $metaDesc meta description
     */
    public function setMetaDesc($metaDesc) {
        $this->metaDesc = $metaDesc;
        return $this;
    }
    /**
     * Set metaKeywords
     *
     * @param string $metaKeywords meta keywords
     */
    public function setMetaKeywords($metaKeywords) {
        $this->metaKeywords = $metaKeywords;
        return $this;
    }
    /**
     * Get metaDesc
     *
     * @return string
     */
    public function hasArticles(){
       return (count($this->getArticles())>0) ? true : false;
    }
    /**
     * Get isDelete
     *
     * @return string
     */
    public function getIsDelete() {
        return $this->isDelete;
    }
    /**
     * Set isDelete
     *
     * @param bool $isDelete is delete
     */
    public function setIsDelete($isDelete) {
        $this->isDelete = $isDelete;
        return $this;
    }
    /**
     * Get isMain
     *
     * @return string
     */
    public function getIsMain() {
        return $this->isMain;
    }
    /**
     * Set isMain
     *
     * @param bool $isMain is main
     */
    public function setIsMain($isMain) {
        $this->isMain = $isMain;
        return $this;
    }
    /**
     * Get disallowRobots
     *
     * @return bool
     */
    public function getDisallowRobots() {
        return $this->disallowRobots;
    }
    /**
     * Set disallowRobots
     *
     * @param bool $disallowRobots disalow robots
     */
    public function setDisallowRobots($disallowRobots) {
        $this->disallowRobots = $disallowRobots;
        return $this;
    }
    /**
     * Get disallowRobots
     *
     * @return bool
     */
    public function isDisallowRobots() {
        return $this->disallowRobots;
    }

    /**
     * @return string
     */
    public function getMetaTitle()
    {
        return $this->metaTitle;
    }

    /**
     * @param string $metaTitle
     * @return ArticleCategory
     */
    public function setMetaTitle(string $metaTitle)
    {
        $this->metaTitle = $metaTitle;

        return $this;
    }

    /**
     * @return string
     */
    public function getFollowAttribute()
    {
        return $this->followAttribute;
    }

    /**
     * @param string $followAttribute
     * @return ArticleCategory
     */
    public function setFollowAttribute(string $followAttribute): ArticleCategory
    {
        $this->followAttribute = $followAttribute;

        return $this;
    }






}
