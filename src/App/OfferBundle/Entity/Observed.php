<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Observed
 *
 * @author wojciech przygoda
 *
 * @ORM\Table(name="observed")
 * @ORM\Entity(repositoryClass="App\OfferBundle\Entity\ObservedRepository")
 */
class Observed
{
    /**
     * @var int observed id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string hash
     *
     * @ORM\Column(name="hash", type="string", length=255)
     */
    private $hash;

    /**
     * @var \DateTime create date
     *
     * @ORM\Column(name="createDate", type="datetime")
     */
    private $createDate;

    /**
     * @var User user
     *
     * @ORM\ManyToOne(targetEntity="App\UserBundle\Entity\User", inversedBy="observed")
     */
    protected $user;

    /**
     * @var Offer[] collection of offers
     *
     * @ORM\ManyToOne(targetEntity="Offer", inversedBy="observed")
     */
    private $offer;

    /**
     * Costructor
     */
    public function __construct() {
        $this->createDate = new \DateTime();
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
     * Set hash
     *
     * @param string $hash hash
     *
     * @return Observed
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * Get hash
     *
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Set createDate
     *
     * @param \DateTime $createDate create date
     *
     * @return Observed
     */
    public function setCreateDate($createDate)
    {
        $this->createDate = $createDate;

        return $this;
    }

    /**
     * Get createDate
     *
     * @return \DateTime
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }

    /**
     * Set user
     *
     * @param User $user user
     *
     * @return Observed
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set offer
     *
     * @param Offer $offer offer
     *
     * @return Observed
     */
    public function setOffer($offer)
    {
        $this->offer = $offer;

        return $this;
    }

    /**
     * Get offer
     *
     * @return Offer
     */
    public function getOffer()
    {
        return $this->offer;
    }
}

