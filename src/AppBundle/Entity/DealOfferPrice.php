<?php

namespace AppBundle\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use AppBundle\Annotation\LoggableTableTitleAnnotation;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use AppBundle\Action\Admin\DealOfferPriceRoomDeleteAction;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DealOfferPriceRepository")
 *
 * @ApiResource(
 *     attributes={
 *         "normalization_context"={"groups"={"deal-offer-price_GET"}},
 *         "denormalization_context"={"groups"={"deal-offer-price_SAVE"}},
 *     },
 *     itemOperations={
 *         "get"={"method"="GET", "access_control"="is_granted('ROLE_MANAGER')"},
 *         "put"={"method"="PUT", "access_control"="is_granted('ROLE_MANAGER')"},
 *         "delete"={
 *              "method"="DELETE",
 *              "access_control"="is_granted('ROLE_ADMIN')",
 *              "controller"=DealOfferPriceRoomDeleteAction::class,
 *              "path"="/deal-offer-prices/{id}/delete"
 *          },
 *     },
 *     collectionOperations={
 *         "get"={"method"="GET", "access_control"="is_granted('ROLE_MANAGER')"},
 *         "get_deal_offer_prices_without_rooms"={
 *              "method"="GET",
 *              "pagination_client_enabled"=true,
 *              "pagination_client_items_per_page"=true,
 *              "access_control"="is_granted('ROLE_MANAGER')",
 *              "path"="/deal-offer-prices-without-rooms",
 *          },
 *     },
 * )
 *
 * @ApiResource(
 *     itemOperations={
 *         "get"={"method"="GET", "access_control"="is_granted('ROLE_ADMIN')"},
 *     },
 *     collectionOperations={
 *         "get"={"method"="GET", "access_control"="is_granted('ROLE_ADMIN')"},
 *     }
 * )
 *
 * @ApiFilter(SearchFilter::class, properties={"title"="partial"})
 *
 * @Gedmo\Loggable(logEntryClass="AppBundle\Entity\LogEntry")
 * @LoggableTableTitleAnnotation(title="Цены")
 */
class DealOfferPrice
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Groups({
     *     "deal-offer-price_GET", "deal-offer-price-room_GET", "hotel-view_GET", "hotel.dealOffersPrices", "room.dealOfferPrice",
     *     "active_deal_offers_with_hotel",
     * })
     */
    protected $id;

    /**
     * @ORM\Column(name="title", type="text")
     *
     * @Gedmo\Versioned
     *
     * @Groups({
     *     "deal-offer-price_GET",
     *     "hotel-view_GET",
     *     "room.dealOfferPrice",
     *     "hotel.dealOffersPrices",
     *     "active_deal_offers_with_hotel",
     * })
     */
    protected $title;

    /**
     * @ORM\ManyToOne(targetEntity="DealOffer", inversedBy="dealOfferPrices")
     * @ORM\JoinColumn(name="deal_offer_id", referencedColumnName="id", nullable=false)
     *
     * @Gedmo\Versioned
     *
     * @Groups({"deal-offer-price_GET", "hotel-view_GET"})
     */
    protected $dealOffer;

    /**
     * @ORM\Column(name="original_price", type="float")
     *
     * @Gedmo\Versioned
     *
     * @Groups({
     *     "deal-offer-price_GET",
     *     "hotel-view_GET",
     *     "room.dealOfferPrice",
     *     "hotel.dealOffersPrices",
     *     "active_deal_offers_with_hotel",
     * })
     */
    protected $originalPrice;

    /**
     * @ORM\Column(name="discount", type="integer", length=11)
     *
     * @Gedmo\Versioned
     *
     * @Groups({
     *     "deal-offer-price_GET",
     *     "hotel-view_GET",
     *     "room.dealOfferPrice",
     *     "hotel.dealOffersPrices",
     *     "active_deal_offers_with_hotel",
     * })
     */
    protected $discount;

    /**
     * @var float
     *
     * @Gedmo\Versioned
     *
     * @Groups({
     *     "deal-offer-price_GET",
     *     "hotel-view_GET",
     *     "room.dealOfferPrice",
     *     "hotel.dealOffersPrices",
     *     "active_deal_offers_with_hotel",
     * })
     */
    protected $price;

    /**
     * @ORM\Column(name="valid_date", type="datetime", nullable=true)
     * @Groups({"room.dealOfferPrice", "hotel.dealOffersPrices", "active_deal_offers_with_hotel"})
     * @Gedmo\Versioned
     */
    protected $validDate;

    /**
     * @ORM\Column(name="group_price_valid_date", type="datetime", nullable=true)
     * @Groups({"room.dealOfferPrice", "hotel.dealOffersPrices"})
     * @Gedmo\Versioned
     */
    protected $groupPriceValidDate;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     *
     * @Gedmo\Versioned
     * @Groups({
     *     "room.dealOfferPrice",
     *     "deal-offer-price_GET",
     *     "deal-offer-price_GET",
     *     "hotel.dealOffersPrices",
     * })
     */
    protected $createdAt;

    /**
     * @ORM\OneToMany(targetEntity="DealOfferPriceRoom", mappedBy="dealOfferPrice")
     * @Groups("hotel.dealOffersPricesRooms")
     */
    protected $dealOfferRooms;

    /**
     * @var int
     *
     * @ORM\Column(name="max_coupone", type="integer")
     */
    protected $maxCoupone;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text")
     *
     * @Groups({
     *     "deal-offer-price_GET",
     *     "deal-offer-price_SAVE",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $comment;

    /**
     * @var int|null
     *
     * @ORM\Column(name="purchases_count", type="integer", nullable=true)
     *
     * @Groups({
     *     "deal-offer-price_GET",
     *     "hotel-view_GET",
     *     "room.dealOfferPrice",
     *     "hotel.dealOffersPrices",
     * })
     */
    protected $purchasesCount;

    /**
     * DealOfferPrice constructor.
     */
    function __construct()
    {
        $this->createdAt = new \DateTime('now');
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return DealOfferPrice
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set originalPrice
     *
     * @param float $originalPrice
     *
     * @return DealOfferPrice
     */
    public function setOriginalPrice($originalPrice)
    {
        $this->originalPrice = $originalPrice;

        return $this;
    }

    /**
     * Get originalPrice
     *
     * @return float
     */
    public function getOriginalPrice()
    {
        return $this->originalPrice;
    }

    /**
     * Set discount
     *
     * @param integer $discount
     *
     * @return DealOfferPrice
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * Get discount
     *
     * @return integer
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return DealOfferPrice
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set dealOffer
     *
     * @param mixed $dealOffer
     *
     * @return DealOfferPrice
     */
    public function setDealOffer(\AppBundle\Entity\DealOffer $dealOffer)
    {
        $this->dealOffer = $dealOffer;

        return $this;
    }

    /**
     * Get dealOffer
     *
     * @return \AppBundle\Entity\DealOffer
     */
    public function getDealOffer()
    {
        return $this->dealOffer;
    }

    /**
     * Add dealOfferRoom
     *
     * @param \AppBundle\Entity\DealOfferPriceRoom $dealOfferRoom
     *
     * @return DealOfferPrice
     */
    public function addDealOfferRoom(\AppBundle\Entity\DealOfferPriceRoom $dealOfferRoom)
    {
        $this->dealOfferRooms[] = $dealOfferRoom;

        return $this;
    }

    /**
     * Remove dealOfferRoom
     *
     * @param \AppBundle\Entity\DealOfferPriceRoom $dealOfferRoom
     */
    public function removeDealOfferRoom(\AppBundle\Entity\DealOfferPriceRoom $dealOfferRoom)
    {
        $this->dealOfferRooms->removeElement($dealOfferRoom);
    }

    /**
     * Get dealOfferRooms
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDealOfferRooms()
    {
        return $this->dealOfferRooms;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return ceil($this->originalPrice * round(1 - $this->discount / 100, 4));
    }

    /**
     * @return \DateTime|null
     */
    public function getValidDate()
    {
        return $this->validDate;
    }

    /**
     * @param \DateTime|null $validDate
     * @return DealOfferPrice
     */
    public function setValidDate($validDate)
    {
        $this->validDate = $validDate;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getGroupPriceValidDate()
    {
        return $this->groupPriceValidDate;
    }

    /**
     * @param \DateTime|null $groupPriceValidDate
     * @return DealOfferPrice
     */
    public function setGroupPriceValidDate($groupPriceValidDate)
    {
        $this->groupPriceValidDate = $groupPriceValidDate;

        return $this;
    }

    /**
     * @return int
     */
    public function getMaxCoupone()
    {
        return $this->maxCoupone;
    }

    /**
     * @param int $maxCoupone
     */
    public function setMaxCoupone($maxCoupone)
    {
        $this->maxCoupone = $maxCoupone;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment($comment): void
    {
        $this->comment = $comment;
    }

    /**
     * @return int|null
     */
    public function getPurchasesCount(): ?int
    {
        return $this->purchasesCount;
    }

    /**
     * @param int|null $purchasesCount
     * @return DealOfferPrice
     */
    public function setPurchasesCount(?int $purchasesCount): DealOfferPrice
    {
        $this->purchasesCount = $purchasesCount;
        return $this;
    }
}
