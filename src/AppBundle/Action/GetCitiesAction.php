<?php

namespace AppBundle\Action;

use AppBundle\DtoProvider\CityDtoProvider;
use AppBundle\Entity\CityRegion;
use AppBundle\Repository\CityRegionRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class GetCitiesAction
 *
 * @package AppBundle\Action
 */
class GetCitiesAction
{
    /**
     * @var CityDtoProvider
     */
    private $cityDtoProvider;

    /**
     * GetCitiesAction constructor.
     *
     * @param CityDtoProvider $cityDtoProvider
     */
    public function __construct(CityDtoProvider $cityDtoProvider)
    {
        $this->cityDtoProvider = $cityDtoProvider;
    }

    /**
     * @Route(
     *     name="cities",
     *     path="/api/v3/cities",
     * )
     *
     * @Method({"GET"})
     *
     * @return JsonResponse
     */
    public function __invoke()
    {
        $cities = $this->cityDtoProvider->findAll();

        return new JsonResponse($cities);
    }

}
