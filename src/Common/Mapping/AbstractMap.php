<?php

namespace Common\Mapping;

/**
 * Базовый класс для маппинга
 *
 * @package app\infrastructure\json\mapping
 */
abstract class AbstractMap
{
    /** @var array */
    protected $items = [];

    /**
     * @return array Набор добавленных элементов
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Проверка возможности добавления элемента в коллекцию
     *
     * @param mixed $item
     *
     * @return bool
     */
    abstract public function itemIsValid($item);

    /**
     * Сравнение элементов на идентичность
     *
     * @param mixed $item1
     * @param mixed $item2
     *
     * @return bool
     */
    abstract public function itemsEquals($item1, $item2);

    /**
     * Добавление элемента
     *
     * @param mixed $item
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function add($item)
    {
        $this->validateItem($item);

        if (!$this->itemExists($item)) {
            return $this->_add($item);
        }

        return false;
    }

    /**
     * Внутренний механизм добавления
     *
     * @param $item
     *
     * @return bool
     */
    protected function _add($item)
    {
        $this->items[] = $item;
        return true;
    }

    /**
     * Проверка на существование элемента в коллекции
     *
     * @param $item
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function itemExists($item)
    {
        $this->validateItem($item);

        foreach ($this->items as $mapItem) {
            if ($this->itemsEquals($item, $mapItem)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param mixed $item
     *
     * @throws \Exception
     */
    protected function validateItem($item)
    {
        if (!$this->itemIsValid($item)) {
            throw new \Exception('Тип переданного элемента не поддерживается');
        }
    }

    /**
     * @return int Количество хранимых элементов
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * Очистка
     */
    public function clear()
    {
        $this->items = [];
    }
}
