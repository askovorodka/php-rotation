<?php

namespace AppBundle\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use AppBundle\Annotation\LoggableTableTitleAnnotation;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use AppBundle\Annotation\IsActiveAnnotation;
use AppBundle\Action\Admin\DealOfferUnlinkAction;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DealOfferRepository")
 * @IsActiveAnnotation(fieldName="is_active")
 * @ApiResource(
 *     itemOperations={
 *         "get"={"method"="GET", "access_control"="is_granted('ROLE_MANAGER')"},
 *         "delete"={"method"="DELETE", "access_control"="is_granted('ROLE_ADMIN')", "controller"=DealOfferUnlinkAction::class, "path"="/deal-offer/{id}/delete"},
 *         "get_hotel_by_deal_offer_mobile"={
 *              "method"="GET",
 *              "path"="/mobile/hotel-by-deal-offer/{id}",
 *              "normalization_context"={
 *                  "groups"={
 *                      "hotel-public-fields",
 *                      "hotel.hotelAmenities",
 *                      "hotel-amenity_GET",
 *                      "amenity_GET",
 *                      "hotel-view_GET",
 *                  }
 *              }
 *          },
 *         "get_hotel_view_by_deal_offer_mobile"={
 *              "method"="GET",
 *              "path"="/mobile/hotel-views-by-deal-offer/{id}",
 *              "normalization_context"={
 *                  "groups"={
 *                      "hotel-public-fields",
 *                      "hotel.category", "hotel-category_GET",
 *                      "hotel.contacts", "contact_GET", "contact-category_GET", "contact-type_GET",
 *                      "hotel.hotelAmenities", "hotel-amenity_GET", "amenity_GET",
 *                      "hotel.rooms", "room_GET",
 *                                  "room.roomAmenities", "room-amenity_GET", "amenity_GET",
 *                                  "room.dealOfferPrice",
 *                                  "room.roomPhotos", "room-photo_GET", "photo_GET",
 *                      "hotel.hotelPhotos", "hotel-photo_GET", "photo_GET",
 *              }},
 *          },
 *
 *     },
 *     collectionOperations={
 *         "get"={"method"="GET", "access_control"="is_granted('ROLE_MANAGER')"},
 *         "connect_hotel"={
 *              "method"="POST",
 *              "access_control"="is_granted('ROLE_MANAGER')",
 *              "route_name"="connect_hotel",
 *              "normalization_context"={"groups"={"connect_hotel_normalization_context"}},
 *              "swagger_context" = {
 *                  "parameters" = {
 *                      {
 *                          "name" = "dealOfferId",
 *                          "in" = "path",
 *                          "required" = "true",
 *                          "type" = "integer"
 *                      },
 *                      {
 *                          "name" = "hotelId",
 *                          "in" = "path",
 *                          "required" = "true",
 *                          "type" = "integer"
 *                      },
 *                  }
 *              }
 *          },
 *         "get_deal_offers_without_hotel"={
 *              "method"="GET",
 *              "pagination_client_enabled"=true,
 *              "pagination_client_items_per_page"=true,
 *              "access_control"="is_granted('ROLE_MANAGER')",
 *              "path"="/deal-offers-without-hotel",
 *          },
 *          "get_active_deal_offers_with_hotel"={
 *              "method"="GET",
 *              "pagination_client_enabled"=true,
 *              "pagination_client_items_per_page"=true,
 *              "access_control"="is_granted('ROLE_MANAGER')",
 *              "path"="/active-deal-offers-with-hotel",
 *              "normalization_context"={
 *                  "groups"={
 *                      "active_deal_offers_with_hotel",
 *                  }
 *              },
 *          },
 *     },
 * )
 *
 * @ApiResource(
 *     itemOperations={
 *         "get"={"method"="GET", "access_control"="is_granted('ROLE_ADMIN')"}
 *     },
 *     collectionOperations={
 *         "get"={"method"="GET", "access_control"="is_granted('ROLE_ADMIN')"},
 *     }
 * )
 *
 * @ApiFilter(SearchFilter::class, properties={"title"="partial"})
 * @ApiFilter(OrderFilter::class, properties={"createdAt"})
 *
 * @Gedmo\Loggable(logEntryClass="AppBundle\Entity\LogEntry")
 * @LoggableTableTitleAnnotation(title="Акции")
 */
class DealOffer
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Groups({
     *     "connect_hotel_normalization_context", "deal-offer-price_GET", "hotel.activeDealOffer","hotel.dealOffers",
     *     "hotel_GET", "active_deal_offers_with_hotel",
     * })
     */
    protected $id;

    /**
     * @ORM\Column(name="restriction", type="text", nullable=true)
     *
     * @Gedmo\Versioned
     */
    protected $restriction;

    /**
     * @ORM\Column(name="title", type="text", nullable=true)
     * @Groups({"hotel.dealOffers", "active_deal_offers_with_hotel"})
     * @Gedmo\Versioned
     */
    protected $title;

    /**
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     * @Groups({"hotel.dealOffers"})
     * @Gedmo\Versioned
     */
    protected $address;

    /**
     * @ORM\Column(name="country", type="string", length=255, nullable=true)
     *
     * @Gedmo\Versioned
     */
    protected $country;

    /**
     * @ORM\Column(name="administrative_area", type="string", length=255, nullable=true)
     *
     * @Gedmo\Versioned
     */
    protected $administrativeArea;

    /**
     * @ORM\Column(name="sub_administrative_area", type="string", length=255, nullable=true)
     *
     * @Gedmo\Versioned
     */
    protected $subAdministrativeArea;

    /**
     * @ORM\Column(name="locality", type="string", length=255, nullable=true)
     *
     * @Gedmo\Versioned
     */
    protected $locality;

    /**
     * @ORM\Column(name="thoroughfare", type="string", length=255, nullable=true)
     *
     * @Gedmo\Versioned
     */
    protected $thoroughfare;

    /**
     * @ORM\Column(name="premise", type="string", length=255, nullable=true)
     *
     * @Gedmo\Versioned
     */
    protected $premise;

    /**
     * @ORM\Column(name="postal_code", type="string", length=255, nullable=true)
     *
     * @Gedmo\Versioned
     */
    protected $postalCode;

    /**
     * @ORM\Column(name="title_link", type="text", nullable=true)
     *
     * @Gedmo\Versioned
     *
     * @Groups({"hotel.activeDealOffer", "hotel.dealOffers", "hotel_GET", "active_deal_offers_with_hotel"})
     */
    protected $titleLink;

    /**
     * @ORM\Column(name="duration_days", type="integer", length=11)
     *
     * @Gedmo\Versioned
     */
    protected $durationDays;

    /**
     * @ORM\Column(name="valid_at", type="datetime")
     * @Groups({"hotel.dealOffers", "hotel_GET", "active_deal_offers_with_hotel"})
     * @Gedmo\Versioned
     */
    protected $validAt;

    /**
     * @ORM\Column(name="start_coupon_at", type="datetime")
     * @Groups({"hotel.dealOffers"})
     * @Gedmo\Versioned
     */
    protected $startCouponAt;

    /**
     * @ORM\Column(name="start_at", type="datetime")
     * @Groups({"hotel.dealOffers", "active_deal_offers_with_hotel"})
     * @Gedmo\Versioned
     */
    protected $startAt;

    /**
     * @ORM\Column(name="end_at", type="datetime")
     * @Groups({"hotel.dealOffers"})
     * @Gedmo\Versioned
     */
    protected $endAt;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     * @Groups({"hotel.dealOffers", "active_deal_offers_with_hotel", "hotel_GET"})
     * @Gedmo\Versioned
     */
    protected $createdAt;

    /**
     * @ORM\OneToMany(targetEntity="DealOfferPrice", mappedBy="dealOffer")
     * @Groups({"hotel.dealOffersPrices", "active_deal_offers_with_hotel"})
     */
    protected $dealOfferPrices;

    /**
     * @ORM\ManyToOne(targetEntity="Hotel", inversedBy="dealOffers")
     * @ORM\JoinColumn(name="hotel_id", referencedColumnName="id")
     *
     * @Gedmo\Versioned
     *
     * @Groups({"connect_hotel_normalization_context", "hotel-public-fields", "active_deal_offers_with_hotel"})
     */
    protected $hotel;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     * @Groups({"hotel.dealOffers", "hotel_GET", "active_deal_offers_with_hotel"})
     * @Gedmo\Versioned
     */
    protected $isActive;

    /**
     * @ORM\OneToMany(targetEntity="DealOfferPartnerManager", mappedBy="dealOffer")
     */
    protected $dealOfferPartnerManagers;

    /**
     * @ORM\Column(name="partner_id", type="integer", length=11, nullable=true)
     * @Groups({"hotel.dealOffers"})
     * @Gedmo\Versioned
     */
    protected $partnerId;

    /**
     * @var array|null
     *
     * @ORM\Column(name="manager_info", type="json", nullable=true)
     */
    protected $managerInfo;

    /**
     * DealOffer constructor.
     */
    function __construct()
    {
        $this->createdAt = new \DateTime('now');
        $this->isActive = true;
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
     * Set restriction
     *
     * @param string $restriction
     *
     * @return DealOffer
     */
    public function setRestriction($restriction)
    {
        $this->restriction = $restriction;

        return $this;
    }

    /**
     * Get restriction
     *
     * @return string
     */
    public function getRestriction()
    {
        return $this->restriction;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return DealOffer
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
     * Set address
     *
     * @param string $address
     *
     * @return DealOffer
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set country
     *
     * @param string $country
     *
     * @return DealOffer
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set administrativeArea
     *
     * @param string $administrativeArea
     *
     * @return DealOffer
     */
    public function setAdministrativeArea($administrativeArea)
    {
        $this->administrativeArea = $administrativeArea;

        return $this;
    }

    /**
     * Get administrativeArea
     *
     * @return string
     */
    public function getAdministrativeArea()
    {
        return $this->administrativeArea;
    }

    /**
     * Set subAdministrativeArea
     *
     * @param string $subAdministrativeArea
     *
     * @return DealOffer
     */
    public function setSubAdministrativeArea($subAdministrativeArea)
    {
        $this->subAdministrativeArea = $subAdministrativeArea;

        return $this;
    }

    /**
     * Get subAdministrativeArea
     *
     * @return string
     */
    public function getSubAdministrativeArea()
    {
        return $this->subAdministrativeArea;
    }

    /**
     * Set locality
     *
     * @param string $locality
     *
     * @return DealOffer
     */
    public function setLocality($locality)
    {
        $this->locality = $locality;

        return $this;
    }

    /**
     * Get locality
     *
     * @return string
     */
    public function getLocality()
    {
        return $this->locality;
    }

    /**
     * Set thoroughfare
     *
     * @param string $thoroughfare
     *
     * @return DealOffer
     */
    public function setThoroughfare($thoroughfare)
    {
        $this->thoroughfare = $thoroughfare;

        return $this;
    }

    /**
     * Get thoroughfare
     *
     * @return string
     */
    public function getThoroughfare()
    {
        return $this->thoroughfare;
    }

    /**
     * Set premise
     *
     * @param string $premise
     *
     * @return DealOffer
     */
    public function setPremise($premise)
    {
        $this->premise = $premise;

        return $this;
    }

    /**
     * Get premise
     *
     * @return string
     */
    public function getPremise()
    {
        return $this->premise;
    }

    /**
     * Set postalCode
     *
     * @param string $postalCode
     *
     * @return DealOffer
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * Get postalCode
     *
     * @return string
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * Set titleLink
     *
     * @param string $titleLink
     *
     * @return DealOffer
     */
    public function setTitleLink($titleLink)
    {
        $this->titleLink = $titleLink;

        return $this;
    }

    /**
     * Get titleLink
     *
     * @return string
     */
    public function getTitleLink()
    {
        return $this->titleLink;
    }

    /**
     * Set durationDays
     *
     * @param integer $durationDays
     *
     * @return DealOffer
     */
    public function setDurationDays($durationDays)
    {
        $this->durationDays = $durationDays;

        return $this;
    }

    /**
     * Get durationDays
     *
     * @return integer
     */
    public function getDurationDays()
    {
        return $this->durationDays;
    }

    /**
     * Set validAt
     *
     * @param \DateTime $validAt
     *
     * @return DealOffer
     */
    public function setValidAt($validAt)
    {
        $this->validAt = $validAt;

        return $this;
    }

    /**
     * Get validAt
     *
     * @return \DateTime
     */
    public function getValidAt()
    {
        return $this->validAt;
    }

    /**
     * Set startCouponAt
     *
     * @param \DateTime $startCouponAt
     *
     * @return DealOffer
     */
    public function setStartCouponAt($startCouponAt)
    {
        $this->startCouponAt = $startCouponAt;

        return $this;
    }

    /**
     * Get startCouponAt
     *
     * @return \DateTime
     */
    public function getStartCouponAt()
    {
        return $this->startCouponAt;
    }

    /**
     * Set startAt
     *
     * @param \DateTime $startAt
     *
     * @return DealOffer
     */
    public function setStartAt($startAt)
    {
        $this->startAt = $startAt;

        return $this;
    }

    /**
     * Get startAt
     *
     * @return \DateTime
     */
    public function getStartAt()
    {
        return $this->startAt;
    }

    /**
     * Set endAt
     *
     * @param \DateTime $endAt
     *
     * @return DealOffer
     */
    public function setEndAt($endAt)
    {
        $this->endAt = $endAt;

        return $this;
    }

    /**
     * Get endAt
     *
     * @return \DateTime
     */
    public function getEndAt()
    {
        return $this->endAt;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return DealOffer
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
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return DealOffer
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Add dealOfferPrice
     *
     * @param \AppBundle\Entity\DealOfferPrice $dealOfferPrice
     *
     * @return DealOffer
     */
    public function addDealOfferPrice(\AppBundle\Entity\DealOfferPrice $dealOfferPrice)
    {
        $this->dealOfferPrices[] = $dealOfferPrice;

        return $this;
    }

    /**
     * Remove dealOfferPrice
     *
     * @param \AppBundle\Entity\DealOfferPrice $dealOfferPrice
     */
    public function removeDealOfferPrice(\AppBundle\Entity\DealOfferPrice $dealOfferPrice)
    {
        $this->dealOfferPrices->removeElement($dealOfferPrice);
    }

    /**
     * Get dealOfferPrices
     *
     * @return \Doctrine\Common\Collections\Collection|DealOfferPrice[]
     */
    public function getDealOfferPrices()
    {
        return $this->dealOfferPrices;
    }

    /**
     * Set hotel
     *
     * @param \AppBundle\Entity\Hotel $hotel
     *
     * @return DealOffer
     */
    public function setHotel(\AppBundle\Entity\Hotel $hotel = null)
    {
        $this->hotel = $hotel;

        return $this;
    }

    /**
     * Get hotel
     *
     * @return \AppBundle\Entity\Hotel
     */
    public function getHotel()
    {
        return $this->hotel;
    }

    /**
     * Get dealOfferPartnerManagers
     *
     * @return \Doctrine\Common\Collections\Collection|DealOfferPartnerManager[]
     */
    public function getDealOfferPartnerManagers()
    {
        return $this->dealOfferPartnerManagers;
    }

    /**
     * @return int
     */
    public function getPartnerId()
    {
        return $this->partnerId;
    }

    /**
     * @param int $partnerId
     * @return DealOffer
     */
    public function setPartnerId($partnerId)
    {
        $this->partnerId = $partnerId;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getManagerInfo(): ?array
    {
        return $this->managerInfo;
    }

    /**
     * @param array|null $managerInfo
     * @return DealOffer
     */
    public function setManagerInfo(?array $managerInfo): DealOffer
    {
        $this->managerInfo = $managerInfo;
        return $this;
    }

}
