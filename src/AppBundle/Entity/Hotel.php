<?php

namespace AppBundle\Entity;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use AppBundle\Annotation\LoggableTableTitleAnnotation;
use AppBundle\Validator\Hotel\HotelIsProduction;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use AppBundle\Annotation\IsActiveAnnotation;
use AppBundle\Annotation\IsProductionAnnotation;

/**
 * @ORM\Table(name="hotel", indexes={
 *     @ORM\Index(name="is_active", columns={"is_active"})
 * })
 * @ORM\Entity(repositoryClass="AppBundle\Repository\HotelRepository")
 *
 * @UniqueEntity(fields={"sysname", "address"})
 * @IsActiveAnnotation(fieldName="is_active")
 * @IsProductionAnnotation(fieldName="is_production")
 * @ApiResource(
 *     attributes={
 *         "normalization_context"={"groups"={
 *             "hotel_GET",
 *             "hotel.category", "hotel-category_GET",
 *          }},
 *         "denormalization_context"={"groups"={
 *             "hotel_SAVE",
 *             "hotel.category",
 *             "hotel.contacts",
 *          }},
 *          "pagination_items_per_page"=10
 *     },
 *     itemOperations={
 *         "get"={"method"="GET", "access_control"="is_granted('ROLE_USER')"},
 *         "put"={"method"="PUT", "access_control"="is_granted('ROLE_MANAGER')"},
 *         "get_hotel_view"={
 *              "method"="GET",
 *              "path"="/hotel-views/{id}",
 *              "normalization_context"={"groups"={
 *                  "hotel-public-fields",
 *                  "hotel.category", "hotel-category_GET",
 *                  "hotel.contacts", "contact_GET", "contact-category_GET", "contact-type_GET",
 *                  "hotel.hotelAmenities", "hotel-amenity_GET", "amenity_GET",
 *                  "hotel.rooms",  "room_GET",
 *                                  "room.roomAmenities", "room-amenity_GET", "amenity_GET",
 *                                  "room.dealOfferRoomPrices",
 *                                  "room.roomPhotos", "room-photo_GET", "photo_GET",
 *                  "hotel.hotelPhotos", "hotel-photo_GET", "photo_GET",
 *                  "hotel.activeDealOffer",
 *                  "hotel-view_GET",
 *              }},
 *          },
 *          "get_hotel_deal_offers_prices"={
 *              "method"="GET",
 *              "path"="/hotel-deal-offers-prices/{id}",
 *              "normalization_context"={
 *                  "groups"={
 *                      "hotel.dealOffers",
 *                      "hotel.dealOffersPrices",
 *                      "hotel.dealOffersPricesRooms"
 *                  }
 *              },
 *          },
 *         "get_hotel_view_mobile"={
 *              "method"="GET",
 *              "path"="/hotel-views-mobile/{id}",
 *              "normalization_context"={"groups"={
 *                  "hotel-public-fields",
 *                  "hotel.hotelAmenities", "hotel-amenity_GET", "amenity_GET",
 *                  "hotel-view_GET",
 *              }},
 *          },
 *     },
 *     collectionOperations={
 *         "get"={"method"="GET", "access_control"="is_granted('ROLE_USER')"},
 *          "hotels_catalog_mobile.search"={
 *              "method"="GET",
 *              "path"="/mobile/hotels-catalog/search",
 *              "normalization_context"={
 *                  "groups"={
 *                  "hotel-public-fields",
 *                  "hotel.category", "hotel-category_GET",
 *                  "hotel.hotelPhotos", "hotel-photo_GET", "photo_GET",
 *                  "hotel.activeDealOffer",
 *                  },
 *              "_pagination_items_per_page"=8,
 *              "_pagination_client_items_per_page"=true,
 *                  "swagger_context"={
 *                      "parameters"={
 *                          {
 *                          "in" = "query",
 *                          "description" = "Территориальное расположение отеля",
 *                          "name" = "area",
 *                          "required" = false,
 *                          "type" = "string",
 *                          "example" = "Московская область",
 *                          },
 *                          {
 *                          "in" = "query",
 *                          "description" = "Идентификаторы категорий отелей (через запятую)",
 *                          "name" = "hotel_categories",
 *                          "required" = false,
 *                          "type" = "string",
 *                          "example" = "1,2",
 *                          },
 *                      {
 *                          "in" = "query",
 *                          "description" = "Идентификаторы удобств отелей (через запятую)",
 *                          "name" = "hotel_amenities",
 *                          "required" = false,
 *                          "type" = "string",
 *                          "example" = "53,55",
 *                      },
 *                      {
 *                          "in" = "query",
 *                          "description" = "Идентификаторы удобств в номерах (через запятую)",
 *                          "name" = "hotel_amenities",
 *                          "required" = false,
 *                          "type" = "string",
 *                          "example" = "15, 23, 21",
 *                      },
 *                      {
 *                          "in" = "query",
 *                          "description" = "Цена от",
 *                          "name" = "price_gte",
 *                          "required" = false,
 *                          "type" = "integer",
 *                          "example" = "5000",
 *                      },
 *                          {
 *                          "in" = "query",
 *                          "description" = "Цена до",
 *                          "name" = "price_lte",
 *                          "required" = false,
 *                          "type" = "integer",
 *                          "example" = "20000",
 *                          },
 *
 *                      }
 *                  }
 *              }
 *          },
 *         "hotels_catalog.search"={
 *              "method"="GET",
 *              "path"="/hotels-catalog/search",
 *              "normalization_context"={"groups"={
 *                  "hotel-public-fields",
 *                  "hotel.category", "hotel-category_GET",
 *                  "hotel.hotelAmenities", "hotel-amenity_GET", "amenity_GET",
 *                  "hotel.hotelPhotos", "hotel-photo_GET", "photo_GET",
 *                  "hotel.activeDealOfferData",
 *              }},
 *              "pagination_items_per_page"=8,
 *              "pagination_client_items_per_page"=true,
 *              "swagger_context" = {
 *                  "parameters" = {
 *                      {
 *                          "in" = "query",
 *                          "description" = "Территориальное расположение отеля",
 *                          "name" = "area",
 *                          "required" = false,
 *                          "type" = "string",
 *                          "example" = "Московская область",
 *                      },
 *                      {
 *                          "in" = "query",
 *                          "description" = "Идентификаторы категорий отелей (через запятую)",
 *                          "name" = "hotel_categories",
 *                          "required" = false,
 *                          "type" = "string",
 *                          "example" = "1,2",
 *                      },
 *                      {
 *                          "in" = "query",
 *                          "description" = "Идентификаторы удобств отелей (через запятую)",
 *                          "name" = "hotel_amenities",
 *                          "required" = false,
 *                          "type" = "string",
 *                          "example" = "53,55",
 *                      },
 *                      {
 *                          "in" = "query",
 *                          "description" = "Идентификаторы удобств в номерах (через запятую)",
 *                          "name" = "hotel_amenities",
 *                          "required" = false,
 *                          "type" = "string",
 *                          "example" = "15, 23, 21",
 *                      },
 *                      {
 *                          "in" = "query",
 *                          "description" = "Цена от",
 *                          "name" = "price_gte",
 *                          "required" = false,
 *                          "type" = "integer",
 *                          "example" = "5000",
 *                      },
 *                      {
 *                          "in" = "query",
 *                          "description" = "Цена до",
 *                          "name" = "price_lte",
 *                          "required" = false,
 *                          "type" = "integer",
 *                          "example" = "20000",
 *                      },
 *                  }
 *              }
 *          },
 *         "hotels_catalog.filters_data"={
 *              "method"="GET",
 *              "route_name"="hotels_catalog.get_filters_data",
 *              "pagination_enabled"=false,
 *              "pagination_client_items_per_page"=false,
 *          },
 *         "hotels_catalog.hotels_counters"={
 *              "method"="GET",
 *              "route_name"="hotels_catalog.get_hotels_counters",
 *              "pagination_enabled"=false,
 *              "pagination_client_items_per_page"=false,
 *              "swagger_context" = {
 *                  "parameters" = {
 *                      {
 *                          "in" = "query",
 *                          "description" = "Территориальное расположение отеля",
 *                          "name" = "area",
 *                          "required" = false,
 *                          "type" = "string",
 *                          "example" = "Московская область",
 *                      },
 *                      {
 *                          "in" = "query",
 *                          "description" = "Идентификаторы категорий отелей (через запятую)",
 *                          "name" = "hotel_categories",
 *                          "required" = false,
 *                          "type" = "string",
 *                          "example" = "1,2",
 *                      },
 *                      {
 *                          "in" = "query",
 *                          "description" = "Идентификаторы удобств отелей (через запятую)",
 *                          "name" = "hotel_amenities",
 *                          "required" = false,
 *                          "type" = "string",
 *                          "example" = "53,55",
 *                      },
 *                      {
 *                          "in" = "query",
 *                          "description" = "Идентификаторы удобств в номерах (через запятую)",
 *                          "name" = "hotel_amenities",
 *                          "required" = false,
 *                          "type" = "string",
 *                          "example" = "15, 23, 21",
 *                      },
 *                      {
 *                          "in" = "query",
 *                          "description" = "Цена от",
 *                          "name" = "price_gte",
 *                          "required" = false,
 *                          "type" = "integer",
 *                          "example" = "5000",
 *                      },
 *                      {
 *                          "in" = "query",
 *                          "description" = "Цена до",
 *                          "name" = "price_lte",
 *                          "required" = false,
 *                          "type" = "integer",
 *                          "example" = "20000",
 *                      },
 *                  }
 *              }
 *          },
 *         "post"={"method"="POST", "access_control"="is_granted('ROLE_MANAGER')"},
 *         "get_hotels_without_rooms"={
 *              "method"="GET",
 *              "pagination_client_enabled"=true,
 *              "pagination_client_items_per_page"=true,
 *              "access_control"="is_granted('ROLE_MANAGER')",
 *              "path"="/hotels-without-rooms",
 *          },
 *         "get_hotels_without_amenities"={
 *              "method"="GET",
 *              "pagination_client_enabled"=true,
 *              "pagination_client_items_per_page"=true,
 *              "access_control"="is_granted('ROLE_MANAGER')",
 *              "path"="/hotels-without-amenities",
 *          },
 *         "get_hotels_without_photos"={
 *              "method"="GET",
 *              "pagination_client_enabled"=true,
 *              "pagination_client_items_per_page"=true,
 *              "access_control"="is_granted('ROLE_MANAGER')",
 *              "path"="/hotels-without-photos",
 *          },
 *          "address_geocode"={"route_name"="hotels_address_geocode", "access_control"="is_granted('ROLE_MANAGER')"},
 *     },
 * )
 *
 * @ApiFilter(OrderFilter::class, properties={"minOriginPrice","dealOffers.createdAt", "dealOffers.isActive"}, arguments={"orderParameterName"="order"})
 * @ApiFilter(SearchFilter::class, properties={"title"="partial"})
 *
 * @Gedmo\Loggable(logEntryClass="AppBundle\Entity\LogEntry")
 * @LoggableTableTitleAnnotation(title="Отели")
 */
class Hotel
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Groups({
     *     "hotel_GET",
     *     "connect_hotel_normalization_context",
     *     "get_rooms_for_connect_deal_offer_price",
     *     "hotel-public-fields",
     *     "hotel.dealOffers",
     *     "admin-quotas-list",
     *     "active_deal_offers_with_hotel",
     *     "federal-hotel-pin_GET",
     *     "local-hotel-pin_GET",
     * })
     */
    protected $id;

    /**
     * @ORM\Column(name="sysname", type="string", length=255, unique=true)
     *
     * @Groups({
     *     "hotel_GET",
     *     "hotel_SAVE",
     *     "preset-hotel_GET",
     *     "hotel-public-fields",
     *     "active_deal_offers_with_hotel",
     *     "federal-hotel-pin_GET",
     *     "local-hotel-pin_GET",
     * })
     *
     * @Gedmo\Versioned
     *
     * @Assert\NotBlank()
     */
    protected $sysname;

    /**
     * @ORM\Column(name="title", type="string", length=255)
     *
     * @Groups({
     *     "hotel_GET",
     *     "hotel_SAVE",
     *     "preset-hotel_GET",
     *     "get_rooms_for_connect_deal_offer_price",
     *     "hotel-public-fields",
     *     "hotel.dealOffers",
     *     "admin-quotas-list",
     *     "active_deal_offers_with_hotel",
     *     "federal-hotel-pin_GET",
     *     "local-hotel-pin_GET",
     * })
     *
     * @Gedmo\Versioned
     *
     * @Assert\NotBlank()
     */
    protected $title;

    /**
     * @ORM\Column(name="description", type="text", nullable=true)
     *
     * @Groups({
     *     "hotel_GET",
     *     "hotel_SAVE",
     *     "preset-hotel_GET",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $description;

    /**
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     *
     * @Groups({
     *     "hotel_GET",
     *     "hotel_SAVE",
     *     "preset-hotel_GET",
     *     "hotel-public-fields",
     *     "admin-quotas-list",
     * })
     *
     * @Gedmo\Versioned
     *
     * @Assert\NotBlank()
     */
    protected $address;

    /**
     * @ORM\Column(name="country", type="string", length=255, nullable=true)
     *
     * @Groups({
     *     "hotel_GET",
     *     "hotel_SAVE",
     *     "preset-hotel_GET",
     * })
     *
     * @Gedmo\Versioned
     *
     * @Assert\NotBlank()
     */
    protected $country;

    /**
     * @ORM\Column(name="administrative_area", type="string", length=255, nullable=true)
     *
     * @Groups({
     *     "hotel_GET",
     *     "hotel_SAVE",
     *     "preset-hotel_GET",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $administrativeArea;

    /**
     * @ORM\Column(name="sub_administrative_area", type="string", length=255, nullable=true)
     *
     * @Groups({
     *     "hotel_GET",
     *     "hotel_SAVE",
     *     "preset-hotel_GET",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $subAdministrativeArea;

    /**
     * @ORM\Column(name="locality", type="string", length=255, nullable=true)
     *
     * @Groups({
     *     "hotel_GET",
     *     "hotel_SAVE",
     *     "preset-hotel_GET",
     *     "hotel-public-fields",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $locality;

    /**
     * @ORM\Column(name="thoroughfare", type="string", length=255, nullable=true)
     *
     * @Groups({
     *     "hotel_GET",
     *     "hotel_SAVE",
     *     "preset-hotel_GET",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $thoroughfare;

    /**
     * @ORM\Column(name="premise", type="string", length=255, nullable=true)
     *
     * @Groups({
     *     "hotel_GET",
     *     "hotel_SAVE",
     *     "preset-hotel_GET",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $premise;

    /**
     * @ORM\Column(name="postal_code", type="string", length=255, nullable=true)
     *
     * @Groups({
     *     "hotel_GET",
     *     "hotel_SAVE",
     *     "preset-hotel_GET",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $postalCode;

    /**
     * @ORM\Column(name="rating", type="float", length=11, nullable=true)
     *
     * @Groups({
     *     "hotel_GET",
     *     "hotel_SAVE",
     *     "preset-hotel_GET",
     *     "hotel-public-fields",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $rating;

    /**
     * @ORM\Column(name="city_region_text", type="string", length=255, nullable=true)
     *
     * @Gedmo\Versioned
     */
    protected $cityRegionText;

    /**
     * @ORM\Column(name="alt_title", type="string", length=255, nullable=true)
     *
     * @Gedmo\Versioned
     */
    protected $altTitle;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     *
     * @Groups({
     *     "hotel_GET",
     *     "hotel_SAVE",
     *     "preset-hotel_GET",
     *     "hotel-public-fields",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $isActive;

    /**
     * @ORM\Column(name="is_production", type="boolean", nullable=true)
     *
     * @HotelIsProduction
     *
     * @Groups({
     *     "hotel_GET",
     *     "hotel_SAVE",
     *     "preset-hotel_GET",
     *     "hotel-public-fields",
     *     "active_deal_offers_with_hotel",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $isProduction;

    /**
     * @ORM\Column(name="meta_description", type="text")
     *
     * @Groups({
     *     "hotel_GET",
     *     "hotel_SAVE",
     *     "preset-hotel_GET",
     *     "hotel-public-fields",
     * })
     *
     * @Gedmo\Versioned
     *
     * @Assert\NotBlank()
     */
    protected $metaDescription;

    /**
     * @ORM\Column(type="float", nullable=true)
     *
     * @Groups({
     *     "hotel_GET",
     *     "preset-hotel_GET",
     *     "hotel-public-fields",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $minOriginalPrice;

    /**
     * @ORM\Column(name="discount", type="integer", length=11, nullable=true)
     *
     * @Gedmo\Versioned
     *
     * @Groups({
     *     "hotel_GET",
     *     "preset-hotel_GET",
     *     "hotel-public-fields",
     * })
     */
    protected $discount;

    /**
     * @Groups({
     *     "hotel-public-fields",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $minPrice;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\OneToMany(targetEntity="Contact", mappedBy="hotel", cascade={"persist"})
     *
     * @ApiSubresource
     *
     * @Groups({
     *     "hotel.contacts",
     * })
     *
     * @Assert\Valid()
     */
    protected $contacts;

    /**
     * @ORM\OneToMany(targetEntity="Room", mappedBy="hotel")
     *
     * @ApiSubresource
     *
     * @Groups({
     *     "hotel.rooms",
     * })
     */
    protected $rooms;

    /**
     * @ORM\OneToMany(targetEntity="DealOffer", mappedBy="hotel")
     *
     * @Groups({
     *     "hotel_GET",
     *     "hotel.dealOffers",
     * })
     */
    protected $dealOffers;

    /**
     * @var DealOffer|false|null
     *
     * @Groups({
     *     "hotel.activeDealOffer",
     * })
     */
    protected $activeDealOffer = false;

    /**
     * @ORM\OneToMany(targetEntity="HotelAmenity", mappedBy="hotel")
     *
     * @ApiSubresource
     *
     * @Groups({
     *     "hotel.hotelAmenities",
     * })
     */
    protected $hotelAmenities;

    /**
     * @ORM\OneToMany(targetEntity="HotelPhoto", mappedBy="hotel")
     *
     * @ApiSubresource
     *
     * @Groups({
     *     "hotel.hotelPhotos",
     * })
     *
     * @ORM\OrderBy({"listOrder" = "ASC"})
     */
    protected $hotelPhotos;

    /**
     * @ORM\OneToMany(targetEntity="HotelAdditionalRule", mappedBy="hotel")
     */
    protected $hotelAdditionalRules;

    /**
     * @ORM\OneToMany(targetEntity="PresetHotel", mappedBy="hotel")
     */
    protected $presets;

    /**
     * @ORM\ManyToOne(targetEntity="HotelCategory", inversedBy="hotels")
     * @ORM\JoinColumn(name="hotel_category_id", referencedColumnName="id", nullable=true)
     *
     * @Groups({
     *     "hotel.category",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $hotelCategory;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text")
     *
     * @Groups({
     *     "hotel_GET",
     *     "hotel_SAVE",
     *     "active_deal_offers_with_hotel",
     * })
     *
     * @Gedmo\Versioned
     */
    protected $comment;

    /**
     * @var int|null
     *
     * @ORM\Column(name="purchases_count", type="integer", nullable=true)
     *
     * @Groups({
     *     "hotel_GET",
     *     "hotel-public-fields",
     * })
     */
    protected $purchasesCount;

    /**
     * @ORM\Column(name="latitude", type="decimal", precision=11, scale=8, options={"comment": "Широта"}, nullable=true)
     * @Groups({
     *     "hotel_SAVE",
     *     "hotel_GET",
     *     })
     */
    protected $latitude;

    /**
     * @ORM\Column(name="longitude", type="decimal", precision=11, scale=8, options={"comment": "Долгота"}, nullable=true)
     * @Groups({
     *     "hotel_SAVE",
     *     "hotel_GET",
     * })
     */
    protected $longitude;


    /**
     * @ORM\Column(name="country_code", type="string", length=3, options={"comment": "Код страны"}, nullable=true)
     * @Groups({
     *     "hotel_SAVE",
     *     "hotel_GET",
     * })
     */
    protected $countryCode;

    /**
     * @ORM\Column(name="city_id", type="integer", options={"comment": "ID города в нашем геосервисе"}, nullable=true)
     * @Groups({
     *     "hotel_SAVE",
     *     "hotel_GET",
     * })
     */
    protected $cityId;

    /**
     * @ORM\Column(name="crm_city_id", type="integer", options={"comment": "ID города в CRM"}, nullable=true)
     * @Groups({
     *     "hotel_SAVE",
     *     "hotel_GET",
     * })
     */
    protected $crmCityId;

    /**
     * @ORM\Column(name="crm_city_name", type="string", options={"comment": "Название города в CRM"}, nullable=true)
     * @Groups({
     *     "hotel_SAVE",
     *     "hotel_GET",
     * })
     */
    protected $crmCityName;

    /**
     * @var array $photosUrls
     * @Groups({"hotel.hotelPhotos"})
     */
    private $photosUrls = [];


    /**
     * @var int $regionId
     * @ORM\Column(name="region_id", type="integer")
     * @Gedmo\Versioned
     */
    protected $regionId;

    /**
     * Hotel constructor.
     */
    function __construct()
    {
        $this->createdAt = new \DateTime('now');
        $this->isActive = true;
        $this->isProduction = false;

        $this->contacts = new ArrayCollection();
        $this->rooms = new ArrayCollection();
        $this->dealOffers = new ArrayCollection();
        $this->hotelAmenities = new ArrayCollection();
        $this->hotelPhotos = new ArrayCollection();
        $this->hotelAdditionalRules = new ArrayCollection();
        $this->presets = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set sysname
     *
     * @param string $sysname
     *
     * @return Hotel
     */
    public function setSysname($sysname)
    {
        $this->sysname = $sysname;

        return $this;
    }

    /**
     * Get sysname
     *
     * @return string
     */
    public function getSysname()
    {
        return $this->sysname;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Hotel
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Hotel
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return Hotel
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set country
     *
     * @param string $country
     *
     * @return Hotel
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set administrativeArea
     *
     * @param string $administrativeArea
     *
     * @return Hotel
     */
    public function setAdministrativeArea($administrativeArea)
    {
        $this->administrativeArea = $administrativeArea;

        return $this;
    }

    /**
     * Get administrativeArea
     *
     * @return string
     */
    public function getAdministrativeArea()
    {
        return $this->administrativeArea;
    }

    /**
     * Set subAdministrativeArea
     *
     * @param string $subAdministrativeArea
     *
     * @return Hotel
     */
    public function setSubAdministrativeArea($subAdministrativeArea)
    {
        $this->subAdministrativeArea = $subAdministrativeArea;

        return $this;
    }

    /**
     * Get subAdministrativeArea
     *
     * @return string
     */
    public function getSubAdministrativeArea()
    {
        return $this->subAdministrativeArea;
    }

    /**
     * Set locality
     *
     * @param string $locality
     *
     * @return Hotel
     */
    public function setLocality($locality)
    {
        $this->locality = $locality;

        return $this;
    }

    /**
     * Get locality
     *
     * @return string
     */
    public function getLocality()
    {
        return $this->locality;
    }

    /**
     * Set thoroughfare
     *
     * @param string $thoroughfare
     *
     * @return Hotel
     */
    public function setThoroughfare($thoroughfare)
    {
        $this->thoroughfare = $thoroughfare;

        return $this;
    }

    /**
     * Get thoroughfare
     *
     * @return string
     */
    public function getThoroughfare()
    {
        return $this->thoroughfare;
    }

    /**
     * Set premise
     *
     * @param string $premise
     *
     * @return Hotel
     */
    public function setPremise($premise)
    {
        $this->premise = $premise;

        return $this;
    }

    /**
     * Get premise
     *
     * @return string
     */
    public function getPremise()
    {
        return $this->premise;
    }

    /**
     * Set postalCode
     *
     * @param string $postalCode
     *
     * @return Hotel
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * Get postalCode
     *
     * @return string
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * Set rating
     *
     * @param float $rating
     *
     * @return Hotel
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating
     *
     * @return float
     */
    public function getRating()
    {
        return round((float)$this->rating, 1);
    }

    /**
     * Set cityRegionText
     *
     * @param string $cityRegionText
     *
     * @return Hotel
     */
    public function setCityRegionText($cityRegionText)
    {
        $this->cityRegionText = $cityRegionText;

        return $this;
    }

    /**
     * Get cityRegionText
     *
     * @return string
     */
    public function getCityRegionText()
    {
        return $this->cityRegionText;
    }

    /**
     * Set altTitle
     *
     * @param string $altTitle
     *
     * @return Hotel
     */
    public function setAltTitle($altTitle)
    {
        $this->altTitle = $altTitle;

        return $this;
    }

    /**
     * Get altTitle
     *
     * @return string
     */
    public function getAltTitle()
    {
        return $this->altTitle;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return Hotel
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set isProduction
     *
     * @param boolean $isProduction
     *
     * @return Hotel
     */
    public function setIsProduction($isProduction)
    {
        $this->isProduction = $isProduction;

        return $this;
    }

    /**
     * Get isProduction
     *
     * @return boolean
     */
    public function getIsProduction()
    {
        return $this->isProduction;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Hotel
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set metaDescription
     *
     * @param string $metaDescription
     *
     * @return Hotel
     */
    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    /**
     * Get metaDescription
     *
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * Add contact
     *
     * @param \AppBundle\Entity\Contact $contact
     *
     * @return Hotel
     */
    public function addContact(\AppBundle\Entity\Contact $contact)
    {
        $this->contacts->add($contact);
        $contact->setHotel($this);

        return $this;
    }

    /**
     * Remove contact
     *
     * @param \AppBundle\Entity\Contact $contact
     */
    public function removeContact(\AppBundle\Entity\Contact $contact)
    {
        $this->contacts->removeElement($contact);
    }

    /**
     * Get contacts
     *
     * @return Contact[]\Doctrine\Common\Collections\Collection
     */
    public function getContacts()
    {
        return $this->contacts;
    }

    /**
     * Add room
     *
     * @param \AppBundle\Entity\Room $room
     *
     * @return Hotel
     */
    public function addRoom(\AppBundle\Entity\Room $room)
    {
        $this->rooms->add($room);
        $room->setHotel($this);

        return $this;
    }

    /**
     * Remove room
     *
     * @param \AppBundle\Entity\Room $room
     */
    public function removeRoom(\AppBundle\Entity\Room $room)
    {
        $this->rooms->removeElement($room);
    }

    /**
     * Get rooms
     *
     * @return Room[]|\Doctrine\Common\Collections\Collection
     */
    public function getRooms()
    {
        return $this->rooms;
    }

    /**
     * Add dealOffer
     *
     * @param \AppBundle\Entity\DealOffer $dealOffer
     *
     * @return Hotel
     */
    public function addDealOffer(\AppBundle\Entity\DealOffer $dealOffer)
    {
        $this->dealOffers->add($dealOffer);
        $dealOffer->setHotel($this);
        return $this;
    }

    /**
     * Remove dealOffer
     *
     * @param \AppBundle\Entity\DealOffer $dealOffer
     */
    public function removeDealOffer(\AppBundle\Entity\DealOffer $dealOffer)
    {
        $this->dealOffers->removeElement($dealOffer);
    }

    /**
     * Get dealOffers
     *
     * @return DealOffer[]|\Doctrine\Common\Collections\Collection
     */
    public function getDealOffers()
    {
        return $this->dealOffers;
    }

    /**
     * Add hotelAmenity
     *
     * @param \AppBundle\Entity\HotelAmenity $hotelAmenity
     *
     * @return Hotel
     */
    public function addHotelAmenity(\AppBundle\Entity\HotelAmenity $hotelAmenity)
    {
        $this->hotelAmenities->add($hotelAmenity);
        $hotelAmenity->setHotel($this);

        return $this;
    }

    /**
     * Remove hotelAmenity
     *
     * @param \AppBundle\Entity\HotelAmenity $hotelAmenity
     */
    public function removeHotelAmenity(\AppBundle\Entity\HotelAmenity $hotelAmenity)
    {
        $this->hotelAmenities->removeElement($hotelAmenity);
    }

    /**
     * @param HotelAmenity[]|ArrayCollection $amenities
     * @return void
     */
    public function setHotelAmenities($amenities): void
    {
        $this->hotelAmenities = $amenities;
    }

    /**
     * @param Room[]|ArrayCollection $rooms
     * @return void
     */
    public function setRooms($rooms): void
    {
        $this->rooms = $rooms;
    }

    /**
     * @param mixed $dealOffers
     * @return void
     */
    public function setDealOffers($dealOffers): void
    {
        if (is_array($dealOffers)) {
            $this->dealOffers = new ArrayCollection($dealOffers);
        } else {
            $this->dealOffers = $dealOffers;
        }
    }

    /**
     * Get hotelAmenities
     *
     * @return HotelAmenity[]|\Doctrine\Common\Collections\Collection
     */
    public function getHotelAmenities()
    {
        return $this->hotelAmenities;
    }

    /**
     * Add hotelPhoto
     *
     * @param \AppBundle\Entity\HotelPhoto $hotelPhoto
     *
     * @return Hotel
     */
    public function addHotelPhoto(\AppBundle\Entity\HotelPhoto $hotelPhoto)
    {
        $this->hotelPhotos->add($hotelPhoto);
        $hotelPhoto->setHotel($this);

        return $this;
    }

    /**
     * Remove hotelPhoto
     *
     * @param \AppBundle\Entity\HotelPhoto $hotelPhoto
     */
    public function removeHotelPhoto(\AppBundle\Entity\HotelPhoto $hotelPhoto)
    {
        $this->hotelPhotos->removeElement($hotelPhoto);
    }

    /**
     * Get hotelPhotos
     *
     * @return HotelPhoto[]\Doctrine\Common\Collections\Collection
     */
    public function getHotelPhotos()
    {
        return $this->hotelPhotos;
    }

    /**
     * Add hotelAdditionalRule
     *
     * @param \AppBundle\Entity\HotelAdditionalRule $hotelAdditionalRule
     *
     * @return Hotel
     */
    public function addHotelAdditionalRule(\AppBundle\Entity\HotelAdditionalRule $hotelAdditionalRule)
    {
        $this->hotelAdditionalRules->add($hotelAdditionalRule);
        $hotelAdditionalRule->setHotel($this);

        return $this;
    }

    /**
     * Remove hotelAdditionalRule
     *
     * @param \AppBundle\Entity\HotelAdditionalRule $hotelAdditionalRule
     */
    public function removeHotelAdditionalRule(\AppBundle\Entity\HotelAdditionalRule $hotelAdditionalRule)
    {
        $this->hotelAdditionalRules->removeElement($hotelAdditionalRule);
    }

    /**
     * Get hotelAdditionalRules
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHotelAdditionalRules()
    {
        return $this->hotelAdditionalRules;
    }

    /**
     * Add preset
     *
     * @param \AppBundle\Entity\PresetHotel $preset
     *
     * @return Hotel
     */
    public function addPreset(\AppBundle\Entity\PresetHotel $preset)
    {
        $this->presets->add($preset);
        $preset->setHotel($this);

        return $this;
    }

    /**
     * Remove preset
     *
     * @param \AppBundle\Entity\PresetHotel $preset
     */
    public function removePreset(\AppBundle\Entity\PresetHotel $preset)
    {
        $this->presets->removeElement($preset);
    }

    /**
     * Get presets
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPresets()
    {
        return $this->presets;
    }

    /**
     * Set hotelCategory
     *
     * @param \AppBundle\Entity\HotelCategory $hotelCategory
     *
     * @return Hotel
     */
    public function setHotelCategory(\AppBundle\Entity\HotelCategory $hotelCategory)
    {
        $this->hotelCategory = $hotelCategory;

        return $this;
    }

    /**
     * Get hotelCategory
     *
     * @return \AppBundle\Entity\HotelCategory
     */
    public function getHotelCategory()
    {
        return $this->hotelCategory;
    }

    /**
     * Возвращает связанную активную акцию
     *
     * @todo BTRV-254 Унести (+ переосмыслить) поиск активной акции в более подходящее место
     *
     * @return null|DealOffer
     */
    public function getActiveDealOffer()
    {
        if ($this->activeDealOffer === false) {
            $this->activeDealOffer = null;
            foreach ($this->getDealOffers() as $dealOffer) {
                if ($dealOffer->getIsActive()) {
                    $this->activeDealOffer = $dealOffer;
                    break;
                }
            }
        }

        return $this->activeDealOffer;
    }

    /**
     * @return mixed
     */
    public function getMinOriginalPrice()
    {
        return $this->minOriginalPrice;
    }

    /**
     * @param mixed $minOriginalPrice
     */
    public function setMinOriginalPrice($minOriginalPrice): void
    {
        $this->minOriginalPrice = $minOriginalPrice;
    }

    /**
     * @return mixed
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * @param mixed $discount
     */
    public function setDiscount($discount): void
    {
        $this->discount = $discount;
    }

    /**
     * @param mixed $minPrice
     */
    public function setMinPrice($minPrice): void
    {
        $this->minPrice = $minPrice;
    }

    /**
     * @return mixed
     */
    public function getMinPrice()
    {
        return $this->minPrice ?: (
        $this->minOriginalPrice
            ? (ceil($this->minOriginalPrice * (1 - $this->discount / 100)))
            : null
        );
    }

    /**
     * @param string $url
     */
    public function addPhotoUrl(string $url): void {
        $this->photosUrls[] = $url;
    }

    /**
     * @return array
     */
    public function getPhotosUrls(): ?array {
        return $this->photosUrls;
    }

    /**
     * @Groups({"hotel.activeDealOfferData"})
     * @return array|null
     */
    public function getActiveDealOfferData(): ?array
    {
        if ($dealOffer = $this->getActiveDealOffer()) {
            return [
                'id' => $dealOffer->getId(),
                'titleLink' => $dealOffer->getTitleLink(),
            ];
        }

        return null;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment($comment): void
    {
        $this->comment = $comment;
    }

    /**
     * @return int|null
     */
    public function getPurchasesCount(): ?int
    {
        return $this->purchasesCount;
    }

    /**
     * @param int|null $purchasesCount
     * @return Hotel
     */
    public function setPurchasesCount(?int $purchasesCount): Hotel
    {
        $this->purchasesCount = $purchasesCount;
        return $this;
    }

    /**
     * Set latitude.
     *
     * @param string|null $latitude
     *
     * @return Hotel
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude.
     *
     * @return string|null
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude.
     *
     * @param string|null $longitude
     *
     * @return Hotel
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude.
     *
     * @return string|null
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set countryCode.
     *
     * @param string|null $countryCode
     *
     * @return Hotel
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    /**
     * Get countryCode.
     *
     * @return string|null
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * Set cityId.
     *
     * @param int|null $cityId
     *
     * @return Hotel
     */
    public function setCityId($cityId)
    {
        $this->cityId = $cityId;

        return $this;
    }

    /**
     * Get cityId.
     *
     * @return int|null
     */
    public function getCityId()
    {
        return $this->cityId;
    }

    /**
     * Set crmCityId.
     *
     * @param int|null $crmCityId
     *
     * @return Hotel
     */
    public function setCrmCityId($crmCityId)
    {
        $this->crmCityId = $crmCityId;

        return $this;
    }

    /**
     * Get crmCityId.
     *
     * @return int|null
     */
    public function getCrmCityId()
    {
        return $this->crmCityId;
    }

    /**
     * Set crmCityName.
     *
     * @param string|null $crmCityName
     *
     * @return Hotel
     */
    public function setCrmCityName($crmCityName)
    {
        $this->crmCityName = $crmCityName;

        return $this;
    }

    /**
     * Get crmCityName.
     *
     * @return string|null
     */
    public function getCrmCityName()
    {
        return $this->crmCityName;
    }


    /**
     * Set regionId.
     *
     * @param int $regionId
     *
     * @return Hotel
     */
    public function setRegionId($regionId)
    {
        $this->regionId = $regionId;

        return $this;
    }

    /**
     * Get regionId.
     *
     * @return int
     */
    public function getRegionId()
    {
        return $this->regionId;
    }
}
