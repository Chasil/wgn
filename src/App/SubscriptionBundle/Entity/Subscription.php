<?php
/**
 * This file is part of the AppSubscriptionBundle package.
 *
 */
namespace App\SubscriptionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Subscription
 *
 * @author wojciech przygoda
 *
 * @ORM\Table(name="subscription")
 * @ORM\Entity(repositoryClass="App\SubscriptionBundle\Entity\SubscriptionRepository")
 */
class Subscription
{
    /**
     * @const state active
     */
    const STATE_ACTIVE = 'active';
    /**
     * @const state unactive
     */
    const STATE_UNACTIVE = 'unactive';
    /**
     * @var int subscription id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int number of available offers
     *
     * @ORM\Column(name="numberOfAvailable", type="integer")
     */
    private $numberOfAvailable;

    /**
     * @var int number of used offers
     *
     * @ORM\Column(name="numberOfUsed", type="integer")
     */
    private $numberOfUsed;

    /**
     * @var \DateTime create date
     *
     * @ORM\Column(name="createDate", type="datetime")
     */
    private $createDate;

    /**
     * @var \DateTime expire date
     *
     * @ORM\Column(name="expireDate", type="datetime")
     */
    private $expireDate;

    /**
     * @var float net price
     *
     * @ORM\Column(name="netPrice", type="float")
     */
    private $netPrice;

    /**
     * @var int tax
     *
     * @ORM\Column(name="tax", type="integer")
     */
    private $tax;

    /**
     * @var User user
     *
     * @ORM\ManyToOne(targetEntity="App\UserBundle\Entity\User", inversedBy="subscriptions")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id",nullable=true)
     */
    protected $user;

    /**
     * Constructor
     */
    public function __construct() {
        $this->createDate = new \DateTime();
        $this->tax = 23;
        $this->numberOfUsed = 0;
        $this->numberOfAvailable = 0;
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
     * Set numberOfAvailable
     *
     * @param integer $numberOfAvailable number of available offers
     *
     * @return Subscription
     */
    public function setNumberOfAvailable($numberOfAvailable)
    {
        $this->numberOfAvailable = $numberOfAvailable;

        return $this;
    }

    /**
     * Get numberOfAvailable
     *
     * @return int
     */
    public function getNumberOfAvailable()
    {
        return $this->numberOfAvailable;
    }

    /**
     * Set numberOfUsed
     *
     * @param integer $numberOfUsed number of used offers
     *
     * @return Subscription
     */
    public function setNumberOfUsed($numberOfUsed)
    {
        $this->numberOfUsed = $numberOfUsed;

        return $this;
    }

    /**
     * Get numberOfUsed
     *
     * @return int
     */
    public function getNumberOfUsed()
    {
        return $this->numberOfUsed;
    }

    /**
     * Set createDate
     *
     * @param \DateTime $createDate create date
     *
     * @return Subscription
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
     * Set expireDate
     *
     * @param \DateTime $expireDate expire date
     *
     * @return Subscription
     */
    public function setExpireDate($expireDate)
    {
        $this->expireDate = $expireDate;

        return $this;
    }

    /**
     * Get expireDate
     *
     * @return \DateTime
     */
    public function getExpireDate()
    {
        return $this->expireDate;
    }

    /**
     * Set netPrice
     *
     * @param float $netPrice net price
     *
     * @return Subscription
     */
    public function setNetPrice($netPrice)
    {
        $this->netPrice = $netPrice;

        return $this;
    }

    /**
     * Get netPrice
     *
     * @return float
     */
    public function getNetPrice()
    {
        return $this->netPrice;
    }

    /**
     * Set tax
     *
     * @param integer $tax tax
     *
     * @return Subscription
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
     * Set user
     *
     * @param User $user user
     *
     * @return Subscription
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
     * Reduce Available offers
     */
    public function reduceAvailable() {
        $this->numberOfUsed++;
    }
}

