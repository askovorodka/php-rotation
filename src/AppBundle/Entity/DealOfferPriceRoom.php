<?php

namespace AppBundle\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use AppBundle\Annotation\LoggableTableTitleAnnotation;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Table(name="deal_offer_price_room",uniqueConstraints={
 *      @ORM\UniqueConstraint(name="deal_offer_price_room_unique", columns={"deal_offer_price_id", "room_id"})
 * })
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DealOfferPriceRoomRepository")
 *
 * @UniqueEntity(fields={"dealOfferPrice", "room"}, message="Запись с указанными `dealOfferPrice` и `room` уже существует")
 *
 * @ApiResource(
 *     attributes={
 *         "normalization_context"={"groups"={"deal-offer-price-room_GET"}},
 *     },
 *     itemOperations={
 *         "get"={"method"="GET", "access_control"="is_granted('ROLE_MANAGER')"},
 *         "delete"={"method"="DELETE", "access_control"="is_granted('ROLE_ADMIN')"},
 *     },
 *     collectionOperations={
 *         "get"={"method"="GET", "access_control"="is_granted('ROLE_MANAGER')"},
 *         "post"={"method"="POST", "access_control"="is_granted('ROLE_MANAGER')"},
 *     },
 * )
 *
 * @Gedmo\Loggable(logEntryClass="AppBundle\Entity\LogEntry")
 * @LoggableTableTitleAnnotation(title="Связки цен и номеров")
 */
class DealOfferPriceRoom
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Groups({"deal-offer-price-room_GET", "hotel-view_GET", "hotel.dealOffersPricesRooms"})
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Room", inversedBy="dealOfferRoomPrices")
     * @ORM\JoinColumn(name="room_id", referencedColumnName="id", nullable=false)
     *
     * @Gedmo\Versioned
     *
     * @Groups({"deal-offer-price-room_GET", "hotel.dealOffersPricesRooms"})
     */
    protected $room;

    /**
     * @ORM\ManyToOne(targetEntity="DealOfferPrice", inversedBy="dealOfferRooms")
     * @ORM\JoinColumn(name="deal_offer_price_id", referencedColumnName="id", nullable=false)
     *
     * @Gedmo\Versioned
     *
     * @Groups({"deal-offer-price-room_GET", "hotel-view_GET"})
     */
    protected $dealOfferPrice;

    /**
     * @ORM\Column(name="max_guests", type="smallint", options={"unsigned": true})
     *
     * @Gedmo\Versioned
     *
     * @Groups({"deal-offer-price-room_GET", "hotel-view_GET", "hotel.dealOffersPricesRooms"})
     */
    protected $maxGuests;

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
     * Set maxGuests
     *
     * @param integer $maxGuests
     *
     * @return DealOfferPriceRoom
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
     * Set room
     *
     * @param \AppBundle\Entity\Room $room
     *
     * @return DealOfferPriceRoom
     */
    public function setRoom(\AppBundle\Entity\Room $room)
    {
        $this->room = $room;

        return $this;
    }

    /**
     * Get room
     *
     * @return \AppBundle\Entity\Room
     */
    public function getRoom()
    {
        return $this->room;
    }

    /**
     * Set dealOfferPrice
     *
     * @param \AppBundle\Entity\DealOfferPrice $dealOfferPrice
     *
     * @return DealOfferPriceRoom
     */
    public function setDealOfferPrice(\AppBundle\Entity\DealOfferPrice $dealOfferPrice)
    {
        $this->dealOfferPrice = $dealOfferPrice;

        return $this;
    }

    /**
     * Get dealOfferPrice
     *
     * @return \AppBundle\Entity\DealOfferPrice
     */
    public function getDealOfferPrice()
    {
        return $this->dealOfferPrice;
    }
}
