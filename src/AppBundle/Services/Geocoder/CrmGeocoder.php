<?php

namespace AppBundle\Services\Geocoder;

use AppBundle\Services\Geocoder\Dto\AddressDto;
use AppBundle\Services\Geocoder\Dto\MetroDto;
use AppBundle\Services\Geocoder\ValueObjects\CoordinatesPoint;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;

/**
 * Class CrmGeocoder
 *
 * @package AppBundle\Services\Geocoder
 */
class CrmGeocoder implements GeocoderInterface
{
    private const CRM_GEOCODER_HOST = 'https://geocoder.biglion.app';

    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * CrmGeocoder constructor.
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
            $requestUrl = self::CRM_GEOCODER_HOST . '/decode';
            $response = $this->httpClient->request('GET', $requestUrl, [
                'query' => [
                    'q' => $searchStr,
                ],
            ]);
        } catch (GuzzleException $e) {
            $this->logger->error($e->getMessage(), [$requestUrl, $searchStr]);
            return $addressDto;
        }

        $responseContent = $response->getBody()->getContents();

        if (($statusCode = $response->getStatusCode()) !== 200) {
            $this->logger->error("Crm geocoder response code: $statusCode", [
                'request_address' => $searchStr,
                'response_content' => $responseContent,
            ]);
            return $addressDto;
        }

        $this->fillAddressDto($responseContent, $addressDto);

        if ($cityId = $addressDto->cityId) {
            $crmCityInfo = $this->findCrmCityInfo($cityId);

            $addressDto->crmCityId = $this->getIdValue($crmCityInfo, 'CrmCityId');
            $addressDto->crmCityName = $this->getStringValue($crmCityInfo, 'Name');
        }

        return $addressDto;
    }

    /**
     * @inheritdoc
     */
    public function findNearestMetro(CoordinatesPoint $point): MetroDto
    {
        return new MetroDto();
    }

    /**
     * @param string     $responseContent
     * @param AddressDto $addressDto
     */
    private function fillAddressDto(string $responseContent, AddressDto $addressDto): void
    {
        $data = json_decode($responseContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->logger->error('Json decode error: ' . json_last_error_msg(), [
                'response_content' => $responseContent,
            ]);
            return;
        }

        $addressDto->cityId = $this->getIdValue($data, 'CityID');

        $geoObject = $data['GeocoderLocations'][0] ?? [];
        if (!$geoObject) {
            return;
        }

        $addressDto->country = $geoObject['Country'] ?? null;
        $addressDto->administrativeArea = $geoObject['AdminLevels']['1']['Name'] ?? null;
        $addressDto->subAdministrativeArea = $geoObject['AdminLevels']['2']['Name'] ?? null;
        $addressDto->locality = $geoObject['Locality'] ?? null;
        $addressDto->thoroughfare = $geoObject['StreetName'] ?? null;
        $addressDto->premise = $geoObject['StreetNumber'] ?? null;
        $addressDto->countryCode = $geoObject['CountryCode'] ?? null;
        $addressDto->latitude = isset($geoObject['Latitude']) ? (string)$geoObject['Latitude'] : null;
        $addressDto->longitude = isset($geoObject['Longitude']) ? (string)$geoObject['Longitude'] : null;
    }

    /**
     * @param int $cityId
     *
     * @return array
     */
    private function findCrmCityInfo(int $cityId): array
    {
        try {
            $requestUrl = self::CRM_GEOCODER_HOST . "/crmCity/$cityId";
            $response = $this->httpClient->request('GET', $requestUrl);
        } catch (GuzzleException $e) {
            $this->logger->error($e->getMessage(), [$requestUrl]);
            return [];
        }

        $responseContent = $response->getBody()->getContents();
        $data = json_decode($responseContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->logger->error('Json decode error: ' . json_last_error_msg(), [
                'response_content' => $responseContent,
            ]);
            return [];
        }

        return $data;
    }

    /**
     * @param array $data
     * @param       $key
     * @return int|null
     */
    private function getIdValue(array $data, $key): ?int
    {
        $value = null;

        if (isset($data[$key]) && $data[$key]) {
            $value = (int)$data[$key];
        }

        return $value;
    }

    /**
     * @param array $data
     * @param       $key
     * @return null|string
     */
    private function getStringValue(array $data, $key): ?string
    {
        $value = null;

        if (isset($data[$key]) && $data[$key]) {
            $value = (string)$data[$key];
        }

        return $value;
    }
}
