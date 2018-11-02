<?php

namespace AppBundle\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Class PresetCategory
 *
 * @package AppBundle\Entity
 *
 * @ORM\Entity
 *
 * @ApiResource
 *
 * @Gedmo\Loggable(logEntryClass="AppBundle\Entity\LogEntry")
 */
class PresetCategory
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Groups({"preset_GET"})
     */
    protected $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="sysname", type="string", length=255, unique=true)
     *
     * @Groups({"preset_GET"})
     *
     * @Gedmo\Versioned
     */
    protected $sysname;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="text")
     *
     * @Groups({"preset_GET"})
     *
     * @Gedmo\Versioned
     */
    protected $description;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getSysname(): ?string
    {
        return $this->sysname;
    }

    /**
     * @param null|string $sysname
     * @return PresetCategory
     */
    public function setSysname(?string $sysname): PresetCategory
    {
        $this->sysname = $sysname;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param null|string $description
     * @return PresetCategory
     */
    public function setDescription(?string $description): PresetCategory
    {
        $this->description = $description;
        return $this;
    }

}
