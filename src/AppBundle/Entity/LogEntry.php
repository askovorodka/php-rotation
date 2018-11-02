<?php

namespace AppBundle\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Loggable\Entity\MappedSuperclass\AbstractLogEntry;
use Symfony\Component\Security\Core\User\UserInterface;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;

/**
 * Class LogEntry
 *
 * @ORM\Table(
 *     name="logs",
 *  indexes={
 *      @ORM\Index(name="log_class_lookup_idx", columns={"object_class"}),
 *      @ORM\Index(name="log_date_lookup_idx", columns={"logged_at"}),
 *      @ORM\Index(name="log_user_lookup_idx", columns={"username"}),
 *      @ORM\Index(name="log_version_lookup_idx", columns={"object_id", "object_class", "version"})
 *  }
 * )
 *
 * @ORM\Entity(repositoryClass="Gedmo\Loggable\Entity\Repository\LogEntryRepository")
 *
 * @ApiFilter(SearchFilter::class, properties={
 *     "objectId"="exact",
 *     "objectClass"="exact",
 *     "action": "exact",
 *     "username":"exact"})
 * @ApiFilter(DateFilter::class, properties={"loggedAt"})
 *
 * @ApiResource(
 *     itemOperations={
 *         "get"={"method"="GET", "access_control"="is_granted('ROLE_ADMIN')"}
 *     },
 *     collectionOperations={
 *         "get"={"method"="GET", "access_control"="is_granted('ROLE_ADMIN')"}
 *     }
 * )
 */
class LogEntry extends AbstractLogEntry
{
    /**
     * @var array
     * @ORM\Column(type="array", nullable=true)
     */
    protected $oldData = null;

    /**
     * @var UserInterface
     * @ORM\ManyToOne(targetEntity="Symfony\Component\Security\Core\User\UserInterface")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    protected $user;

    /**
     * @return array
     */
    public function getOldData()
    {
        return $this->oldData;
    }

    /**
     * @param array $oldData
     */
    public function setOldData(array $oldData)
    {
        $this->oldData = $oldData;
    }

    /**
     * @return UserInterface|null
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param UserInterface|null $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }
}
