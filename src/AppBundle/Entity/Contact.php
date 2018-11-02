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
 *         "normalization_context"={"groups"={"contact_GET", "contact-category_GET", "contact-type_GET"}},
 *         "denormalization_context"={"groups"={"contact_SAVE"}},
 *         "access_control"="is_granted('ROLE_USER')",
 *         "pagination_client_enabled"=true,
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
 *     }
 * )
 *
 * @Gedmo\Loggable(logEntryClass="AppBundle\Entity\LogEntry")
 * @LoggableTableTitleAnnotation(title="Контакты")
 */
class Contact
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Groups({"contact_GET"})
     */
    protected $id;

    /**
     * @ORM\Column(name="title", type="string", length=255)
     *
     * @Groups({"contact_GET", "contact_SAVE", "hotel_SAVE"})
     *
     * @Gedmo\Versioned
     */
    protected $title;

    /**
     * @ORM\ManyToOne(targetEntity="ContactCategory", inversedBy="contacts")
     * @ORM\JoinColumn(name="contact_category_id", referencedColumnName="id", nullable=false)
     *
     * @Groups({"contact_GET", "contact_SAVE", "hotel_SAVE"})
     *
     * @Gedmo\Versioned
     */
    protected $contactCategory;

    /**
     * @ORM\ManyToOne(targetEntity="ContactType", inversedBy="contacts")
     * @ORM\JoinColumn(name="contact_type_id", referencedColumnName="id", nullable=false)
     *
     * @Groups({"contact_GET", "contact_SAVE", "hotel_SAVE"})
     *
     * @Gedmo\Versioned
     */
    protected $contactType;

    /**
     * @ORM\ManyToOne(targetEntity="Hotel", inversedBy="contacts")
     * @ORM\JoinColumn(name="hotel_id", referencedColumnName="id", nullable=false)
     *
     * @Gedmo\Versioned
     */
    protected $hotel;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     *
     * @Groups({"contact_GET", "contact_SAVE", "hotel_SAVE"})
     *
     * @Gedmo\Versioned
     */
    protected $isActive;

    /**
     * @ORM\Column(name="is_priority", type="boolean", options={"default" : 0})
     *
     * @Groups({"contact_GET", "contact_SAVE", "hotel_SAVE"})
     *
     * @Gedmo\Versioned
     */
    protected $isPriority;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * Contact constructor.
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
     * @return Contact
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
     * @return Contact
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
     * @return Contact
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
     * @return Contact
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
     * Set contactCategory
     *
     * @param \AppBundle\Entity\ContactCategory $contactCategory
     *
     * @return Contact
     */
    public function setContactCategory(\AppBundle\Entity\ContactCategory $contactCategory)
    {
        $this->contactCategory = $contactCategory;

        return $this;
    }

    /**
     * Get contactCategory
     *
     * @return \AppBundle\Entity\ContactCategory
     */
    public function getContactCategory()
    {
        return $this->contactCategory;
    }

    /**
     * Set contactType
     *
     * @param \AppBundle\Entity\ContactType $contactType
     *
     * @return Contact
     */
    public function setContactType(\AppBundle\Entity\ContactType $contactType)
    {
        $this->contactType = $contactType;

        return $this;
    }

    /**
     * Get contactType
     *
     * @return \AppBundle\Entity\ContactType
     */
    public function getContactType()
    {
        return $this->contactType;
    }

    /**
     * Set hotel
     *
     * @param \AppBundle\Entity\Hotel $hotel
     *
     * @return Contact
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
