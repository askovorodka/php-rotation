<?php

namespace AppBundle\Helper;

use Symfony\Component\HttpFoundation\Request;

final class MobileRequestHelper
{
    /**
     * mobile routes names
     */
    const MOBILE_OPERATIONS = [
        'hotels_catalog_mobile.search',
        'get_hotel_by_deal_offer_mobile',
        'get_hotel_view_by_deal_offer_mobile',
    ];

    /**
     * method check if request router name mobile
     * @param $routeName
     * @return bool
     */
    public static function isMobileOperationName(string $operationName)
    {
        return in_array($operationName, self::MOBILE_OPERATIONS);
    }

    /**
     * method find request route in Request object
     * @param Request $request
     * @return null|string
     */
    public static function getRouteName(Request $request): ?string
    {
        try {
            if ($request->attributes->get('_api_collection_operation_name')) {
                return $request->attributes->get('_api_collection_operation_name');
            }
            if ($request->attributes->get('_api_item_operation_name')) {
                return $request->attributes->get('_api_item_operation_name');
            }
        } catch (\Throwable $exception) {
            return '';
        }
    }

    /**
     * method modify file extension by regular exprerssion *.svg -> *.png
     * @param string $filename
     * @return string
     */
    public static function changePngExtention($filename)
    {
        return preg_replace("/(?<extension>\.svg)$/i", ".png", $filename);
    }
}
