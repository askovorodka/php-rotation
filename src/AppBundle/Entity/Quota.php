<?php

namespace AppBundle\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use AppBundle\Exception\QuantityCannotBeDecreasedException;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use AppBundle\Annotation\LoggableTableTitleAnnotation;
use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Quota
 *
 * @ORM\Table(name="quota", indexes={
 *     @ORM\Index(name="rooms_dates", columns={"room_id","date"})
 * })
 * @ORM\Entity(repositoryClass="AppBundle\Repository\QuotaRepository")
 * @ApiResource(
 *    collectionOperations={
 *          "admin_quotas_list"={
 *              "method"="GET",
 *              "normalization_context"={
 *                  "groups"={
 *                      "admin-quotas-list",
 *                  },
 *               },
 *              "pagination_client_enabled"=true,
 *              "pagination_client_items_per_page"=true,
 *              "access_control"="is_granted('ROLE_MANAGER')",
 *          }
 *     }
 * )
 *
 * @ApiFilter(SearchFilter::class, properties={
 *     "room.id":"exact",
 *     "room.hotel.id": "exact",
 *     "room.hotel.title": "partial",
 *     "room.title": "partial",
 * })
 * @ApiFilter(DateFilter::class, properties={"date"})
 * @ApiFilter(OrderFilter::class, properties={"date"})
 * @ApiResource(
 *     itemOperations={
 *          "get"={"method"="GET", "access_control"="is_granted('ROLE_USER')"},
 *          "put"={"method"="PUT", "access_control"="is_granted('ROLE_MANAGER')"},
 *     },
 *     collectionOperations = {
 *          "increase_quantity" = {
 *              "method"="POST",
 *              "access_control"="is_granted('ROLE_MANAGER')",
 *              "route_name"="increase_quantity",
 *              "normalization_context"={
 *                  "groups"={"quota_context"}
 *              },
 *              "swagger_context" = {
 *                  "parameters" = {
 *                      {
 *                          "name" = "roomId",
 *                          "required" = "true",
 *                          "type" = "integer",
 *                      },
 *                      {
 *                          "name" = "beginDate",
 *                          "required" = "true",
 *                          "type" = "string",
 *                          "format"="date-time",
 *                      },
 *                      {
 *                          "name" = "endDate",
 *                          "required" = "true",
 *                          "type" = "string",
 *                          "format"="date-time",
 *                      },
 *                      {
 *                          "name" = "delta",
 *                          "required" = "true",
 *                          "type" = "string",
 *                          "format"="integer",
 *                      },
 *                      {
 *                          "name" = "dayType",
 *                          "type" = "string",
 *                          "enum" = {"weekday", "day_off", "all"},
 *                      },
 *                  }
 *              }
 *          },
 *          "decrease_quantity" = {
 *              "method"="POST",
 *              "access_control"="is_granted('ROLE_MANAGER')",
 *              "route_name"="decrease_quantity",
 *              "normalization_context"={
 *                  "groups"={"quota_context"}
 *              },
 *              "swagger_context" = {
 *                  "parameters" = {
 *                      {
 *                          "name" = "roomId",
 *                          "required" = "true",
 *                          "type" = "integer",
 *                      },
 *                      {
 *                          "name" = "beginDate",
 *                          "required" = "true",
 *                          "type" = "string",
 *                          "format"="date-time",
 *                      },
 *                      {
 *                          "name" = "endDate",
 *                          "required" = "true",
 *                          "type" = "string",
 *                          "format"="date-time",
 *                      },
 *                      {
 *                          "name" = "delta",
 *                          "required" = "true",
 *                          "type" = "string",
 *                          "format"="integer",
 *                      },
 *                      {
 *                          "name" = "dayType",
 *                          "type" = "string",
 *                          "enum" = {"weekday", "day_off", "all"},
 *                      },
 *                  }
 *              }
 *          }
 *     }
 * )
 *
 * @Gedmo\Loggable(logEntryClass="AppBundle\Entity\LogEntry")
 * @LoggableTableTitleAnnotation(title="Квоты")
 */
class Quota
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @Groups({"admin-quotas-list"})
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Groups({"quota_context"})
     */
    protected $id;

    /**
     * @var Room
     *
     * @ORM\ManyToOne(targetEntity="Room", inversedBy="quotas")
     * @ORM\JoinColumn(name="room_id", referencedColumnName="id", nullable=false)
     *
     * @ApiSubresource
     * @Groups({"admin-quotas-list"})
     * @Gedmo\Versioned
     *
     * @Groups({"quota_context"})
     */
    protected $room;

    /**
     * @var \DateTimeInterface
     *
     * @ORM\Column(name="date", type="date", options={"comment":"Дата квоты (день)"})
     * @Groups({"admin-quotas-list", "quota_context"})
     */
    protected $date;

    /**
     * @ORM\Column(name="quantity", type="smallint", options={"comment": "Кол-во номеров по квоте в день"})
     * @Groups({"admin-quotas-list", "quota_context"})
     */
    protected $quantity;

    /**
     * @ORM\Column(name="quantity_free", type="smallint", options={"comment": "Оставшееся кол-во номеров по квоте в день"})
     * @Groups({"admin-quotas-list", "quota_context"})
     */
    protected $quantityFree;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Уменьшает кол-во мест в квоте
     *
     * @param int $delta
     *
     * @throws QuantityCannotBeDecreasedException, \InvalidArgumentException
     */
    public function decreaseQuantity(int $delta): void
    {
        if ($delta < 0) {
            throw new \InvalidArgumentException('delta не может быть отрицательной');
        }

        if ($this->quantityFree - $delta < 0) {
            throw QuantityCannotBeDecreasedException::withSlug(
                $this->room->getId(),
                $delta,
                $this->date,
                $this->quantityFree
            );
        }

        $this->quantityFree -= $delta;
        $this->quantity -= $delta;
    }

    /**
     * Увеличивает кол-во мест в квоте
     *
     * @param int $delta
     */
    public function increaseQuantity(int $delta): void
    {
        if ($delta < 0) {
            throw new \InvalidArgumentException('delta не может быть отрицательной');
        }

        $this->quantity += $delta;
        $this->quantityFree += $delta;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTimeInterface $date
     *
     * @return Quota
     */
    public function setDate(\DateTimeInterface $date): Quota
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return Room
     */
    public function getRoom()
    {
        return $this->room;
    }

    /**
     * @param Room $room
     *
     * @return Quota
     */
    public function setRoom(Room $room): Quota
    {
        $this->room = $room;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param mixed $quantity
     * @return Quota
     * @throws QuantityCannotBeDecreasedException
     */
    public function setQuantity(int $quantity): Quota
    {
        $delta = $quantity - $this->quantity;

        if ($delta > 0) {
            $this->increaseQuantity($delta);
        } else {
            $this->decreaseQuantity(-$delta);
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getQuantityFree()
    {
        return $this->quantityFree;
    }
}
