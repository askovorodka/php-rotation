<?php

namespace AppBundle\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use AppBundle\Annotation\LoggableTableTitleAnnotation;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Component\Serializer\Annotation\Groups;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="preset_photo", uniqueConstraints={
 *     @UniqueConstraint(name="preset_unique", columns={"preset_id"}),
 *     @UniqueConstraint(name="photo_unique", columns={"photo_id"})
 * })
 * @ORM\Entity
 *
 *
 * @ApiResource(
 *     attributes={
 *         "normalization_context"={"groups"={"preset-photo_GET", "photo_GET"}},
 *         "pagination_client_enabled"=true,
 *         "pagination_client_items_per_page"=true,
 *         "filters"={"api-platform.boolean_filter"}
 *     },
 *     itemOperations={
 *         "preset_photos_get_subresource"={"method"="GET", "access_control"="is_granted('ROLE_USER')"},
 *         "put"={"method"="PUT", "access_control"="is_granted('ROLE_MANAGER')", "denormalization_context"={"groups"={"preset-photo_PUT"}}},
 *         "delete"={"method"="DELETE", "access_control"="is_granted('ROLE_ADMIN')"}
 *     },
 *     collectionOperations={
 *         "get"={"method"="GET", "access_control"="is_granted('ROLE_USER')"},
 *         "preset_photos_get_subresource"={"method"="GET", "access_control"="is_granted('ROLE_USER')"},
 *         "photo_upload"={"route_name"="preset_photo_upload", "access_control"="is_granted('ROLE_MANAGER')"}
 *     }
 * )
 *
 * @Gedmo\Loggable(logEntryClass="AppBundle\Entity\LogEntry")
 * @LoggableTableTitleAnnotation(title="Фотографии подборок")
 */
class PresetPhoto
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Groups({
     *     "preset-photo_GET",
     * })
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Preset", inversedBy="presetPhoto")
     * @ORM\JoinColumn(name="preset_id", referencedColumnName="id", nullable=false)
     *
     * @Gedmo\Versioned
     */
    protected $preset;

    /**
     * @ORM\ManyToOne(targetEntity="Photo", inversedBy="presetPhoto")
     * @ORM\JoinColumn(name="photo_id", referencedColumnName="id", nullable=false)
     *
     * @Groups({
     *     "preset-photo_GET",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $photo;

    /**
     * @ORM\Column(name="alt", type="string", length=255, nullable=true)
     *
     * @Groups({
     *     "preset-photo_GET",
     *     "preset-photo_PUT",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $alt;

    /**
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     *
     * @Groups({
     *     "preset-photo_GET",
     *     "preset-photo_PUT",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $title;

    /**
     * @ORM\Column(name="area_width", type="integer", length=11, nullable=true)
     *
     * @Groups({"preset-photo_GET", "preset-photo_PUT"})
     *
     * @Gedmo\Versioned
     */
    protected $areaWidth;

    /**
     * @ORM\Column(name="area_height", type="integer", length=11, nullable=true)
     *
     * @Groups({"preset-photo_GET", "preset-photo_PUT"})
     *
     * @Gedmo\Versioned
     */
    protected $areaHeight;

    /**
     * @ORM\Column(name="offset_top", type="integer", length=11, nullable=true)
     *
     * @Groups({"preset-photo_GET", "preset-photo_PUT"})
     *
     * @Gedmo\Versioned
     */
    protected $offsetTop;

    /**
     * @ORM\Column(name="offset_left", type="integer", length=11, nullable=true)
     *
     * @Groups({"preset-photo_GET", "preset-photo_PUT"})
     *
     * @Gedmo\Versioned
     */
    protected $offsetLeft;

    /**
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     *
     * @Groups({
     *     "preset-photo_GET",
     *     "preset-photo_PUT",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $description;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * PresetPhoto constructor.
     */
    function __construct()
    {
        $this->createdAt = new \DateTime('now');
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return PresetPhoto
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
     * Set preset
     *
     * @param \AppBundle\Entity\Preset $preset
     *
     * @return PresetPhoto
     */
    public function setPreset(\AppBundle\Entity\Preset $preset)
    {
        $this->preset = $preset;

        return $this;
    }

    /**
     * Get preset
     *
     * @return \AppBundle\Entity\Preset
     */
    public function getPreset()
    {
        return $this->preset;
    }

    /**
     * Set photo
     *
     * @param \AppBundle\Entity\Photo $photo
     *
     * @return PresetPhoto
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
     * @return PresetPhoto
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
     * @return PresetPhoto
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
     * @return PresetPhoto
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
