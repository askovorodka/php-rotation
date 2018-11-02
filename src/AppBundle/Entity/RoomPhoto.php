<?php

namespace AppBundle\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use AppBundle\Annotation\LoggableTableTitleAnnotation;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Gedmo\Mapping\Annotation as Gedmo;
use AppBundle\Annotation\IsActiveAnnotation;

/**
 * @ORM\Table(name="room_photos")
 * @ORM\Entity
 *
 * @ApiResource(
 *     attributes={
 *         "normalization_context"={"groups"={"room-photo_GET", "photo_GET"}},
 *         "pagination_client_enabled"=true,
 *         "pagination_client_items_per_page"=true,
 *         "filters"={"api-platform.boolean_filter"}
 *     },
 *     itemOperations={
 *         "room_photos_get_subresource"={"method"="GET", "access_control"="is_granted('ROLE_USER')"},
 *         "put"={"method"="PUT", "access_control"="is_granted('ROLE_MANAGER')", "denormalization_context"={"groups"={"room-photo_PUT"}}},
 *         "delete"={"method"="DELETE", "access_control"="is_granted('ROLE_ADMIN')"}
 *     },
 *     collectionOperations={
 *         "get"={"method"="GET", "access_control"="is_granted('ROLE_USER')"},
 *         "room_photos_get_subresource"={"method"="GET", "access_control"="is_granted('ROLE_USER')"}
 *     }
 * )
 *
 * @Gedmo\Loggable(logEntryClass="AppBundle\Entity\LogEntry")
 * @LoggableTableTitleAnnotation(title="Фотогрфии номеров")
 * @IsActiveAnnotation(fieldName="is_active")
 */
class RoomPhoto
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Groups({
     *     "room-photo_GET",
     * })
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Room", inversedBy="roomPhotos")
     * @ORM\JoinColumn(name="room_id", referencedColumnName="id", nullable=false)
     *
     * @Gedmo\Versioned
     */
    protected $room;

    /**
     * @ORM\ManyToOne(targetEntity="Photo", inversedBy="roomPhotos")
     * @ORM\JoinColumn(name="photo_id", referencedColumnName="id", nullable=false)
     *
     * @Groups({
     *     "room-photo_GET",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $photo;

    /**
     * @ORM\Column(name="alt", type="string", length=255, nullable=true)
     *
     * @Groups({
     *     "room-photo_PUT",
     *     "room-photo_GET",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $alt;

    /**
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     *
     * @Groups({
     *     "room-photo_PUT",
     *     "room-photo_GET"
     * })
     *
     * @Gedmo\Versioned
     */
    protected $title;

    /**
     * @ORM\Column(name="area_width", type="integer", length=11, nullable=true)
     *
     * @Groups({"room-photo_PUT", "room-photo_GET", "hotel-view_GET"})
     *
     * @Gedmo\Versioned
     */
    protected $areaWidth;

    /**
     * @ORM\Column(name="area_height", type="integer", length=11, nullable=true)
     *
     * @Groups({"room-photo_PUT", "room-photo_GET", "hotel-view_GET"})
     *
     * @Gedmo\Versioned
     */
    protected $areaHeight;

    /**
     * @ORM\Column(name="offset_top", type="integer", length=11, nullable=true)
     *
     * @Groups({"room-photo_PUT", "room-photo_GET", "hotel-view_GET"})
     *
     * @Gedmo\Versioned
     */
    protected $offsetTop;

    /**
     * @ORM\Column(name="offset_left", type="integer", length=11, nullable=true)
     *
     * @Groups({"room-photo_PUT", "room-photo_GET", "hotel-view_GET"})
     *
     * @Gedmo\Versioned
     */
    protected $offsetLeft;

    /**
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     *
     * @Groups({
     *     "room-photo_PUT",
     *     "room-photo_GET",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $description;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     *
     * @Groups({
     *     "room-photo_PUT",
     *     "room-photo_GET",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $isActive;

    /**
     * @ORM\Column(name="list_order", type="integer", length=11, options={"default" : 0})
     *
     * @Groups({
     *     "room-photo_PUT",
     *     "room-photo_GET",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $listOrder;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * RoomPhoto constructor.
     */
    function __construct()
    {
        $this->createdAt = new \DateTime('now');
        $this->isActive = true;
        $this->listOrder = 0;
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
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return RoomPhoto
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
     * @return RoomPhoto
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
     * Set listOrder
     *
     * @param integer $listOrder
     *
     * @return RoomPhoto
     */
    public function setListOrder($listOrder)
    {
        $this->listOrder = $listOrder;

        return $this;
    }

    /**
     * Get listOrder
     *
     * @return integer
     */
    public function getListOrder()
    {
        return $this->listOrder;
    }

    /**
     * Set room
     *
     * @param \AppBundle\Entity\Room $room
     *
     * @return RoomPhoto
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
     * Set photo
     *
     * @param \AppBundle\Entity\Photo $photo
     *
     * @return RoomPhoto
     */
    public function setPhoto(\AppBundle\Entity\Photo $photo)
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * Get photo
     *
     * @return \AppBundle\Entity\Photo
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * Set alt.
     *
     * @param string|null $alt
     *
     * @return RoomPhoto
     */
    public function setAlt($alt = null)
    {
        $this->alt = $alt;

        return $this;
    }

    /**
     * Get alt.
     *
     * @return string|null
     */
    public function getAlt()
    {
        return $this->alt;
    }

    /**
     * Set title.
     *
     * @param string|null $title
     *
     * @return RoomPhoto
     */
    public function setTitle($title = null)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string|null
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description.
     *
     * @param string|null $description
     *
     * @return RoomPhoto
     */
    public function setDescription($description = null)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return mixed
     */
    public function getAreaWidth()
    {
        return $this->areaWidth;
    }

    /**
     * @param mixed $areaWidth
     */
    public function setAreaWidth($areaWidth)
    {
        $this->areaWidth = $areaWidth;
    }

    /**
     * @return mixed
     */
    public function getAreaHeight()
    {
        return $this->areaHeight;
    }

    /**
     * @param mixed $areaHeight
     */
    public function setAreaHeight($areaHeight)
    {
        $this->areaHeight = $areaHeight;
    }

    /**
     * @return mixed
     */
    public function getOffsetTop()
    {
        return $this->offsetTop;
    }

    /**
     * @param mixed $offsetTop
     */
    public function setOffsetTop($offsetTop)
    {
        $this->offsetTop = $offsetTop;
    }

    /**
     * @return mixed
     */
    public function getOffsetLeft()
    {
        return $this->offsetLeft;
    }

    /**
     * @param mixed $offsetLeft
     */
    public function setOffsetLeft($offsetLeft)
    {
        $this->offsetLeft = $offsetLeft;
    }
}
