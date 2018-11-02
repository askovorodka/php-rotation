<?php

namespace AppBundle\DtoProvider;

use AppBundle\Dto\RegionDto;
use Doctrine\DBAL\Connection;

/**
 * Class RegionDtoProvider
 *
 * @package AppBundle\DtoProvider
 */
class RegionDtoProvider
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
     * @return RegionDto[]
     */
    public function findAll(): array
    {
        $qb = $this->conn->createQueryBuilder();
        $qb->select('distinct region_id, region_title')
            ->from('city_region')
            ->orderBy('region_title');

        $data = $qb->execute()->fetchAll();

        $result = [];

        foreach ($data as $row) {
            $result[] = new RegionDto(
                (int)$row['region_id'],
                $row['region_title']
            );
        }

        return $result;
    }

}
