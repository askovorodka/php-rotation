<?php

namespace AppBundle\Services\HotelsList\HotelsListBuilder;

use AppBundle\Services\HotelsList\HotelsListServiceInterface;
use Doctrine\DBAL\Connection;

class AdministrativeAreaHotelsListBuilder extends AbstractHotelsListBuilder
{
    /**
     * @var string[]
     */
    private $priorityAreas;

    public function __construct(Connection $conn, array $priorityAreas)
    {
        parent::__construct($conn);
        $this->priorityAreas = $priorityAreas;
    }

    function getListName(): string
    {
        return HotelsListServiceInterface::ADMINISTRATIVE_AREA_LIST_NAME;
    }

    protected function getQuery(): string
    {
        $qb = $this->conn->createQueryBuilder();

        $qb->select('distinct administrative_area, id')
            ->from('hotel_with_min_price')
            ->where('administrative_area <> \'\'');

        if ($this->priorityAreas) {
            $priorityAreas = array_map(function ($area) {
                return "'$area'";
            }, $this->priorityAreas);
            $priorityAreasReversedStr = implode(',', array_reverse($priorityAreas));
            $qb->addOrderBy("FIELD(administrative_area, $priorityAreasReversedStr)", "DESC");
        }

        $qb->addOrderBy('administrative_area', 'asc');

        return $qb->getSQL();
    }

    protected function getCategoryId(array $row)
    {
        return $row['administrative_area'];
    }

    protected function getCategoryData(array $row): array
    {
        return [];
    }

    protected function getHotelId(array $row)
    {
        return $row['id'];
    }
}
