<?php

namespace AppBundle\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use AppBundle\Annotation\LoggableTableTitleAnnotation;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Gedmo\Mapping\Annotation as Gedmo;
use AppBundle\Annotation\IsActiveAnnotation;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;

/**
 * @ORM\Table(name="room_amenities", uniqueConstraints={
 *     @UniqueConstraint(name="room_amenities_unique", columns={"room_id", "amenity_id"})
 * })
 * @ORM\Entity
 *
 * @ApiResource(
 *     attributes={
 *         "normalization_context"={"groups"={"room-amenity_GET", "amenity_GET"}},
 *         "pagination_client_enabled"=true,
 *         "pagination_client_items_per_page"=true,
 *         "filters"={"api-platform.boolean_filter"}
 *     },
 *     itemOperations={
 *         "get"={"method"="GET", "access_control"="is_granted('ROLE_USER')"},
 *         "put"={"method"="PUT", "access_control"="is_granted('ROLE_MANAGER')", "denormalization_context"={"groups"={"room-amenity_PUT"}}},
 *         "delete"={"method"="DELETE", "access_control"="is_granted('ROLE_ADMIN')"}
 *     },
 *     collectionOperations={
 *         "get"={"method"="GET", "access_control"="is_granted('ROLE_USER')"},
 *         "post"={"method"="POST", "access_control"="is_granted('ROLE_MANAGER')", "denormalization_context"={"groups"={"room-amenity_POST"}}}
 *     }
 * )
 *
 * @ApiFilter(OrderFilter::class, properties={"isActive"}, arguments={"orderParameterName"="order"})
 * @IsActiveAnnotation(fieldName="is_active")
 * @Gedmo\Loggable(logEntryClass="AppBundle\Entity\LogEntry")
 * @LoggableTableTitleAnnotation(title="Привязка удобств к номерам")
 */
class RoomAmenity
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Groups({
     *     "room-amenity_GET",
     * })
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Room", inversedBy="roomAmenities")
     * @ORM\JoinColumn(name="room_id", referencedColumnName="id", nullable=false)
     *
     * @Groups({
     *     "room-amenity_POST",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $room;

    /**
     * @ORM\ManyToOne(targetEntity="Amenity", inversedBy="roomAmenities")
     * @ORM\JoinColumn(name="amenity_id", referencedColumnName="id", nullable=false)
     *
     * @Groups({
     *     "room-amenity_GET",
     *     "room-amenity_POST",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $amenity;

    /**
     * @ORM\Column(name="comment", type="string", length=255, nullable=true)
     *
     * @Groups({
     *     "room-amenity_GET",
     *     "room-amenity_POST",
     *     "room-amenity_PUT",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $comment;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     *
     * @Groups({
     *     "room-amenity_GET",
     *     "room-amenity_POST",
     *     "room-amenity_PUT",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $isActive;

    /**
     * @ORM\Column(name="is_priority", type="boolean", options={"default" : 0})
     *
     * @Groups({
     *     "room-amenity_GET",
     *     "room-amenity_POST",
     *     "room-amenity_PUT",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $isPriority;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

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
     * @return RoomAmenity
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
     * @return RoomAmenity
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
     * @return RoomAmenity
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
     * Set isPriority
     *
     * @param boolean $isPriority
     *
     * @return RoomAmenity
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
     * Set room
     *
     * @param \AppBundle\Entity\Room $room
     *
     * @return RoomAmenity
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
     * Set amenity
     *
     * @param \AppBundle\Entity\Amenity $amenity
     *
     * @return RoomAmenity
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
