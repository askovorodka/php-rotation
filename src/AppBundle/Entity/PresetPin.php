<?php

namespace AppBundle\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Class PresetPin
 *
 * @package AppBundle\Entity
 *
 * @ORM\Table(name="preset_pin")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PresetPinRepository")
 *
 * @ApiResource(
 *     attributes={
 *         "normalization_context"={"groups"={"preset-pin_GET"}}
 *     },
 *     collectionOperations={
 *         "get"={"method"="GET", "access_control"="is_granted('ROLE_MANAGER')"},
 *         "post"={"method"="POST", "access_control"="is_granted('ROLE_MANAGER')"},
 *     },
 *     itemOperations={
 *         "get"={"method"="GET", "access_control"="is_granted('ROLE_MANAGER')"},
 *         "put"={"method"="PUT", "access_control"="is_granted('ROLE_MANAGER')"},
 *         "delete"={"method"="DELETE", "access_control"="is_granted('ROLE_MANAGER')"},
 *     },
 * )
 *
 * @Gedmo\Loggable(logEntryClass="AppBundle\Entity\LogEntry")
 */
class PresetPin
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Groups({"preset-pin_GET"})
     *
     * @Gedmo\Versioned
     */
    private $id;

    /**
     * @var PinCategory
     *
     * @ORM\ManyToOne(targetEntity="PinCategory", fetch="EAGER")
     * @ORM\JoinColumn(name="pin_category_id", referencedColumnName="id")
     *
     * @Gedmo\Versioned
     */
    private $pinCategory;

    /**
     * @var Preset|null
     *
     * @ORM\ManyToOne(targetEntity="Preset")
     * @ORM\JoinColumn(name="base_preset_id", referencedColumnName="id", nullable=false)
     *
     * @ApiSubresource
     *
     * @Groups({"preset-pin_GET"})
     *
     * @Gedmo\Versioned
     */
    private $basePreset;

    /**
     * @var Preset|null
     *
     * @ORM\ManyToOne(targetEntity="Preset")
     * @ORM\JoinColumn(name="pinned_preset_id", referencedColumnName="id", nullable=false)
     *
     * @ApiSubresource
     *
     * @Groups({"preset-pin_GET"})
     *
     * @Gedmo\Versioned
     */
    private $pinnedPreset;

    /**
     * @var int|null
     *
     * @ORM\Column(name="city_id", type="integer", nullable=true)
     *
     * @Groups({"preset-pin_GET"})
     *
     * @Gedmo\Versioned
     */
    private $cityId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="region_id", type="integer", nullable=true)
     *
     * @Groups({"preset-pin_GET"})
     *
     * @Gedmo\Versioned
     */
    private $regionId;

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="integer", nullable=false)
     *
     * @Groups({"preset-pin_GET"})
     *
     * @Gedmo\Versioned
     */
    private $position;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=false)
     *
     * @Groups({"preset-pin_GET"})
     *
     * @Gedmo\Versioned
     */
    private $isActive;

    /**
     * @var \DateTimeInterface
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     *
     * @Groups({"preset-pin_GET"})
     *
     * @Gedmo\Versioned
     */
    private $createdAt;

    /**
     * PresetPin constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->isActive = true;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Preset|null
     */
    public function getBasePreset(): ?Preset
    {
        return $this->basePreset;
    }

    /**
     * @param Preset|null $basePreset
     * @return PresetPin
     */
    public function setBasePreset(?Preset $basePreset): PresetPin
    {
        $this->basePreset = $basePreset;
        return $this;
    }

    /**
     * @return Preset|null
     */
    public function getPinnedPreset(): ?Preset
    {
        return $this->pinnedPreset;
    }

    /**
     * @param Preset|null $pinnedPreset
     * @return PresetPin
     */
    public function setPinnedPreset(?Preset $pinnedPreset): PresetPin
    {
        $this->pinnedPreset = $pinnedPreset;
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
     * @return PresetPin
     */
    public function setCityId(?int $cityId): PresetPin
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
     * @return PresetPin
     */
    public function setRegionId(?int $regionId): PresetPin
    {
        $this->regionId = $regionId;
        return $this;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @param int $position
     * @return PresetPin
     */
    public function setPosition(int $position): PresetPin
    {
        $this->position = $position;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @param bool $isActive
     * @return PresetPin
     */
    public function setIsActive(bool $isActive): PresetPin
    {
        $this->isActive = $isActive;
        return $this;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTimeInterface $createdAt
     * @return PresetPin
     */
    public function setCreatedAt(\DateTimeInterface $createdAt): PresetPin
    {
        $this->createdAt = $createdAt;
        return $this;
    }


    /**
     * Set pinCategory.
     *
     * @param \AppBundle\Entity\PinCategory $pinCategory
     *
     * @return PresetPin
     */
    public function setPinCategory(\AppBundle\Entity\PinCategory $pinCategory)
    {
        $this->pinCategory = $pinCategory;

        return $this;
    }

    /**
     * Get pinCategory.
     *
     * @return \AppBundle\Entity\PinCategory
     */
    public function getPinCategory()
    {
        return $this->pinCategory;
    }
}
