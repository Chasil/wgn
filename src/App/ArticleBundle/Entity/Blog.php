<?php

namespace App\ArticleBundle\Entity;

use App\AppBundle\Component\SubdomainHelper;
use App\OfferBundle\Entity\Category;
use App\OfferBundle\Entity\TransactionType;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * CategoryOfferDescription
 *
 * @ORM\Table(name="blog")
 * @ORM\Entity(repositoryClass="BlogRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Blog
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255,nullable=true)
     */
    private $name;
    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255,nullable=true)
     */
    private $city;
    /**
     * @var string
     *
     * @ORM\Column(name="subdomain", type="string", length=255)
     */
    private $subdomain;
    /**
     * @var TransactionType transaction type
     * @ORM\ManyToOne(targetEntity="App\OfferBundle\Entity\TransactionType", inversedBy="descriptions")
     */
    protected $transactionType;
    /**
     * @var Category category
     * @ORM\ManyToOne(targetEntity="App\OfferBundle\Entity\Category", inversedBy="descriptions")
     */
    protected $category;
    /**
     * @var Article[] collection of articles
     *
     * @ORM\OneToMany(targetEntity="Article", mappedBy="blog")
     */
    protected $articles;

    /**
     * Blog constructor.
     */
    public function __construct()
    {
        $this->articles = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    
    /**
     * Set city
     *
     * @param string $city
     *
     * @return Blog
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }


    public function getTransactionType() {
        return $this->transactionType;
    }

    public function setTransactionType(TransactionType $transactionType) {
        $this->transactionType = $transactionType;
        return $this;
    }

    public function getCategory() {
        return $this->category;
    }

    public function setCategory(Category $category) {
        $this->category = $category;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Blog
     */
    public function setName(string $name): Blog
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Article[]
     */
    public function getArticles()
    {
        return $this->articles;
    }

    /**
     * @param Article[] $articles
     * @return Blog
     */
    public function setArticles(array $articles)
    {
        $this->articles = $articles;

        return $this;
    }

    public function hasArticles()
    {
        return !$this->articles->isEmpty();
    }

    /**
     * @return string
     */
    public function getSubdomain()
    {
        return $this->subdomain;
    }

    /**
     * @param string $subdomain
     * @return Blog
     */
    public function setSubdomain(string $subdomain): Blog
    {
        $this->subdomain = $subdomain;

        return $this;
    }

    public function isWithoutLocation()
    {
        $params = SubdomainHelper::getParamsFromSubdomain($this->getSubdomain());

        return (count($params) == 2)? true : false;
    }

    public function getTransactionName()
    {
        $params = SubdomainHelper::getParamsFromSubdomain($this->getSubdomain());

        return $params[0];
    }
    public function getTypeName()
    {
        $params = SubdomainHelper::getParamsFromSubdomain($this->getSubdomain());

        return $params[1];
    }

}

