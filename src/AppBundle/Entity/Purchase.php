<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use AppBundle\Annotation\LoggableTableTitleAnnotation;

/**
 * Purchase
 *
 * @ORM\Table(name="purchase", indexes={
 *     @ORM\Index(name="status", columns={"status"})
 *     })
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PurchaseRepository")
 *
 * @Gedmo\Loggable(logEntryClass="AppBundle\Entity\LogEntry")
 * @LoggableTableTitleAnnotation(title="Покупки")
 */
class Purchase
{
    /** Статусы заказов */
    public const STATUS_NOT_PAID = 0;
    public const STATUS_PROCESSED = 1;
    public const STATUS_PAID = 2;
    public const STATUS_OVERDUE = 3;
    public const STATUS_CANCELED = 4;
    public const STATUS_AUTO_CANCELED = 5;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="DealOffer", inversedBy="purchases")
     * @ORM\JoinColumn(name="deal_offer_id", referencedColumnName="id", nullable=false)
     *
     * @Gedmo\Versioned
     */
    protected $dealOffer;

    /**
     * @ORM\ManyToOne(targetEntity="DealOfferPrice", inversedBy="purchases")
     * @ORM\JoinColumn(name="deal_offer_price_id", referencedColumnName="id", nullable=false)
     *
     * @Gedmo\Versioned
     */
    protected $dealOfferPrice;

    /**
     * @ORM\Column(name="cl_client_id", type="integer", nullable=false)
     *
     * @Gedmo\Versioned
     */
    protected $clClientId;

    /**
     * @ORM\Column(name="purchase_date", type="datetime", nullable=false)
     *
     * @Gedmo\Versioned
     */
    protected $purchaseDate;

    /**
     * @ORM\Column(name="status", type="smallint", nullable=true)
     *
     * @Gedmo\Versioned
     */
    protected $status;

    /**
     * @ORM\Column(name="total_price", type="integer", nullable=false)
     */
    protected $totalPrice;

    /**
     * @var int|null
     *
     * @ORM\Column(name="quantity", type="integer", nullable=true)
     */
    protected $quantity;

    /**
     * @var int|null
     *
     * @ORM\Column(name="quantity_return", type="integer", nullable=true)
     */
    protected $quantityReturn;


    public function setId($id) {
        $this->id = $id;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set clClientId.
     *
     * @param int $clClientId
     *
     * @return Purchase
     */
    public function setClClientId($clClientId)
    {
        $this->clClientId = $clClientId;

        return $this;
    }

    /**
     * Get clClientId.
     *
     * @return int
     */
    public function getClClientId()
    {
        return $this->clClientId;
    }

    /**
     * Set purchaseDate.
     *
     * @param \DateTime $purchaseDate
     *
     * @return Purchase
     */
    public function setPurchaseDate($purchaseDate)
    {
        $this->purchaseDate = $purchaseDate;

        return $this;
    }

    /**
     * Get purchaseDate.
     *
     * @return \DateTime
     */
    public function getPurchaseDate()
    {
        return $this->purchaseDate;
    }

    /**
     * Set status.
     *
     * @param int|null $status
     *
     * @return Purchase
     */
    public function setStatus($status = null)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status.
     *
     * @return int|null
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set totalPrice.
     *
     * @param int $totalPrice
     *
     * @return Purchase
     */
    public function setTotalPrice($totalPrice)
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    /**
     * Get totalPrice.
     *
     * @return int
     */
    public function getTotalPrice()
    {
        return $this->totalPrice;
    }


    /**
     * Set dealOffer.
     *
     * @param \AppBundle\Entity\DealOffer $dealOffer
     *
     * @return Purchase
     */
    public function setDealOffer(\AppBundle\Entity\DealOffer $dealOffer)
    {
        $this->dealOffer = $dealOffer;

        return $this;
    }

    /**
     * Get dealOffer.
     *
     * @return \AppBundle\Entity\DealOffer
     */
    public function getDealOffer()
    {
        return $this->dealOffer;
    }

    /**
     * Set dealOfferPrice.
     *
     * @param \AppBundle\Entity\DealOfferPrice $dealOfferPrice
     *
     * @return Purchase
     */
    public function setDealOfferPrice(\AppBundle\Entity\DealOfferPrice $dealOfferPrice)
    {
        $this->dealOfferPrice = $dealOfferPrice;

        return $this;
    }

    /**
     * Get dealOfferPrice.
     *
     * @return \AppBundle\Entity\DealOfferPrice
     */
    public function getDealOfferPrice()
    {
        return $this->dealOfferPrice;
    }

    /**
     * @return int|null
     */
    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    /**
     * @param int|null $quantity
     *
     * @return Purchase
     */
    public function setQuantity(?int $quantity): Purchase
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getQuantityReturn(): ?int
    {
        return $this->quantityReturn;
    }

    /**
     * @param int|null $quantityReturn
     *
     * @return Purchase
     */
    public function setQuantityReturn(?int $quantityReturn): Purchase
    {
        $this->quantityReturn = $quantityReturn;

        return $this;
    }
}
