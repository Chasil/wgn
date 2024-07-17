<?php
/**
 * This file is part of the AppArticleLinkBundle package.
 *
 */
namespace App\ArticleLinkBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * Class Link
 *
 * @author wojciech przygoda
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\ArticleLinkBundle\Entity\ArticleLinkRepository")
 * @ORM\HasLifecycleCallbacks
 */
class ArticleLink
{
    /**
     * @var integer link id
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
     * @var string url
     *
     * @ORM\Column(name="url", type="string", length=255,nullable=true)
     */
    private $url;
    /**
     * @var integer ordering
     *
     * @ORM\Column(name="ordering", type="integer")
     */
    private $ordering;
    /**
     * @var Category category
     *
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="links")
     */

    protected $category;
    /**
     * @var boolean publication state
     *
     * @ORM\Column(name="isPublish", type="boolean")
     */
    private $isPublish;

    /**
     *
     * @var array list of changes
     */
    private $changes = array();

    /**
     * Constructor
     */
    function __construct() {
        $this->isPublish = true;
    }
    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }
    /**
     * Get name
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }
    /**
     * Set name
     *
     * @param string $name name
     * @return Link
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }
    /**
     * Get url
     *
     * @return string
     */
    public function getUrl() {
        return $this->url;
    }
    /**
     * Set url
     *
     * @param string $url url
     * @return Link
     */
    public function setUrl($url) {
        $this->url = $url;
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
     * Set ordering
     *
     * @param string $ordering ordering
     * @return Link
     */
    public function setOrdering($ordering) {
        $this->ordering = $ordering;
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
     * @return Link
     */
    public function setCategory($category) {
        $this->category = $category;
        return $this;
    }
    /**
     * Get isPublish
     *
     * @return boolean
     */
    public function getIsPublish() {
        return $this->isPublish;
    }
    /**
     * Set isPublish
     *
     * @param boolean $isPublish publication state
     * @return Link
     */
    public function setIsPublish($isPublish) {
        $this->isPublish = $isPublish;
        return $this;
    }
    /**
     * Increment ordering
     *
     * @return Link
     */
    public function incrementOrdering(){
        $this->ordering++;
        return $this;
    }
    /**
     * Decrement ordering
     *
     * @return Link
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
}
