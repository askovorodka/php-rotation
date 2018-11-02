<?php

namespace AppBundle\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use AppBundle\Annotation\LoggableTableTitleAnnotation;
use Doctrine\ORM\Mapping as ORM;
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
 *         "normalization_context"={"groups"={"photo_GET"}}
 *     },
 *     collectionOperations={
 *         "get"={"method"="GET", "access_control"="is_granted('ROLE_USER')"},
 *         "photo_upload"={"route_name"="multiple_photos_upload", "access_control"="is_granted('ROLE_MANAGER')"}
 *     },
 *     itemOperations={
 *         "get"={"method"="GET", "access_control"="is_granted('ROLE_USER')"},
 *         "delete"={"method"="DELETE", "access_control"="is_granted('ROLE_ADMIN')"}
 *     }
 * )
 *
 * @Gedmo\Loggable(logEntryClass="AppBundle\Entity\LogEntry")
 * @LoggableTableTitleAnnotation(title="Фотографии")
 */
class Photo
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Groups({
     *     "photo_GET",
     * })
     */
    protected $id;

    /**
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     *
     * @Groups({
     *     "photo_GET",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $title;

    /**
     * @ORM\Column(name="width", type="integer", length=11, nullable=true)
     *
     * @Groups({"photo_GET", "hotel-photo_GET", "room-photo_GET", "preset-photo_GET", "hotel-view_GET"})
     *
     * @Gedmo\Versioned
     */
    protected $width;

    /**
     * @ORM\Column(name="height", type="integer", length=11, nullable=true)
     *
     * @Groups({"photo_GET", "hotel-photo_GET", "room-photo_GET", "preset-photo_GET", "hotel-view_GET"})
     *
     * @Gedmo\Versioned
     */
    protected $height;

    /**
     * @ORM\Column(name="photo", type="string", length=255)
     *
     * @Groups({
     *     "photo_GET",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $photo;

    /**
     * @ORM\ManyToOne(targetEntity="PhotoCategory", inversedBy="photos")
     * @ORM\JoinColumn(name="photo_category_id", referencedColumnName="id", nullable=false)
     *
     * @Gedmo\Versioned
     */
    protected $photoCategory;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     *
     * @Groups({"photo_GET"})
     *
     * @Gedmo\Versioned
     */
    protected $isActive;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\OneToMany(targetEntity="HotelPhoto", mappedBy="photo")
     */
    protected $hotelPhotos;

    /**
     * @ORM\OneToMany(targetEntity="RoomPhoto", mappedBy="photo")
     */
    protected $roomPhotos;

    /**
     * @ORM\OneToOne(targetEntity="PresetPhoto", mappedBy="photo")
     */
    protected $presetPhoto;

    /**
     * Photo constructor.
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
     * @return Photo
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
     * Set photo
     *
     * @param string $photo
     *
     * @return Photo
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * Get photo
     *
     * @return string
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return Photo
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
     * @return Photo
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
     * Set photoCategory
     *
     * @param \AppBundle\Entity\PhotoCategory $photoCategory
     *
     * @return Photo
     */
    public function setPhotoCategory(\AppBundle\Entity\PhotoCategory $photoCategory)
    {
        $this->photoCategory = $photoCategory;

        return $this;
    }

    /**
     * Get photoCategory
     *
     * @return \AppBundle\Entity\PhotoCategory
     */
    public function getPhotoCategory()
    {
        return $this->photoCategory;
    }

    /**
     * Add hotelPhoto
     *
     * @param \AppBundle\Entity\HotelPhoto $hotelPhoto
     *
     * @return Photo
     */
    public function addHotelPhoto(\AppBundle\Entity\HotelPhoto $hotelPhoto)
    {
        $this->hotelPhotos[] = $hotelPhoto;

        return $this;
    }

    /**
     * Remove hotelPhoto
     *
     * @param \AppBundle\Entity\HotelPhoto $hotelPhoto
     */
    public function removeHotelPhoto(\AppBundle\Entity\HotelPhoto $hotelPhoto)
    {
        $this->hotelPhotos->removeElement($hotelPhoto);
    }

    /**
     * Get hotelPhotos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHotelPhotos()
    {
        return $this->hotelPhotos;
    }

    /**
     * Add roomPhoto
     *
     * @param \AppBundle\Entity\RoomPhoto $roomPhoto
     *
     * @return Photo
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
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRoomPhotos()
    {
        return $this->roomPhotos;
    }

    /**
     * Set presetPhoto
     *
     * @param \AppBundle\Entity\PresetPhoto $presetPhoto
     *
     * @return Photo
     */
    public function setPresetPhoto(\AppBundle\Entity\PresetPhoto $presetPhoto = null)
    {
        $this->presetPhoto = $presetPhoto;

        return $this;
    }

    /**
     * Get presetPhoto
     *
     * @return \AppBundle\Entity\PresetPhoto
     */
    public function getPresetPhoto()
    {
        return $this->presetPhoto;
    }

    /**
     * @return mixed
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param mixed $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * @return mixed
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param mixed $height
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }
}
