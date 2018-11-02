<?php

namespace AppBundle\Services;

use AppBundle\Dto\Yandex\AddressDto;

/**
 * Class YandexAddressDecoder
 */
class YandexAddressDecoder
{
    /** @var string */
    private $url = 'https://geocode-maps.yandex.ru/1.x/?format=json&geocode=';
    /** @var array|null */
    private $data;

    /**
     * @param $address
     * @return AddressDto
     */
    public function geocode($address)
    {
        $dto = new AddressDto();
        if (!$address) {
            return $dto;
        }

        return $this->request($dto, $address);
    }

    /**
     * @param AddressDto $dto
     * @param $address
     * @return AddressDto
     */
    private function request(AddressDto $dto, $address)
    {
        $data = json_decode(file_get_contents($this->url . urlencode($address)), true);
        if (json_last_error() != JSON_ERROR_NONE) {
            return $dto;
        }

        if (
            is_array($data) &&
            array_key_exists('response', $data) &&
            $data['response']['GeoObjectCollection']['metaDataProperty']['GeocoderResponseMetaData']['found']
        ) {
            $this->data = $data['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['metaDataProperty']['GeocoderMetaData']['AddressDetails'];
        } else {
            return $dto;
        }

        if (is_array($data)) {
            try {
                $dto->country = $this->getField('Country');
                $dto->administrativeArea = $this->getField('AdministrativeArea');
                $dto->subAdministrativeArea = $this->getField('SubAdministrativeArea');
                $dto->locality = $this->getField('Locality');
                $dto->thoroughfare = $this->getField('Thoroughfare');
                $dto->premise = $this->getField('Premise');
                $dto->postalCode = $this->getField('PostalCode');
            } catch (\Exception $e) {

            }
        }

        $this->data = null;

        return $dto;
    }

    /**
     * @param string $field
     * @return string
     */
    private function getField($field)
    {
        switch ($field) {
            case 'Country':
            case 'AdministrativeArea':
            case 'SubAdministrativeArea':
            case 'Locality':
            case 'Thoroughfare':
                if (array_key_exists($field, $this->data)) {
                    $return = (string)$this->data[$field]["{$field}Name"];
                    $this->data = $this->data[$field];
                    return $return;
                }
                break;

            case 'Premise':
            case 'PostalCode':
                if (array_key_exists($field, $this->data)) {
                    $return = (string)$this->data[$field]["{$field}Number"];
                    $this->data = $this->data[$field];
                    return $return;
                }
                break;
        }

        return '';
    }
}
