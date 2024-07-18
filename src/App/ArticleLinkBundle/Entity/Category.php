<?php
/**
 * This file is part of the ArticleLinkBundle package.
 *
 */
namespace App\ArticleLinkBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * Class Category
 *
 * @author wojciech przygoda
 *
 * @ORM\Table(name="article_link_category")
 * @ORM\Entity(repositoryClass="App\ArticleLinkBundle\Entity\CategoryRepository")
 */
class Category
{
    /**
     * @var integer category id
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
     * @var ArticleLink[] collection of links
     *
     * @ORM\OneToMany(targetEntity="ArticleLink", mappedBy="category")
     * @ORM\OrderBy({"ordering" = "DESC"})
     */
    protected $links;

    /**
     *
     * @var array list of changes
     */
    private $changes = array();

    /**
     * Constructor
     */
    function __construct() {
      $this->articles = new ArrayCollection();
    }
    /**
     * Category to string
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
     * @return Category
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }
    /**
     * Get links
     *
     * @return Link[]
     */
    public function getLinks() {
        return $this->links;
    }
    /**
     * Set links
     *
     * @param Link[] $links collection of links
     * @return Category
     */
    public function setLinks($links) {
        $this->links = $links;
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
     * @param int $ordering ordering
     * @return Category
     */
    public function setOrdering($ordering) {
        $this->ordering = $ordering;
        return $this;
    }
    /**
     * Increment ordering
     *
     * @return Category
     */
    public function incrementOrdering(){
        $this->ordering++;
        return $this;
    }
    /**
     * Decrement ordering
     *
     * @return Category
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
     * Get metaDesc
     *
     * @return string
     */
    public function hasLinks(){
       return (count($this->getLinks())>0) ? true : false;
    }

}
