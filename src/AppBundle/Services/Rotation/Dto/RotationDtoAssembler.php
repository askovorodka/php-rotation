<?php

namespace AppBundle\Services\Rotation\Dto;

use Symfony\Component\HttpFoundation\Request;

class RotationDtoAssembler
{

    const FILTER_PRESET_CITY_ID = 'city_id';
    const FILTER_PRESET_REGION_ID = 'region_id';
    const FILTER_PRESET_SYSNAME = 'sysname';
    const FILTER_PRESET_CATEGORY_SYSNAME = 'category_sysname';
    const ORDER_PRESET_BY_USER_SELECTED = 'by_user_selected';
    const ORDER_PRESET_BY_NEW = 'by_new';
    const ORDER_PRESET_BY_SALES_LEADER = 'by_sales_leader';

    /** @var Request */
    private $request;

    /**
     * constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return RotationDto
     */
    public function assemble(): RotationDto
    {
        $rotationDto = new RotationDto();

        //city_id
        if ($this->request->get(self::FILTER_PRESET_CITY_ID)) {
            $rotationDto->presetCityId = (int) $this->request->get(self::FILTER_PRESET_CITY_ID);
        }

        //region_id
        if ($this->request->get(self::FILTER_PRESET_REGION_ID)) {
            $rotationDto->presetRegionId = (int) $this->request->get(self::FILTER_PRESET_REGION_ID);
        }

        //sysname
        if ($this->request->get(self::FILTER_PRESET_SYSNAME)) {
            $rotationDto->presetSysname = (string) $this->request->get(self::FILTER_PRESET_SYSNAME);
        }

        //category_sysname
        if ($this->request->get(self::FILTER_PRESET_CATEGORY_SYSNAME)) {
            $rotationDto->presetCategorySysname = (string) $this->request->get(self::FILTER_PRESET_CATEGORY_SYSNAME);
        }

        //user_selected order
        if ($this->request->get(self::ORDER_PRESET_BY_USER_SELECTED)) {
            $rotationDto->presetOrderByUserSelected = (string) $this->request->get(self::ORDER_PRESET_BY_USER_SELECTED);
        }

        //new order
        if ($this->request->get(self::ORDER_PRESET_BY_NEW)) {
            $rotationDto->presetOrderByNew = (string) $this->request->get(self::ORDER_PRESET_BY_NEW);
        }

        //sales leader order
        if ($this->request->get(self::ORDER_PRESET_BY_SALES_LEADER)) {
            $rotationDto->presetOrderBySalesLeader = (string) $this->request->get(self::ORDER_PRESET_BY_SALES_LEADER);
        }

        return $rotationDto;
    }
}
