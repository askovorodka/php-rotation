<?php

namespace AppBundle\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use AppBundle\Annotation\LoggableTableTitleAnnotation;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(indexes={
 *     @ORM\Index(name="is_active", columns={"is_active"})
 * })
 * @ORM\Entity
 *
 * @ApiResource(
 *     attributes={
 *          "normalization_context"={"groups"={"amenity-category_GET"}},
 *          "access_control"="is_granted('ROLE_USER')",
 *          "pagination_client_enabled"=true,
 *          "pagination_client_items_per_page"=true,
 *     },
 *     collectionOperations={
 *         "get"={"method"="GET", "access_control"="is_granted('ROLE_USER')"},
 *         "post"={"method"="POST", "access_control"="is_granted('ROLE_ADMIN')"}
 *     },
 *     itemOperations={
 *         "get"={"method"="GET", "access_control"="is_granted('ROLE_USER')"},
 *         "put"={"method"="PUT", "access_control"="is_granted('ROLE_ADMIN')"},
 *         "delete"={"method"="DELETE", "access_control"="is_granted('ROLE_ADMIN')"}
 *     }
 * )
 *
 * @UniqueEntity(fields="title", message="Sorry, this title is already in use.")
 *
 * @Gedmo\Loggable(logEntryClass="AppBundle\Entity\LogEntry")
 * @LoggableTableTitleAnnotation(title="Категории удобств")
 */
class AmenityCategory
{
    /** Системное имя категории удобств отелей */
    const HOTEL_AMENITIES_CATEGORY_SYSTEM_NAME = '11';
    /** Системное имя категории удобств номеров */
    const ROOM_AMENITIES_CATEGORY_SYSTEM_NAME = '12';

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({
     *     "amenity-category_GET",
     * })
     */
    protected $id;

    /**
     * @ORM\Column(name="title", type="string", length=255, unique=true)
     *
     * @Groups({
     *     "amenity-category_GET",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $title;

    /**
     * @ORM\Column(name="system_name", type="string", length=255, unique=true)
     *
     * @Groups({
     *     "contact-type_GET",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $systemName;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     *
     * @Groups({
     *     "amenity-category_GET",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $isActive;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\OneToMany(targetEntity="Amenity", mappedBy="amenityCategory")
     */
    protected $amenities;

    /**
     * AmenityCategory constructor.
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
     * @return AmenityCategory
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
     * @return AmenityCategory
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
     * @return AmenityCategory
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
     * Add amenity
     *
     * @param \AppBundle\Entity\Amenity $amenity
     *
     * @return AmenityCategory
     */
    public function addAmenity(\AppBundle\Entity\Amenity $amenity)
    {
        $this->amenities[] = $amenity;

        return $this;
    }

    /**
     * Remove amenity
     *
     * @param \AppBundle\Entity\Amenity $amenity
     */
    public function removeAmenity(\AppBundle\Entity\Amenity $amenity)
    {
        $this->amenities->removeElement($amenity);
    }

    /**
     * Get amenities
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAmenities()
    {
        return $this->amenities;
    }

    /**
     * @return string
     */
    public function getSystemName(): string
    {
        return $this->systemName;
    }
}
