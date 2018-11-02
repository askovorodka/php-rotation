<?php

namespace AppBundle\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use AppBundle\Annotation\LoggableTableTitleAnnotation;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ApiResource(
 *     attributes={
 *         "normalization_context"={"groups"={"user", "user-read"}},
 *         "denormalization_context"={"groups"={"user", "user-write"}}
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
 * @LoggableTableTitleAnnotation(title="Пользователи")
 */
class User extends BaseUser implements \Symfony\Component\Security\Core\User\UserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Groups({"user"})
     *
     * @Gedmo\Versioned
     */
    protected $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Groups({"user"})
     *
     * @Gedmo\Versioned
     */
    protected $fullname;

    /**
     * @Groups({"user-write"})
     *
     * @Gedmo\Versioned
     */
    protected $plainPassword;

    public function setFullname($fullname)
    {
        $this->fullname = $fullname;

        return $this;
    }

    public function getFullname()
    {
        return $this->fullname;
    }

    public function isUser(UserInterface $user = null)
    {
        return $user instanceof self && $user->id === $this->id;
    }
}
