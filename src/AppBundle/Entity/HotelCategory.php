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
 * @ORM\Entity(repositoryClass="AppBundle\Repository\HotelCategoryRepository")
 *
 * @ApiResource(
 *     attributes={
 *          "normalization_context"={"groups"={"hotel-category_GET"}},
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
 * @LoggableTableTitleAnnotation(title="Категории отелей")
 */
class HotelCategory
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Groups({
     *     "hotel-category_GET",
     * })
     */
    protected $id;

    /**
     * @ORM\Column(name="title", type="string", length=255, unique=true)
     *
     * @Groups({
     *     "hotel-category_GET",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $title;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     *
     * @Groups({
     *     "hotel-category_GET",
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
     * @ORM\OneToMany(targetEntity="Hotel", mappedBy="hotelCategory")
     */
    protected $hotels;

    /**
     * HotelCategory constructor.
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
     * @return HotelCategory
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
     * @return HotelCategory
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
     * @return HotelCategory
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
     * Add hotel
     *
     * @param \AppBundle\Entity\Hotel $hotel
     *
     * @return HotelCategory
     */
    public function addHotel(\AppBundle\Entity\Hotel $hotel)
    {
        $this->hotels[] = $hotel;

        return $this;
    }

    /**
     * Remove hotel
     *
     * @param \AppBundle\Entity\Hotel $hotel
     */
    public function removeHotel(\AppBundle\Entity\Hotel $hotel)
    {
        $this->hotels->removeElement($hotel);
    }

    /**
     * Get amenities
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHotels()
    {
        return $this->hotels;
    }
}
