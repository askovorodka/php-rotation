<?php

namespace AppBundle\Services\HotelsList\HotelsListBuilder;

use AppBundle\Services\HotelsList\HotelsList;
use Doctrine\DBAL\Connection;

abstract class AbstractHotelsListBuilder implements HotelsListBuilderInterface
{
    /**
     * @var Connection
     */
    protected $conn;

    public function __construct(Connection $conn)
    {
        $this->conn = $conn;
    }

    /**
     * @inheritdoc
     */
    public function buildList(): HotelsList
    {
        $query = $this->getQuery();
        $data = $this->conn->fetchAll($query);

        $list = new HotelsList();
        foreach ($data as $row) {
            $categoryId = $this->getCategoryId($row);

            if (!$list->categoryExists($categoryId)) {
                $categoryData = $this->getCategoryData($row);
                $list->addCategory($categoryId, $categoryData);
            }

            $hotelId = $this->getHotelId($row);
            $list->addHotel($categoryId, $hotelId);
        }

        return $list;
    }

    /**
     * @inheritdoc
     */
    abstract function getListName(): string;

    abstract protected function getQuery(): string;

    abstract protected function getCategoryId(array $row);

    abstract protected function getCategoryData(array $row): array;

    abstract protected function getHotelId(array $row);
}
