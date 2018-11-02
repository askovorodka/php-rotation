<?php

namespace AppBundle\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use AppBundle\Annotation\LoggableTableTitleAnnotation;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="room",indexes={
 *     @ORM\Index(name="is_active", columns={"is_active"})
 * })
 * @ORM\Entity
 *
 * @ApiResource(
 *     attributes={
 *         "normalization_context"={"groups"={"room_GET"}},
 *         "denormalization_context"={"groups"={"room_SAVE"}},
 *         "filters"={"api-platform.boolean_filter"}
 *     },
 *     itemOperations={
 *         "get"={"method"="GET", "access_control"="is_granted('ROLE_USER')"},
 *         "put"={"method"="PUT", "access_control"="is_granted('ROLE_MANAGER')"},
 *         "delete"={"method"="DELETE", "access_control"="is_granted('ROLE_ADMIN')"}
 *     },
 *     collectionOperations={
 *         "get"={"method"="GET", "access_control"="is_granted('ROLE_USER')"},
 *         "post"={"method"="POST", "access_control"="is_granted('ROLE_MANAGER')"},
 *          "api_hotels_rooms_get_subresource" = {
 *              "method"="GET",
 *              "pagination_client_enabled"=true,
 *              "pagination_client_items_per_page"=true,
 *          },
 *          "get_rooms_for_connect_deal_offer_price" = {
 *              "method"="GET",
 *              "pagination_client_enabled"=true,
 *              "pagination_client_items_per_page"=true,
 *              "access_control"="is_granted('ROLE_MANAGER')",
 *              "path"="/rooms-for-connect-deal-offer-price",
 *              "normalization_context"={"groups"={"get_rooms_for_connect_deal_offer_price"}},
 *              "swagger_context" = {
 *                  "parameters" = {
 *                      {
 *                          "name" = "dealOfferPriceId",
 *                          "in" = "query",
 *                          "required" = "true",
 *                          "type" = "integer"
 *                      }
 *                  }
 *              }
 *          },
 *     },
 * )
 *
 * @Gedmo\Loggable(logEntryClass="AppBundle\Entity\LogEntry")
 * @LoggableTableTitleAnnotation(title="Номера")
 */
class Room
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Groups({
     *     "room_GET",
     *     "deal-offer-price-room_GET",
     *     "get_rooms_for_connect_deal_offer_price",
     *     "hotel.dealOffersPricesRooms",
     *     "quota_context",
     *     "admin-quotas-list",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Hotel", inversedBy="rooms")
     * @ORM\JoinColumn(name="hotel_id", referencedColumnName="id", nullable=false)
     *
     * @Groups({
     *     "room_SAVE",
     *     "get_rooms_for_connect_deal_offer_price",
     *     "admin-quotas-list",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $hotel;

    /**
     * @ORM\Column(name="title", type="string", length=255)
     *
     * @Groups({
     *     "room_GET",
     *     "room_SAVE",
     *     "get_rooms_for_connect_deal_offer_price",
     *     "hotel.dealOffersPricesRooms",
     *     "admin-quotas-list",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $title;

    /**
     * @ORM\Column(name="area", type="integer", length=11, nullable=true)
     *
     * @Groups({
     *     "room_GET",
     *     "room_SAVE",
     *     "get_rooms_for_connect_deal_offer_price",
     *     "hotel.dealOffersPricesRooms",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $area;

    /**
     * @ORM\Column(name="description", type="text")
     *
     * @Groups({
     *     "room_GET",
     *     "room_SAVE",
     *     "get_rooms_for_connect_deal_offer_price",
     *     "hotel.dealOffersPricesRooms",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $description;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     *
     * @Groups({
     *     "room_GET",
     *     "room_SAVE",
     *     "get_rooms_for_connect_deal_offer_price",
     *     "hotel.dealOffersPricesRooms",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $isActive;

    /**
     * @ORM\Column(name="max_guests", type="smallint", options={"unsigned": true})
     *
     * @Groups({
     *     "room_GET",
     *     "room_SAVE",
     *     "get_rooms_for_connect_deal_offer_price",
     *
     * })
     *
     * @Gedmo\Versioned
     */
    protected $maxGuests;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\OneToMany(targetEntity="RoomAmenity", mappedBy="room")
     *
     * @ApiSubresource
     *
     * @Groups({
     *     "room.roomAmenities",
     * })
     */
    protected $roomAmenities;

    /**
     * @ORM\OneToMany(targetEntity="RoomPhoto", mappedBy="room")
     *
     * @ApiSubresource
     *
     * @Groups({
     *     "room.roomPhotos",
     * })
     *
     * @ORM\OrderBy({"listOrder" = "ASC"})
     */
    protected $roomPhotos;

    /**
     * @ORM\OneToMany(targetEntity="DealOfferPriceRoom", mappedBy="room")
     * @Groups({
     *     "room.dealOfferRoomPrices",
     * })
     */
    protected $dealOfferRoomPrices;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text")
     *
     * @Groups({
     *     "room_GET",
     *     "room_SAVE",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $comment;

    /**
     * @var array $photosUrls
     * @Groups({"room.roomPhotos"})
     */
    private $photosUrls = [];

    /**
     * @var array
     * @Groups({"room.dealOfferPrice"})
     */
    private $dealOfferPrices = [];

    /**
     * @ORM\OneToMany(targetEntity="Quota", mappedBy="room")
     */
    protected $quotas;

    /**
     * Room constructor.
     */
    function __construct()
    {
        $this->createdAt = new \DateTime('now');
        $this->isActive = true;
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
     * @return Room
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
     * Set area
     *
     * @param integer $area
     *
     * @return Room
     */
    public function setArea($area)
    {
        $this->area = $area;

        return $this;
    }

    /**
     * Get area
     *
     * @return integer
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Room
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

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return Room
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Room
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
     * Set maxGuests
     *
     * @param integer $maxGuests
     *
     * @return Room
     */
    public function setMaxGuests($maxGuests)
    {
        $this->maxGuests = $maxGuests;

        return $this;
    }

    /**
     * Get maxGuests
     *
     * @return integer
     */
    public function getMaxGuests()
    {
        return $this->maxGuests;
    }

    /**
     * Set hotel
     *
     * @param \AppBundle\Entity\Hotel $hotel
     *
     * @return Room
     */
    public function setHotel(\AppBundle\Entity\Hotel $hotel)
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
     * Add roomAmenity
     *
     * @param \AppBundle\Entity\RoomAmenity $roomAmenity
     *
     * @return Room
     */
    public function addRoomAmenity(\AppBundle\Entity\RoomAmenity $roomAmenity)
    {
        $this->roomAmenities[] = $roomAmenity;

        return $this;
    }

    /**
     * Remove roomAmenity
     *
     * @param \AppBundle\Entity\RoomAmenity $roomAmenity
     */
    public function removeRoomAmenity(\AppBundle\Entity\RoomAmenity $roomAmenity)
    {
        $this->roomAmenities->removeElement($roomAmenity);
    }

    /**
     * Get roomAmenities
     *
     * @return RoomAmenity[]|\Doctrine\Common\Collections\Collection
     */
    public function getRoomAmenities()
    {
        return $this->roomAmenities;
    }

    /**
     * @param RoomAmenity[]|ArrayCollection $roomAmenities
     * @return void
     */
    public function setRoomAmenities($roomAmenities): void
    {
        $this->roomAmenities = $roomAmenities;
    }

    /**
     * Add roomPhoto
     *
     * @param \AppBundle\Entity\RoomPhoto $roomPhoto
     *
     * @return Room
     */
    public function addRoomPhoto(\AppBundle\Entity\RoomPhoto $roomPhoto)
    {
        $this->roomPhotos[] = $roomPhoto;

        return $this;
    }

    /**
     * Remove roomPhoto
     *
     * @param \AppBundle\Entity\RoomPhoto $roomPhoto
     */
    public function removeRoomPhoto(\AppBundle\Entity\RoomPhoto $roomPhoto)
    {
        $this->roomPhotos->removeElement($roomPhoto);
    }

    /**
     * Get roomPhotos
     *
     * @return RoomPhoto[]\Doctrine\Common\Collections\Collection
     */
    public function getRoomPhotos()
    {
        return $this->roomPhotos;
    }

    /**
     * Add dealOfferRoomPrice
     *
     * @param \AppBundle\Entity\DealOfferPriceRoom $dealOfferRoomPrice
     *
     * @return Room
     */
    public function addDealOfferRoomPrice(\AppBundle\Entity\DealOfferPriceRoom $dealOfferRoomPrice)
    {
        $this->dealOfferRoomPrices[] = $dealOfferRoomPrice;

        return $this;
    }

    /**
     * Remove dealOfferRoomPrice
     *
     * @param \AppBundle\Entity\DealOfferPriceRoom $dealOfferRoomPrice
     */
    public function removeDealOfferRoomPrice(\AppBundle\Entity\DealOfferPriceRoom $dealOfferRoomPrice)
    {
        $this->dealOfferRoomPrices->removeElement($dealOfferRoomPrice);
    }

    /**
     * Get dealOfferRoomPrices
     *
     * @return DealOfferPriceRoom[]|\Doctrine\Common\Collections\Collection
     */
    public function getDealOfferRoomPrices()
    {
        return $this->dealOfferRoomPrices;
    }

    public function addPhotoUrl(string $photo):void
    {
        $this->photosUrls[] = $photo;
    }

    public function getPhotosUrls(): array
    {
        return $this->photosUrls;
    }

    /**
     * @param DealOfferPrice $dealOfferPrice
     */
    public function addDealOfferPrice(\AppBundle\Entity\DealOfferPrice $dealOfferPrice)
    {
        $this->dealOfferPrices[] = $dealOfferPrice;
    }

    /**
     * @return array
     */
    public function getDealOfferPrices()
    {
        return $this->dealOfferPrices;
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
}
