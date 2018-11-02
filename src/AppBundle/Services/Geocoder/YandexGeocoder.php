<?php

namespace AppBundle\Services\Geocoder;

use AppBundle\Services\Geocoder\Dto\AddressDto;
use AppBundle\Services\Geocoder\Dto\MetroDto;
use AppBundle\Services\Geocoder\ValueObjects\CoordinatesPoint;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;

class YandexGeocoder implements GeocoderInterface
{
    private const YANDEX_GEOCODER_URL = 'https://geocode-maps.yandex.ru/1.x/';

    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * YandexGeocoder constructor.
     *
     * @param ClientInterface $httpClient
     * @param LoggerInterface $logger
     */
    public function __construct(ClientInterface $httpClient, LoggerInterface $logger)
    {
        $this->httpClient = $httpClient;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function findAddress(string $searchStr): AddressDto
    {
        $addressDto = new AddressDto();

        try {
            $response = $this->httpClient->request('GET', self::YANDEX_GEOCODER_URL, [
                'query' => [
                    'format' => 'json',
                    'results' => 1,
                    'geocode' => $searchStr,
                ],
            ]);
        } catch (GuzzleException $e) {
            $this->logger->error($e->getMessage());

            return $addressDto;
        }

        $responseContent = $response->getBody()->getContents();

        if (($statusCode = $response->getStatusCode()) !== 200) {
            $this->logger->error("Yandex geocoder response code: $statusCode", [
                'request_address' => $searchStr,
                'response_content' => $responseContent,
            ]);

            return $addressDto;
        }

        $this->fillAddressDto($responseContent, $addressDto);

        return $addressDto;
    }

    /**
     * @inheritdoc
     */
    public function findNearestMetro(CoordinatesPoint $point): MetroDto
    {
        $metroDto = new MetroDto();

        try {
            $response = $this->httpClient->request('GET', self::YANDEX_GEOCODER_URL, [
                'query' => [
                    'format' => 'json',
                    'kind' => 'metro',
                    'results' => 1,
                    'geocode' => $point->getLongitude() . ',' . $point->getLatitude(),
                ],
            ]);
        } catch (GuzzleException $e) {
            $this->logger->error($e->getMessage());

            return $metroDto;
        }

        $responseContent = $response->getBody()->getContents();

        if (($statusCode = $response->getStatusCode()) !== 200) {
            $this->logger->error("Yandex geocoder response code: $statusCode", [
                'request_address' => (string)$point,
                'response_content' => $responseContent,
            ]);

            return $metroDto;
        }

        $this->fillMetroDto($responseContent, $metroDto);

        return $metroDto;
    }

    private function fillAddressDto(string $responseContent, AddressDto $addressDto): void
    {
        $data = json_decode($responseContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->logger->error('Json decode error: ' . json_last_error_msg(), [
                'response_content' => $responseContent,
            ]);
            return;
        }

        $found = $data['response']['GeoObjectCollection']['metaDataProperty']['GeocoderResponseMetaData']['found'] ?? 0;
        if (!$found) {
            return;
        }

        $geoObject = $data['response']['GeoObjectCollection']['featureMember'][0]['GeoObject'] ?? [];
        $addressData = $geoObject['metaDataProperty']['GeocoderMetaData']['Address'] ?? [];

        if (!$addressData) {
            return;
        }

        $addressDto->postalCode = $addressData['postal_code'] ?? null;

        $addressComponents = $addressData['Components'] ?? [];
        foreach ($addressComponents as $addressComponent) {
            $kind = $addressComponent['kind'] ?? null;
            $value = $addressComponent['name'] ?? null;

            switch ($kind) {
                case 'country':
                    $addressDto->country = $value;
                    break;
                case 'province':
                    $addressDto->administrativeArea = $value;
                    break;
                case 'area':
                    $addressDto->subAdministrativeArea = $value;
                    break;
                case 'locality':
                    $addressDto->locality = $value;
                    break;
                case 'street':
                    $addressDto->thoroughfare = $value;
                    break;
                case 'house':
                    $addressDto->premise = $value;
                    break;
                default:
                    break;
            }
        }

        $pointPos = $geoObject['Point']['pos'] ?? null;
        if ($pointPos && \is_string($pointPos)) {
            $coordinates = explode(' ', $pointPos);
            if (\count($coordinates) === 2) {
                [$longitude, $latitude] = $coordinates; //Яндекс геокодер возвращает в формате 'долгота широта'
                $coordinatesPoint = new CoordinatesPoint($latitude, $longitude);
                $addressDto->coordinates = (string)$coordinatesPoint;
            }
        }
    }

    private function fillMetroDto(string $responseContent, MetroDto $metroDto): void
    {
        $data = json_decode($responseContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->logger->error('Json decode error: ' . json_last_error_msg(), [
                'response_content' => $responseContent,
            ]);
            return;
        }

        $found = $data['response']['GeoObjectCollection']['metaDataProperty']['GeocoderResponseMetaData']['found'] ?? 0;
        if (!$found) {
            return;
        }

        $geoObject = $data['response']['GeoObjectCollection']['featureMember'][0]['GeoObject'] ?? [];
        $addressData = $geoObject['metaDataProperty']['GeocoderMetaData']['Address'] ?? [];

        if (!$addressData) {
            return;
        }

        $addressComponents = $addressData['Components'] ?? [];
        foreach ($addressComponents as $addressComponent) {
            $kind = $addressComponent['kind'] ?? null;
            $value = $addressComponent['name'] ?? null;

            switch ($kind) {
                case 'country':
                    $metroDto->country = $value;
                    break;
                case 'province':
                    $metroDto->province = $value;
                    break;
                case 'locality':
                    $metroDto->locality = $value;
                    break;
                case 'route':
                    $metroDto->route = $value;
                    break;
                case 'metro':
                    $metroDto->metro = $value;
                    break;
                default:
                    break;
            }
        }
    }
}
