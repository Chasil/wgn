<?php
/**
 * This file is part of the AppPaymentBundle package.
 *
 */
namespace App\PaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class PaymentItem
 *
 * @author wojciech przygoda
 *
 * @ORM\Table(name="payment_item")
 * @ORM\Entity(repositoryClass="App\PaymentBundle\Entity\PaymentItemRepository")
 * @ORM\HasLifecycleCallbacks
 */
class PaymentItem
{
    /**
     * @const item value
     */
    const VALUE = 11.07;
    /**
     * @const promo item value
     */
    const PROMO_VALUE = 19.68;
    /**
     * @const item type publication
     */
    const TYPE_PUBLICATION = 'publication';
    /**
     * @const item type promo
     */
    const TYPE_PROMO    = 'promo';
    /**
     * @var int item id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var float value
     *
     * @ORM\Column(name="value", type="float")
     */
    private $value;
    /**
     * @var Offer offer
     *
     * @ORM\ManyToOne(targetEntity="App\OfferBundle\Entity\Offer", inversedBy="payments")
     * @ORM\JoinColumn(name="offer_id", referencedColumnName="id")
     */
    protected $offer;
    /**
     * @var Payment payment
     *
     * @ORM\ManyToOne(targetEntity="Payment", inversedBy="paymentItems")
     */
    protected $payment;
    /**
     * @var string type
     *
     * @ORM\Column(name="type", type="string", length=200,nullable=true)
     */
    protected $type;

    /**
     * Constructor
     *
     * @param string $type type
     */
    public function __construct($type) {
        $this->setType($type);
        if($type === self::TYPE_PROMO){
            $this->setValue(self::PROMO_VALUE);
        }else {
            $this->setValue(self::VALUE);
        }
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
     * Set value
     *
     * @param float $value value
     *
     * @return PaymentItem
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Get offer
     *
     * @return Offer
     */
    public function getOffer() {
        return $this->offer;
    }
    /**
     * Set offer
     *
     * @param Offer $offer offer
     * @return PaymentItem
     */
    public function setOffer($offer) {
        $this->offer = $offer;
        return $this;
    }
    /**
     * Get payment
     *
     * @return Payment
     */
    public function getPayment() {
        return $this->payment;
    }
    /**
     * Set payment
     *
     * @param Payment $payment payment
     * @return PaymentItem
     */
    public function setPayment($payment) {
        $this->payment = $payment;
        return $this;
    }
    /**
     * Get Value
     *
     * @return flat
     */
     public function getPrice(){
        return self::VALUE;
    }
    /**
     * Get promo value
     *
     * @return flat
     */
    public function getPromoPrice(){
        return self::PROMO_VALUE;
    }
    /**
     * Get type
     *
     * @return string
     */
    public function getType() {
        return $this->type;
    }
    /**
     * Set type
     *
     * @param string $type type
     * @return PaymentItem
     */
    public function setType($type) {
        $this->type = $type;
        return $this;
    }
}

