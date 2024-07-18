<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Currency
 *
 * @author wojciech przygoda
 *
 * @ORM\Table(name="currency")
 * @ORM\Entity(repositoryClass="App\OfferBundle\Entity\CurrencyRepository")
 */
class Currency
{
    /**
     * @var int currency id
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
     * @var string iso code
     *
     * @ORM\Column(name="isoCode", type="string", length=3)
     */
    private $isoCode;

    /**
     * @var string symbol
     *
     * @ORM\Column(name="symbol", type="string", length=20)
     */
    private $symbol;
    /**
     * @var bool is default
     *
     * @ORM\Column(name="isDefault", type="boolean")
     */
    private $isDefault;
    /**
     * @var float exchange rate
     *
     * @ORM\Column(name="exchangeRate", type="float")
     */
    private $exchangeRate;
    /**
     * @var Offer[] collection of offers
     *
     * @ORM\OneToMany(targetEntity="Offer", mappedBy="currency")
     */
    protected $offers;
    /**
     * @var Offer offer
     *
     * @ORM\OneToMany(targetEntity="Offer", mappedBy="monthPaymentsCurrency")
     */
    protected $orrersMonthPaymentsCurrency;
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
     * @return Currency
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
     * Set isoCode
     *
     * @param string $isoCode isoCode
     *
     * @return Currency
     */
    public function setIsoCode($isoCode)
    {
        $this->isoCode = $isoCode;

        return $this;
    }

    /**
     * Get isoCode
     *
     * @return string
     */
    public function getIsoCode()
    {
        return $this->isoCode;
    }

    /**
     * Set symbol
     *
     * @param string $symbol symbol
     *
     * @return Currency
     */
    public function setSymbol($symbol)
    {
        $this->symbol = $symbol;

        return $this;
    }

    /**
     * Get symbol
     *
     * @return string
     */
    public function getSymbol()
    {
        return $this->symbol;
    }
    /**
     * Get isDefault
     *
     * @return bool
     */
    function getIsDefault() {
        return $this->isDefault;
    }
    /**
     * Set isDefault
     *
     * @param string $isDefault is default
     *
     * @return Currency
     */
    function setIsDefault($isDefault) {
        $this->isDefault = $isDefault;
        return $this;
    }
    /**
     * Get exchangeRate
     *
     * @return float
     */
    function getExchangeRate() {
        return $this->exchangeRate;
    }
    /**
     * Set exchangeRate
     *
     * @param string $exchangeRate exchange rate
     *
     * @return Currency
     */
    function setExchangeRate($exchangeRate) {
        $this->exchangeRate = $exchangeRate;
        return $this;
    }

    /**
     * Currency to string
     * @return string
     */
    public function __toString() {
        return $this->symbol;
    }
}

