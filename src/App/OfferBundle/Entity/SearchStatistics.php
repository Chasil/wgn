<?php

namespace App\OfferBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SearchStatistics
 *
 * @ORM\Table(name="search_statistics")
 * @ORM\Entity(repositoryClass="App\OfferBundle\Entity\SearchStatisticsRepository")
 */
class SearchStatistics
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
     * @var int
     *
     * @ORM\Column(name="displayCounter", type="integer")
     */
    private $displayCounter;

    /**
     * @var Property property
     *
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=true)
     */
    private $category;

    /**
     * @var Property property
     *
     * @ORM\ManyToOne(targetEntity="TransactionType")
     * @ORM\JoinColumn(name="transaction_type_id", referencedColumnName="id", nullable=true)
     */
    private $transaction;

    /**
     * @var int
     *
     * @ORM\Column(name="location_id", type="integer")
     */
    private $locationId;
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
     * Set displayCounter
     *
     * @param integer $displayCounter
     *
     * @return SearchStatistics
     */
    public function setDisplayCounter($displayCounter)
    {
        $this->displayCounter = $displayCounter;

        return $this;
    }

    /**
     * Get displayCounter
     *
     * @return int
     */
    public function getDisplayCounter()
    {
        return $this->displayCounter;
    }
    public function incrementDisplayCounter()
    {
        $this->displayCounter++;
        return $this;
    }

    /**
     * @return Category
     */
    public function getCategory(): Category
    {
        return $this->category;
    }

    /**
     * @param Category $category
     * @return SearchStatistics
     */
    public function setCategory(Category $category): SearchStatistics
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return TransactionType
     */
    public function getTransaction(): TransactionType
    {
        return $this->transaction;
    }

    /**
     * @param TransactionType $transaction
     * @return SearchStatistics
     */
    public function setTransaction(TransactionType $transaction): SearchStatistics
    {
        $this->transaction = $transaction;

        return $this;
    }

    /**
     * @return int
     */
    public function getLocationId(): int
    {
        return $this->locationId;
    }

    /**
     * @param int $locationId
     * @return SearchStatistics
     */
    public function setLocationId(int $locationId): SearchStatistics
    {
        $this->locationId = $locationId;

        return $this;
    }



}

