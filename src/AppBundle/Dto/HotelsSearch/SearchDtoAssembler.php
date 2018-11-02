<?php

namespace AppBundle\Dto\HotelsSearch;

use Symfony\Component\HttpFoundation\Request;

class SearchDtoAssembler
{
    /** Имена GET-параметров фильтров */
    const AREA_FILTER_INPUT_PARAM = 'area';
    const HOTEL_CATEGORIES_FILTER_INPUT_PARAM = 'hotel_categories';
    const HOTEL_AMENITIES_FILTER_INPUT_PARAM = 'hotel_amenities';
    const ROOM_AMENITIES_FILTER_INPUT_PARAM = 'room_amenities';
    const PRICE_GTE_FILTER_INPUT_PARAM = 'price_gte';
    const PRICE_LTE_FILTER_INPUT_PARAM = 'price_lte';
    const ORDER_FILTER_INPUT_PARAM = 'order';

    /**
     * @var Request
     */
    private $request;

    /**
     * SearchDtoAssembler constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Метод возвращает объект данных для поиска отелей
     *
     * @return SearchDto
     *
     * @throws \Exception
     */
    public function assemble()
    {
        if (!$this->request) {
            throw new \Exception('request must be set');
        }

        return new SearchDto(
            $this->getStringFilter(self::AREA_FILTER_INPUT_PARAM),
            $this->getIntegerArrayFilter(self::HOTEL_CATEGORIES_FILTER_INPUT_PARAM),
            $this->getIntegerArrayFilter(self::HOTEL_AMENITIES_FILTER_INPUT_PARAM),
            $this->getIntegerArrayFilter(self::ROOM_AMENITIES_FILTER_INPUT_PARAM),
            $this->getIntegerFilter(self::PRICE_GTE_FILTER_INPUT_PARAM),
            $this->getIntegerFilter(self::PRICE_LTE_FILTER_INPUT_PARAM),
            $this->getOrderFilter(self::ORDER_FILTER_INPUT_PARAM)
        );
    }

    /**
     * @param string $key
     *
     * @return string[]
     */
    protected function getOrderFilter($key)
    {
        $result = $this->request->get($key, []);
        $errorMessage = '`order` filter must be array with values in (asc|desc) range';
        if (!is_array($result)) {
            throw new \InvalidArgumentException($errorMessage);
        }

        array_walk($result, function (&$value) use ($errorMessage) {
            if (!in_array($value, ['asc', 'desc'])) {
                throw new \InvalidArgumentException($errorMessage);
            }
        });

        return $result;
    }

    /**
     * @param $key
     *
     * @return int
     */
    protected function getIntegerFilter($key)
    {
        $result = $this->request->get($key, null);
        if ($result !== null && (int)($result) < 0) {
            throw new \InvalidArgumentException(sprintf('`%s` filter must be an integer nonnegative number', $key));
        }

        return (int)$result;
    }

    /**
     * @param $key
     *
     * @return string
     */
    protected function getStringFilter($key)
    {
        $result = $this->request->get($key, null);
        if ($result !== null && !is_string($result)) {
            throw new \InvalidArgumentException(sprintf('`%s` filter must be a single string', $key));
        }

        return $result;
    }

    /**
     * @param $key
     *
     * @return int[]
     */
    protected function getIntegerArrayFilter($key)
    {
        $result = [];

        $value = $this->request->get($key, null);
        if ($value !== null) {
            $errorMessage = sprintf('`%s` filter must be a string containing integers separated with comma', $key);
            if (!(
                is_string($value)
                && ($result = (array)explode(',', $value))
                && is_array($result)
            )) {
                throw new \InvalidArgumentException($errorMessage);
            }

            array_walk($result, function (&$value) use ($errorMessage) {
                if (!is_numeric($value)) {
                    throw new \InvalidArgumentException($errorMessage);
                }
                $value = (int)$value;
            });
        }

        return $result;
    }
}
