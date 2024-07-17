<?php
/**
 * This file is part of the AppPaymentBundle package.
 *
 */
namespace App\PaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class InvoiceItem
 *
 * @author wojciech przygoda
 *
 * @ORM\Table(name="invoice_item")
 * @ORM\Entity(repositoryClass="App\PaymentBundle\Entity\InvoiceItemRepository")
 */
class InvoiceItem
{
    /**
     * @var int invice item id
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
     * @var int quantity
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * @var string type
     *
     * @ORM\Column(name="type", type="string", length=20)
     */
    private $type;

    /**
     * @var float net price
     *
     * @ORM\Column(name="netprice", type="float")
     */
    private $netprice;

    /**
     * @var int tax
     *
     * @ORM\Column(name="tax", type="integer")
     */
    private $tax;

    /**
     * @var float gross price
     *
     * @ORM\Column(name="grossPrice", type="float")
     */
    private $grossPrice;
   /**
    * @var Invoice invoice
    *
     * @ORM\ManyToOne(targetEntity="Invoice", inversedBy="items")
     */
    protected $invoice;

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
     * @return InvoiceItem
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
     * Set quantity
     *
     * @param integer $quantity quantity
     *
     * @return InvoiceItem
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set type
     *
     * @param string $type type
     *
     * @return InvoiceItem
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
     * Set netprice
     *
     * @param float $netprice netprice
     *
     * @return InvoiceItem
     */
    public function setNetprice($netprice)
    {
        $this->netprice = $netprice;

        return $this;
    }

    /**
     * Get netprice
     *
     * @return float
     */
    public function getNetprice()
    {
        return $this->netprice;
    }

    /**
     * Set tax
     *
     * @param integer $tax tax
     *
     * @return InvoiceItem
     */
    public function setTax($tax)
    {
        $this->tax = $tax;

        return $this;
    }

    /**
     * Get tax
     *
     * @return int
     */
    public function getTax()
    {
        return $this->tax;
    }

    /**
     * Set grossPrice
     *
     * @param float $grossPrice gross price
     *
     * @return InvoiceItem
     */
    public function setGrossPrice($grossPrice)
    {
        $this->grossPrice = $grossPrice;

        return $this;
    }

    /**
     * Get grossPrice
     *
     * @return float
     */
    public function getGrossPrice()
    {
        return $this->grossPrice;
    }
    /**
     * Get invoice
     *
     * @return Invoice
     */
    public function getInvoice() {
        return $this->invoice;
    }
    /**
     * Set invoice
     * @param Invoice $invoice
     * @return InvoiceItem
     */
    public function setInvoice($invoice) {
        $this->invoice = $invoice;
        return $this;
    }
}

