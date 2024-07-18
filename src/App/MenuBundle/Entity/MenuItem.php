<?php
/**
 * This file is part of the AppMenuBundle package.
 *
 */
namespace App\MenuBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * Class MenuItem
 *
 * @author wojciech przygoda
 *
 * @Gedmo\Tree(type="nested")
 * @ORM\Table(name="menu_item")
 * @ORM\Entity(repositoryClass="App\MenuBundle\Entity\MenuItemRepository")
 */
class MenuItem
{
    /**
     * @const type empty
     */
    const TYPE_EMPTY = 'empty';
    /**
     * @const type article
     */
    const TYPE_ARTICLE = 'article';
    /**
     * @const type external
     */
    const TYPE_EXTERNAL = 'external';
    /**
     * @const type separator
     */
    const TYPE_SEPARATOR = 'separator';
    /**
     * @const type category
     */
    const TYPE_CATEGORY = 'category';
    /**
     * @const type route
     */
    const TYPE_ROUTE = 'route';

    /**
     * @var int menu item id
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
     * @var string type
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var string route
     *
     * @ORM\Column(name="route", type="string", length=255)
     */
    private $route;

    /**
     * @var array route parameters
     *
     * @ORM\Column(name="routeParameters", type="array",nullable=true)
     */
    private $routeParameters;

    /**
     * @var bool is publish
     *
     * @ORM\Column(name="isPublish", type="boolean")
     */
    private $isPublish;
    /**
     * @var bool in new window
     *
     * @ORM\Column(name="inNewWindow", type="boolean")
     */
    private $inNewWindow;
    /**
     * @var array parameters
     *
     * @ORM\Column(name="parameters", type="array",nullable=true)
     */
    private $parameters;
    /**
     * @var int tree left site
     *
     * @Gedmo\TreeLeft
     * @ORM\Column(type="integer")
     */
    private $lft;

    /**
     * @var int tree level
     *
     * @Gedmo\TreeLevel
     * @ORM\Column(type="integer")
     */
    private $lvl;

    /**
     *  @var int tree right site
     *
     * @Gedmo\TreeRight
     * @ORM\Column(type="integer")
     */
    private $rgt;

    /**
     * @var MenuItem tree root
     *
     * @Gedmo\TreeRoot
     * @ORM\ManyToOne(targetEntity="MenuItem")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    private $root;

    /**
     *  @var MenuItem tree parent
     *
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="MenuItem", inversedBy="children")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    private $parent;

    /**
     * @var MenuItem[] children
     *
     * @ORM\OneToMany(targetEntity="MenuItem", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    private $children;

    /**
     * @var Menu menu
     *
     * @ORM\ManyToOne(targetEntity="Menu", inversedBy="items")
     */

    protected $menu;

    /**
     * Constructor
     */
    public function __construct() {
        $this->children = new ArrayCollection();
        $this->isPublish = true;
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
     * @param string $name name
     *
     * @return MenuItem
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
     * Set type
     *
     * @param string $type type
     *
     * @return MenuItem
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set route
     *
     * @param string $route route
     *
     * @return MenuItem
     */
    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * Get route
     *
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Set routeParameters
     *
     * @param array $routeParameters route parameters
     *
     * @return MenuItem
     */
    public function setRouteParameters($routeParameters)
    {
        $this->routeParameters = $routeParameters;

        return $this;
    }

    /**
     * Get routeParameters
     *
     * @return array
     */
    public function getRouteParameters()
    {
        return $this->routeParameters;
    }

    /**
     * Set isPublish
     *
     * @param boolean $isPublish is publish
     *
     * @return MenuItem
     */
    public function setIsPublish($isPublish)
    {
        $this->isPublish = $isPublish;

        return $this;
    }

    /**
     * Get isPublish
     *
     * @return bool
     */
    public function getIsPublish()
    {
        return $this->isPublish;
    }
    /**
     * Set inNewWindow
     *
     * @param boolean $inNewWindow in new window
     *
     * @return MenuItem
     */
    public function setInNewWindow($inNewWindow)
    {
        $this->inNewWindow = $inNewWindow;

        return $this;
    }

    /**
     * Get inNewWindow
     *
     * @return bool
     */
    public function getInNewWindow()
    {
        return $this->inNewWindow;
    }
    /**
     * Set ordering
     *
     * @param integer $ordering ordering
     *
     * @return MenuItem
     */
    public function setOrdering($ordering)
    {
        $this->ordering = $ordering;

        return $this;
    }

    /**
     * Get ordering
     *
     * @return int
     */
    public function getOrdering()
    {
        return $this->ordering;
    }

    /**
     * Set params
     *
     * @param array $parameters parameters
     *
     * @return MenuItem
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * Get parameters
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }
    /**
     * Get menu
     *
     * @return Menu
     */
    function getMenu() {
        return $this->menu;
    }
    /**
     * Set menu
     *
     * @param Menu $menu menu
     *
     * @return MenuItem
     */
    function setMenu($menu) {
        $this->menu = $menu;
        return $this;
    }
    /**
     * Get children
     *
     * @return MenuItem[]
     */
    function getChildren() {
        return $this->children;
    }
    /**
     * Set children
     *
     * @param MenuItem[] $children children
     *
     * @return MenuItem
     */
    function setChildren($children) {
        $this->children = $children;
        return $this;
    }
    /**
     * Get parent
     *
     * @return MenuItem
     */
    function getParent() {
        return $this->parent;
    }
    /**
     * Set parent
     *
     * @param Menu $parent menu
     *
     * @return MenuItem
     */
    function setParent($parent) {
        $this->parent = $parent;
        return $this;
    }
    /**
     * Get root
     *
     * @return MenuItem
     */
    public function getRoot() {
        return $this->root;
    }
    /**
     * Set root
     *
     * @param MenuItem $root tree root
     * @return MenuItem
     */
    public function setRoot($root) {
        $this->root = $root;
        return $this;
    }
    /**
     * Get indented name
     * 
     * @return string
     */
    public function getIndentedName() {
        return str_repeat("--", $this->lvl).' '. $this->name;
    }
    /**
     * MenuItem to string
     *
     * @return string
     */
    public function __toString() {
        return $this->name;
    }
}

