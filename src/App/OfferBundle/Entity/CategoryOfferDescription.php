<?php

namespace App\OfferBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;

/**
 * CategoryOfferDescription
 *
 * @ORM\Table(name="category_offer_description")
 * @ORM\Entity(repositoryClass="App\OfferBundle\Entity\CategoryOfferDescriptionRepository")
 */
class CategoryOfferDescription
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
     * @ORM\Column(name="city", type="string", length=255,nullable=true)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;
    /**
     * @var TransactionType transaction type
     * @ORM\ManyToOne(targetEntity="TransactionType", inversedBy="descriptions")
     */
    protected $transactionType;
    /**
     * @var Category category
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="descriptions")
     */
    protected $category;
    /**
     *
     * @var CategoryOfferDescriptionImage category offer description image
     *
     * @ORM\OneToMany(targetEntity="CategoryOfferDescriptionImage", mappedBy="categoryOfferDescription",cascade={"persist", "remove"})
     * @ORM\OrderBy({"ordering" = "ASC"})
     */
    protected $images;
    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return CategoryOfferDescription
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

    /**
     * Set description
     *
     * @param string $description
     *
     * @return CategoryOfferDescription
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
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
     * Get images
     *
     * @return OfferImage[]
     */
    function getImages() {
        return $this->images;
    }

    /**
     * Set images
     *
     * @param OfferImage[] $images collection of images
     * @return CategoryOfferDescription
     */
    function setImages($images) {
        $this->images = $images;
        return $this;
    }

    /**
     * Get first image
     *
     * @return CategoryOfferDescriptionImage
     */
    public function getFirstImage(){
        $criteria = Criteria::create()
            ->orderBy(array("ordering" => Criteria::ASC))
            ->setMaxResults(1)
        ;
        $images = $this->getImages()->matching($criteria);
        return $images[0];
    }

    /**
     * Add image
     *
     * @param \App\OfferBundle\Entity\CategoryOfferDescriptionImage $image
     *
     * @return CategoryOfferDescription
     */
    public function addImage(\App\OfferBundle\Entity\CategoryOfferDescriptionImage $image)
    {
        $this->images[] = $image;

        return $this;
    }

    /**
     * Remove image
     *
     * @param \App\OfferBundle\Entity\CategoryOfferDescriptionImage $image
     */
    public function removeImage(\App\OfferBundle\Entity\CategoryOfferDescriptionImage $image)
    {
        $this->images->removeElement($image);
    }
}