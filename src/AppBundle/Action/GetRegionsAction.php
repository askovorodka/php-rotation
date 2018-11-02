<?php

namespace AppBundle\Action;

use AppBundle\DtoProvider\RegionDtoProvider;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class GetRegionsAction
 *
 * @package AppBundle\Action
 */
class GetRegionsAction
{
    /**
     * @var RegionDtoProvider
     */
    private $regionDtoProvider;

    /**
     * GetRegionsAction constructor.
     *
     * @param RegionDtoProvider $regionDtoProvider
     */
    public function __construct(RegionDtoProvider $regionDtoProvider)
    {
        $this->regionDtoProvider = $regionDtoProvider;
    }

    /**
     * @Route(
     *     name="regions",
     *     path="/api/v3/regions",
     * )
     *
     * @Method({"GET"})
     *
     * @return JsonResponse
     */
    public function __invoke()
    {
        $regions = $this->regionDtoProvider->findAll();

        return new JsonResponse($regions);
    }

}
