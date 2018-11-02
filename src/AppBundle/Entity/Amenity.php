<?php

namespace AppBundle\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Gedmo\Mapping\Annotation as Gedmo;
use AppBundle\Annotation\LoggableTableTitleAnnotation;

/**
 * @ORM\Table(uniqueConstraints={
 *      @ORM\UniqueConstraint(name="amenity_category_id_title", columns={"amenity_category_id", "title"})
 * },indexes={
 *     @ORM\Index(name="is_active", columns={"is_active"})
 * })
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AmenityRepository")
 *
 * @ApiResource(
 *     attributes={
 *          "normalization_context"={"groups"={"amenity_GET", "amenity-category_GET"}},
 *          "access_control"="is_granted('ROLE_USER')",
 *          "pagination_client_enabled"=true,
 *          "pagination_client_items_per_page"=true,
 *     },
 *     collectionOperations={
 *         "get"={"method"="GET", "access_control"="is_granted('ROLE_USER')"},
 *         "post"={"method"="POST", "access_control"="is_granted('ROLE_MANAGER')"}
 *     },
 *     itemOperations={
 *         "get"={"method"="GET", "access_control"="is_granted('ROLE_USER')"},
 *         "put"={"method"="PUT", "access_control"="is_granted('ROLE_MANAGER')"},
 *         "delete"={"method"="DELETE", "access_control"="is_granted('ROLE_ADMIN')"},
 *         "icon_upload"={"route_name"="amenities_icon_upload", "access_control"="is_granted('ROLE_MANAGER')"}
 *     }
 * )
 *
 * @ApiFilter(OrderFilter::class, properties={"isActive", "title"}, arguments={"orderParameterName"="order"})
 *
 * @Gedmo\Loggable(logEntryClass="AppBundle\Entity\LogEntry")
 * @LoggableTableTitleAnnotation(title="Удобства")
 */
class Amenity
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Groups({
     *     "amenity_GET",
     * })
     */
    protected $id;

    /**
     * @param string $title A name property - this description will be avaliable in the API documentation too.
     *
     * @ORM\Column(name="title", type="string", length=255)
     *
     * @Groups({
     *     "amenity_GET",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $title;

    /**
     * @ORM\ManyToOne(targetEntity="AmenityCategory", inversedBy="amenities")
     * @ORM\JoinColumn(name="amenity_category_id", referencedColumnName="id", nullable=false)
     *
     * @Groups({
     *     "amenity_GET",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $amenityCategory;

    /**
     *
     * @Gedmo\Slug(fields={"title", "id"})
     * @param string $sysname need to generate user-friendly font class names.
     *
     * @ORM\Column(name="sysname", type="string", length=255)
     *
     * @Groups({
     *     "amenity_GET",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $sysname;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     *
     * @Groups({
     *     "amenity_GET",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $isActive;

    /**
     * @ORM\Column(name="is_priority", type="boolean", options={"default" : 0})
     *
     * @Groups({
     *     "amenity_GET",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $isPriority;

    /**
     * @ORM\Column(name="icon", type="string", length=255, nullable=true)
     *
     * @Groups({
     *     "amenity_GET",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $icon;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\OneToMany(targetEntity="HotelAmenity", mappedBy="amenity")
     */
    protected $hotelAmenities;

    /**
     * @ORM\OneToMany(targetEntity="RoomAmenity", mappedBy="amenity")
     */
    protected $roomAmenities;

    /**
     * @var string $previewSmall
     * @Groups({"amenity_GET"})
     */
    private $previewSmall;

    /**
     * Amenity constructor.
     */
    function __construct()
    {
        $this->createdAt = new \DateTime('now');
        $this->isActive = true;
        $this->isPriority = false;
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
     * @return Amenity
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
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return Amenity
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
     * Set isPriority
     *
     * @param boolean $isPriority
     *
     * @return Amenity
     */
    public function setIsPriority($isPriority)
    {
        $this->isPriority = $isPriority;

        return $this;
    }

    /**
     * Get isPriority
     *
     * @return boolean
     */
    public function getIsPriority()
    {
        return $this->isPriority;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Amenity
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
     * Set amenityCategory
     *
     * @param \AppBundle\Entity\AmenityCategory $amenityCategory
     *
     * @return Amenity
     */
    public function setAmenityCategory(\AppBundle\Entity\AmenityCategory $amenityCategory)
    {
        $this->amenityCategory = $amenityCategory;

        return $this;
    }

    /**
     * Get amenityCategory
     *
     * @return \AppBundle\Entity\AmenityCategory
     */
    public function getAmenityCategory()
    {
        return $this->amenityCategory;
    }

    /**
     * Add hotelAmenity
     *
     * @param \AppBundle\Entity\HotelAmenity $hotelAmenity
     *
     * @return Amenity
     */
    public function addHotelAmenity(\AppBundle\Entity\HotelAmenity $hotelAmenity)
    {
        $this->hotelAmenities[] = $hotelAmenity;

        return $this;
    }

    /**
     * Remove hotelAmenity
     *
     * @param \AppBundle\Entity\HotelAmenity $hotelAmenity
     */
    public function removeHotelAmenity(\AppBundle\Entity\HotelAmenity $hotelAmenity)
    {
        $this->hotelAmenities->removeElement($hotelAmenity);
    }

    /**
     * Get hotelAmenities
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHotelAmenities()
    {
        return $this->hotelAmenities;
    }

    /**
     * Add roomAmenity
     *
     * @param \AppBundle\Entity\RoomAmenity $roomAmenity
     *
     * @return Amenity
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
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRoomAmenities()
    {
        return $this->roomAmenities;
    }

    /**
     * @return string | null
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param string $icon
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
    }

    /**
     * @return mixed
     */
    public function getSysname()
    {
        return $this->sysname;
    }

    /**
     * @param mixed $sysname
     */
    public function setSysname($sysname)
    {
        $this->sysname = $sysname;
    }

    /**
     * @param string $previewSmall
     */
    public function setPreviewsSmall(string $previewSmall): void
    {
        $this->previewSmall = $previewSmall;
    }

    /**
     * @return string
     */
    public function getPreviewSmall(): ?string
    {
        return $this->previewSmall;
    }
}
