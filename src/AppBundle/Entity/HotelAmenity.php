<?php

namespace AppBundle\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use AppBundle\Annotation\LoggableTableTitleAnnotation;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Component\Serializer\Annotation\Groups;
use Gedmo\Mapping\Annotation as Gedmo;
use AppBundle\Annotation\IsActiveAnnotation;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;

/**
 * @ORM\Table(name="hotel_amenities", uniqueConstraints={
 *     @UniqueConstraint(name="hotel_amenities_unique", columns={"hotel_id", "amenity_id"})
 * })
 * @ORM\Entity
 * @IsActiveAnnotation(fieldName="is_active")
 * @ApiResource(
 *     attributes={
 *         "normalization_context"={"groups"={"hotel-amenity_GET", "amenity_GET", "amenity-category_GET"}},
 *         "pagination_client_enabled"=true,
 *         "pagination_client_items_per_page"=true,
 *         "filters"={"api-platform.boolean_filter"}
 *     },
 *     itemOperations={
 *         "get"={"method"="GET", "access_control"="is_granted('ROLE_USER')"},
 *         "put"={"method"="PUT", "access_control"="is_granted('ROLE_MANAGER')", "denormalization_context"={"groups"={"hotel-amenity_PUT"}}},
 *         "delete"={"method"="DELETE", "access_control"="is_granted('ROLE_ADMIN')"}
 *     },
 *     collectionOperations={
 *         "get"={"method"="GET", "access_control"="is_granted('ROLE_USER')"},
 *         "post"={"method"="POST", "access_control"="is_granted('ROLE_MANAGER')", "denormalization_context"={"groups"={"hotel-amenity_POST"}}},
 *     }
 * )
 *
 * @ApiFilter(OrderFilter::class, properties={"isActive"}, arguments={"orderParameterName"="order"})
 * @Gedmo\Loggable(logEntryClass="AppBundle\Entity\LogEntry")
 * @LoggableTableTitleAnnotation(title="Привязка удобств к отелям")
 */
class HotelAmenity
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Groups({
     *     "hotel-amenity_GET",
     * })
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Hotel", inversedBy="hotelAmenities")
     * @ORM\JoinColumn(name="hotel_id", referencedColumnName="id", nullable=false)
     *
     * @Groups("hotel-amenity_POST")
     *
     * @Gedmo\Versioned
     */
    protected $hotel;

    /**
     * @ORM\ManyToOne(targetEntity="Amenity", inversedBy="hotelAmenities")
     * @ORM\JoinColumn(name="amenity_id", referencedColumnName="id", nullable=false)
     *
     * @Groups({
     *     "hotel-amenity_GET",
     *     "hotel-amenity_POST",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $amenity;

    /**
     * @ORM\Column(name="comment", type="string", length=255, nullable=true)
     *
     * @Groups({
     *     "hotel-amenity_GET",
     *     "hotel-amenity_POST",
     *     "hotel-amenity_PUT",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $comment;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     *
     * @Groups({
     *     "hotel-amenity_GET",
     *     "hotel-amenity_POST",
     *     "hotel-amenity_PUT",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $isActive;

    /**
     * @ORM\Column(name="is_priority", type="boolean", options={"default" : 0})
     *
     * @Groups({
     *     "hotel-amenity_GET",
     *     "hotel-amenity_POST",
     *     "hotel-amenity_PUT",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $isPriority;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * HotelAmenity constructor.
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
     * Set comment
     *
     * @param string $comment
     *
     * @return HotelAmenity
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return HotelAmenity
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
     * @return HotelAmenity
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
     * @return HotelAmenity
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
     * @return HotelAmenity
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
     * Set amenity
     *
     * @param \AppBundle\Entity\Amenity $amenity
     *
     * @return HotelAmenity
     */
    public function setAmenity(\AppBundle\Entity\Amenity $amenity)
    {
        $this->amenity = $amenity;

        return $this;
    }

    /**
     * Get amenity
     *
     * @return \AppBundle\Entity\Amenity
     */
    public function getAmenity()
    {
        return $this->amenity;
    }
}
