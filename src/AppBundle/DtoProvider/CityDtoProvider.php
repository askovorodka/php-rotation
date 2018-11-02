<?php

namespace AppBundle\DtoProvider;

use AppBundle\Dto\CityDto;
use Doctrine\DBAL\Connection;

/**
 * Class CityDtoProvider
 *
 * @package AppBundle\DtoProvider
 */
class CityDtoProvider
{
    /**
     * @var Connection
     */
    private $conn;

    /**
     * CityDtoProvider constructor.
     *
     * @param Connection $conn
     */
    public function __construct(Connection $conn)
    {
        $this->conn = $conn;
    }

    /**
     * @return CityDto[]
     */
    public function findAll(): array
    {
        $qb = $this->conn->createQueryBuilder();
        $qb->select('distinct city_id, city_title')
            ->from('city_region')
            ->orderBy('city_title');

        $data = $qb->execute()->fetchAll();

        $result = [];

        foreach ($data as $row) {
            $result[] = new CityDto(
                (int)$row['city_id'],
                $row['city_title']
            );
        }

        return $result;
    }

}
