<?php

namespace AppBundle\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use AppBundle\Annotation\LoggableTableTitleAnnotation;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="contact_type",indexes={
 *     @ORM\Index(name="is_active", columns={"is_active"})
 * })
 * @ORM\Entity
 *
 * @ApiResource(
 *     attributes={
 *         "normalization_context"={"groups"={"contact-type_GET"}},
 *         "access_control"="is_granted('ROLE_USER')",
 *         "pagination_client_enabled"=true,
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
 * @Gedmo\Loggable(logEntryClass="AppBundle\Entity\LogEntry")
 * * @LoggableTableTitleAnnotation(title="Типы контактов")
 */
class ContactType
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Groups({
     *     "contact-type_GET",
     * })
     */
    protected $id;

    /**
     * @ORM\Column(name="title", type="string", length=255, unique=true)
     *
     * @Groups({
     *     "contact-type_GET",
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
     * @Gedmo\Versioned
     *
     * @Groups({
     *     "contact-type_GET",
     * })
     */
    protected $isActive;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\OneToMany(targetEntity="Contact", mappedBy="contactType")
     */
    protected $contacts;

    /**
     * ContactType constructor.
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
     * @return ContactType
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
     * @return ContactType
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
     * @return ContactType
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
     * Add contact
     *
     * @param \AppBundle\Entity\Contact $contact
     *
     * @return ContactType
     */
    public function addContact(\AppBundle\Entity\Contact $contact)
    {
        $this->contacts[] = $contact;

        return $this;
    }

    /**
     * Remove contact
     *
     * @param \AppBundle\Entity\Contact $contact
     */
    public function removeContact(\AppBundle\Entity\Contact $contact)
    {
        $this->contacts->removeElement($contact);
    }

    /**
     * Get contacts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getContacts()
    {
        return $this->contacts;
    }

    /**
     * @return mixed
     */
    public function getSystemName()
    {
        return $this->systemName;
    }

    /**
     * @param mixed $systemName
     */
    public function setSystemName($systemName): void
    {
        $this->systemName = $systemName;
    }
}
