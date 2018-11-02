<?php

namespace AppBundle\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use AppBundle\Annotation\LoggableTableTitleAnnotation;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Gedmo\Mapping\Annotation as Gedmo;
use AppBundle\Annotation\IsActiveAnnotation;

/**
 * @ORM\Table(name="hotel_photos")
 * @ORM\Entity
 * @IsActiveAnnotation(fieldName="is_active")
 * @ApiResource(
 *     attributes={
 *         "normalization_context"={"groups"={"hotel-photo_GET", "photo_GET"}},
 *         "pagination_client_enabled"=true,
 *         "pagination_client_items_per_page"=true,
 *         "filters"={"api-platform.boolean_filter"}
 *     },
 *     itemOperations={
 *         "hotel_photos_get_subresource"={"method"="GET", "access_control"="is_granted('ROLE_USER')", "normalization_context"={"groups"={"hotel-photo_GET"}}},
 *         "put"={"method"="PUT", "access_control"="is_granted('ROLE_MANAGER')", "denormalization_context"={"groups"={"hotel-photo_PUT"}}},
 *         "delete"={"method"="DELETE", "access_control"="is_granted('ROLE_ADMIN')"}
 *     },
 *     collectionOperations={
 *         "get"={"method"="GET", "access_control"="is_granted('ROLE_USER')"},
 *         "hotel_photos_get_subresource"={"method"="GET", "access_control"="is_granted('ROLE_USER')"},
 *     }
 * )
 *
 * @Gedmo\Loggable(logEntryClass="AppBundle\Entity\LogEntry")
 * @LoggableTableTitleAnnotation(title="Фотографии отелей")
 */
class HotelPhoto
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Groups({
     *     "hotel-photo_GET",
     * })
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Hotel", inversedBy="hotelPhotos")
     * @ORM\JoinColumn(name="hotel_id", referencedColumnName="id", nullable=false)
     *
     * @Gedmo\Versioned
     */
    protected $hotel;

    /**
     * @ORM\ManyToOne(targetEntity="Photo", inversedBy="hotelPhotos")
     * @ORM\JoinColumn(name="photo_id", referencedColumnName="id", nullable=false)
     *
     * @Groups({
     *     "hotel-photo_GET",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $photo;

    /**
     * @ORM\Column(name="alt", type="string", length=255, nullable=true)
     *
     * @Groups({
     *     "hotel-photo_PUT",
     *     "hotel-photo_GET",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $alt;

    /**
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     *
     * @Groups({
     *     "hotel-photo_PUT",
     *     "hotel-photo_GET",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $title;

    /**
     * @ORM\Column(name="area_width", type="integer", length=11, nullable=true)
     *
     * @Groups({"hotel-photo_PUT", "hotel-photo_GET", "hotel-view_GET"})
     *
     * @Gedmo\Versioned
     */
    protected $areaWidth;

    /**
     * @ORM\Column(name="area_height", type="integer", length=11, nullable=true)
     *
     * @Groups({"hotel-photo_PUT", "hotel-photo_GET", "hotel-view_GET"})
     *
     * @Gedmo\Versioned
     */
    protected $areaHeight;

    /**
     * @ORM\Column(name="offset_top", type="integer", length=11, nullable=true)
     *
     * @Groups({"hotel-photo_PUT", "hotel-photo_GET", "hotel-view_GET"})
     *
     * @Gedmo\Versioned
     */
    protected $offsetTop;

    /**
     * @ORM\Column(name="offset_left", type="integer", length=11, nullable=true)
     *
     * @Groups({"hotel-photo_PUT", "hotel-photo_GET", "hotel-view_GET"})
     *
     * @Gedmo\Versioned
     */
    protected $offsetLeft;

    /**
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     *
     * @Groups({
     *     "hotel-photo_PUT",
     *     "hotel-photo_GET",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $description;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     *
     * @Groups({
     *     "hotel-photo_PUT",
     *     "hotel-photo_GET",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $isActive;

    /**
     * @ORM\Column(name="list_order", type="integer", length=11, options={"default" : 0})
     *
     * @Groups({
     *     "hotel-photo_PUT",
     *     "hotel-photo_GET",
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
     * @var string $url
     * @Groups({
     *     "hotel-photo_GET",
     * })
     */
    protected $url;

    /**
     * HotelPhoto constructor.
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
     * @return HotelPhoto
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
     * Set listOrder
     *
     * @param integer $listOrder
     *
     * @return HotelPhoto
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return HotelPhoto
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
     * Set hotel
     *
     * @param \AppBundle\Entity\Hotel $hotel
     *
     * @return HotelPhoto
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
     * Set photo
     *
     * @param \AppBundle\Entity\Photo $photo
     *
     * @return HotelPhoto
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
     * @return HotelPhoto
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
     * @return HotelPhoto
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
     * @return HotelPhoto
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

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }
}
