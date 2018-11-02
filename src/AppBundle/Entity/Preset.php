<?php

namespace AppBundle\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use AppBundle\Annotation\LoggableTableTitleAnnotation;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PresetRepository")
 *
 * @ApiResource(
 *     attributes={
 *         "normalization_context"={"groups"={"preset_GET"}},
 *         "denormalization_context"={"groups"={"preset_SAVE"}},
 *         "pagination_client_enabled"=true,
 *         "pagination_client_items_per_page"=true,
 *         "filters"={"api-platform.boolean_filter"}
 *     },
 *     collectionOperations={
 *         "get"={"method"="GET", "access_control"="is_granted('ROLE_USER')"},
 *         "post"={"method"="POST", "access_control"="is_granted('ROLE_MANAGER')"}
 *     },
 *     itemOperations={
 *         "get"={"method"="GET", "access_control"="is_granted('ROLE_USER')"},
 *         "put"={"method"="PUT", "access_control"="is_granted('ROLE_MANAGER')"},
 *         "delete"={"method"="DELETE", "access_control"="is_granted('ROLE_ADMIN')"}
 *     },
 * )
 *
 * @Gedmo\Loggable(logEntryClass="AppBundle\Entity\LogEntry")
 * @LoggableTableTitleAnnotation(title="Подборки")
 */
class Preset
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Groups({
     *     "preset_GET",
     *     "preset-pin_GET",
     *     "local-hotel-pin_GET",
     * })
     */
    protected $id;

    /**
     * @ORM\Column(name="sysname", type="string", length=255, unique=true, nullable=true)
     *
     * @Groups({
     *     "preset_GET",
     *     "preset_SAVE",
     *     "preset-pin_GET",
     *     "local-hotel-pin_GET",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $sysname;

    /**
     * @ORM\Column(name="title", type="string", nullable=false, length=255)
     *
     * @Groups({
     *     "preset_GET",
     *     "preset_SAVE",
     *     "preset-pin_GET",
     *     "local-hotel-pin_GET",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $title;

    /**
     * @ORM\Column(name="description", type="text")
     *
     * @Groups({"preset_GET", "preset_SAVE"})
     *
     * @Gedmo\Versioned
     */
    protected $description;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     *
     * @Groups({"preset_GET", "preset_SAVE"})
     *
     * @Gedmo\Versioned
     */
    protected $isActive;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\OneToMany(targetEntity="PresetHotel", mappedBy="preset")
     *
     * @ApiSubresource
     *
     * @var ArrayCollection
     */
    protected $presetHotels;

    /**
     * @ORM\OneToMany(targetEntity="PresetPhoto", mappedBy="preset")
     *
     * @ApiSubresource
     *
     * @var ArrayCollection
     */
    protected $presetPhoto;

    /**
     * @var null|PresetCategory
     *
     * @ORM\ManyToOne(targetEntity="PresetCategory")
     * @ORM\JoinColumn(name="preset_category_id", referencedColumnName="id")
     *
     * @ApiSubresource
     *
     * @Groups({"preset_GET", "preset_SAVE"})
     *
     * @Gedmo\Versioned
     */
    protected $presetCategory;

    /**
     * @var null|array
     *
     * @ORM\Column(name="params", type="json")
     *
     * @Groups({"preset_GET", "preset_SAVE"})
     *
     * @Gedmo\Versioned
     */
    protected $params;

    /**
     * @var null|int
     *
     * @ORM\Column(name="city_id", type="integer", nullable=true)
     *
     * @Groups({"preset_GET", "preset_SAVE"})
     *
     * @Gedmo\Versioned
     */
    protected $cityId;

    /**
     * @var null|int
     *
     * @ORM\Column(name="region_id", type="integer", nullable=true)
     *
     * @Groups({"preset_GET", "preset_SAVE"})
     *
     * @Gedmo\Versioned
     */
    protected $regionId;

    /**
     * Preset constructor.
     */
    public function __construct()
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
     * Set sysname
     *
     * @param string $sysname
     *
     * @return Preset
     */
    public function setSysname($sysname)
    {
        $this->sysname = $sysname;

        return $this;
    }

    /**
     * Get sysname
     *
     * @return string
     */
    public function getSysname()
    {
        return $this->sysname;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Preset
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
     * Set description
     *
     * @param string $description
     *
     * @return Preset
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return Preset
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
     * @return Preset
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
     * @param \AppBundle\Entity\PresetHotel $hotel
     *
     * @return Preset
     */
    public function addPresetHotel(\AppBundle\Entity\PresetHotel $hotel)
    {
        $this->presetHotels[] = $hotel;

        return $this;
    }

    /**
     * Remove hotel
     *
     * @param \AppBundle\Entity\PresetHotel $hotel
     */
    public function removePresetHotel(\AppBundle\Entity\PresetHotel $hotel)
    {
        $this->presetHotels->removeElement($hotel);
    }

    /**
     * Get hotels
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPresetHotels()
    {
        return $this->presetHotels;
    }

    /**
     * Add presetPhoto.
     *
     * @param \AppBundle\Entity\PresetPhoto $presetPhoto
     *
     * @return Preset
     */
    public function setPresetPhoto(\AppBundle\Entity\PresetPhoto $presetPhoto)
    {
        $this->presetPhoto = $presetPhoto;

        return $this;
    }

    /**
     * Remove presetPhoto.
     *
     * @param \AppBundle\Entity\PresetPhoto $presetPhoto
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removePresetPhoto(\AppBundle\Entity\PresetPhoto $presetPhoto)
    {
        return $this->presetPhoto->removeElement($presetPhoto);
    }

    /**
     * Get presetPhoto.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPresetPhoto()
    {
        return $this->presetPhoto;
    }

    /**
     * @return PresetCategory|null
     */
    public function getPresetCategory(): ?PresetCategory
    {
        return $this->presetCategory;
    }

    /**
     * @param PresetCategory|null $presetCategory
     * @return Preset
     */
    public function setPresetCategory(?PresetCategory $presetCategory): Preset
    {
        $this->presetCategory = $presetCategory;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getParams(): ?array
    {
        return $this->params;
    }

    /**
     * @param array|null $params
     * @return Preset
     */
    public function setParams(?array $params): Preset
    {
        $this->params = $params;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getCityId(): ?int
    {
        return $this->cityId;
    }

    /**
     * @param int|null $cityId
     * @return Preset
     */
    public function setCityId(?int $cityId): Preset
    {
        $this->cityId = $cityId;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getRegionId(): ?int
    {
        return $this->regionId;
    }

    /**
     * @param int|null $regionId
     * @return Preset
     */
    public function setRegionId(?int $regionId): Preset
    {
        $this->regionId = $regionId;
        return $this;
    }
}
