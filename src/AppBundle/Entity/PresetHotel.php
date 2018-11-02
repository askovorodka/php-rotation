<?php

namespace AppBundle\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use AppBundle\Annotation\LoggableTableTitleAnnotation;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Component\Serializer\Annotation\Groups;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="preset_hotel", uniqueConstraints={
 *     @UniqueConstraint(name="preset_hotel_unique", columns={"preset_id", "hotel_id"})
 * })
 * @ORM\Entity
 *
 * @ApiResource(
 *     attributes={
 *         "normalization_context"={"groups"={"preset-hotel_GET"}},
 *         "pagination_client_enabled"=true,
 *         "pagination_client_items_per_page"=true,
 *         "filters"={"api-platform.boolean_filter"}
 *     },
 *     itemOperations={
 *         "preset_hotels_get_subresource"={"method"="GET", "access_control"="is_granted('ROLE_USER')"},
 *         "put"={"method"="PUT", "access_control"="is_granted('ROLE_MANAGER')", "denormalization_context"={"groups"={"preset-hotel_PUT"}}},
 *         "delete"={"method"="DELETE", "access_control"="is_granted('ROLE_ADMIN')"}
 *     },
 *     collectionOperations={
 *         "get"={"method"="GET", "access_control"="is_granted('ROLE_USER')"},
 *         "preset_hotels_get_subresource"={"method"="GET", "access_control"="is_granted('ROLE_USER')"},
 *         "post"={"method"="POST", "access_control"="is_granted('ROLE_MANAGER')", "denormalization_context"={"groups"={"preset-hotel_POST"}}},
 *     }
 * )
 *
 * @Gedmo\Loggable(logEntryClass="AppBundle\Entity\LogEntry")
 * @LoggableTableTitleAnnotation(title="Привязка отелей к подборкам")
 */
class PresetHotel
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Groups({"preset-hotel_GET"})
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Preset", inversedBy="hotels")
     * @ORM\JoinColumn(name="preset_id", referencedColumnName="id", nullable=false)
     *
     * @Groups({"preset-hotel_POST"})
     *
     * @Gedmo\Versioned
     */
    protected $preset;

    /**
     * @ORM\ManyToOne(targetEntity="Hotel", inversedBy="presets")
     * @ORM\JoinColumn(name="hotel_id", referencedColumnName="id", nullable=false)
     *
     * @Groups({"preset-hotel_GET", "preset-hotel_POST"})
     *
     * @Gedmo\Versioned
     */
    protected $hotel;

    /**
     * @ORM\Column(name="list_order", type="integer", length=11, options={"default" : 0})
     *
     * @Groups({"preset-hotel_GET", "preset-hotel_POST", "preset-hotel_PUT"})
     *
     * @Gedmo\Versioned
     */
    protected $listOrder;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     *
     * @Groups({"preset-hotel_GET", "preset-hotel_POST", "preset-hotel_PUT"})
     *
     * @Gedmo\Versioned
     */
    protected $isActive;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * PresetHotel constructor.
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
     * Set listOrder
     *
     * @param integer $listOrder
     *
     * @return PresetHotel
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
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return PresetHotel
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
     * @return PresetHotel
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
     * @return PresetHotel
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
     * Set hotel
     *
     * @param \AppBundle\Entity\Hotel $hotel
     *
     * @return PresetHotel
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
}
