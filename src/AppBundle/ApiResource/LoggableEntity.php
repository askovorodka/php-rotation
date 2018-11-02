<?php

namespace AppBundle\ApiResource;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Symfony\Routing\RouteNameGenerator;

/**
 * Class LoggableEntity
 *
 * @ApiResource(
 *     itemOperations={
 *          "get_loggable_entity"={"method"="GET", "access_control"="is_granted('ROLE_ADMIN')"}
 *     },
 *     collectionOperations={
 *          "get_loggable_entities"={"method"="GET", "access_control"="is_granted('ROLE_ADMIN')"}
 *     }
 * )
 */
class LoggableEntity
{
    /**
     * @var string
     *
     * @ApiProperty(identifier=true)
     */
    protected $name;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var string
     */
    protected $logRecordsLink;

    /**
     * LoggableEntity constructor.
     * @param string $class
     */
    public function __construct(string $class)
    {
        $this->class = $class;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @param string $class
     */
    public function setClass(string $class)
    {
        $this->class = $class;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        $class = explode('\\', $this->class);
        return str_replace('_', '-', RouteNameGenerator::inflector(end($class), true));
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->id = $name;
    }

    /**
     * @return string
     */
    public function getLogRecordsLink(): string
    {
        return $this->logRecordsLink;
    }

    /**
     * @param string $logRecordsLink
     */
    public function setLogRecordsLink(string $logRecordsLink)
    {
        $this->logRecordsLink = $logRecordsLink;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }
}
