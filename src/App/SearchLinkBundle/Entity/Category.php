<?php
/**
 * This file is part of the AppSearchLinkBundle package.
 *
 */
namespace App\SearchLinkBundle\Entity;

use App\OfferBundle\Entity\TransactionType;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use App\OfferBundle\Entity\Category as OfferCategory;
/**
 * Class Category
 *
 * @author wojciech przygoda
 *
 * @ORM\Table(name="link_category")
 * @ORM\Entity(repositoryClass="App\SearchLinkBundle\Entity\CategoryRepository")
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
     * @var string name
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;
    /**
     * @var integer ordering
     *
     * @ORM\Column(name="ordering", type="integer")
     */
    private $ordering;
    /**
     * @var Link[] collection of links
     *
     * @ORM\OneToMany(targetEntity="Link", mappedBy="category")
     * @ORM\OrderBy({"ordering" = "ASC"})
     */
    protected $links;
    /**
     * @var TransactionType
     *
     * @ORM\ManyToOne(targetEntity="App\OfferBundle\Entity\TransactionType", inversedBy="links")
     * @ORM\OrderBy({"ordering" = "DESC"})
     */
    protected $transactionType;
    /**
     * @var OfferCategory
     *
     * @ORM\ManyToOne(targetEntity="App\OfferBundle\Entity\Category", inversedBy="links")
     * @ORM\OrderBy({"ordering" = "DESC"})
     */
    protected $category;
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
     * @return TransactionType
     */
    public function getTransactionType(): TransactionType
    {
        return $this->transactionType;
    }

    /**
     * @param TransactionType $transactionType
     * @return Category
     */
    public function setTransactionType(TransactionType $transactionType)
    {
        $this->transactionType = $transactionType;

        return $this;
    }

    /**
     * @return OfferCategory
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param OfferCategory $category
     * @return Category
     */
    public function setCategory(OfferCategory $category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return Category
     */
    public function setUrl(string $url): Category
    {
        $this->url = $url;

        return $this;
    }

}
