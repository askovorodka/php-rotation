<?php

namespace AppBundle\Action;

use AppBundle\Services\Rotation\Dto\PresetBuilderDtoAssembler;
use AppBundle\Services\Rotation\Dto\RotationDtoAssembler;
use AppBundle\Services\Rotation\PresetBuilder;
use AppBundle\Services\Rotation\RotationService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class GetCatalogAction
{

    /** @var RotationService  */
    private $rotationService;

    public function __construct(RotationService $rotationService)
    {
        $this->rotationService = $rotationService;
    }

    /**
     * @Route(
     *     name="get_catalog",
     *     path="/api/v3/catalog",
     * )
     *
     * @Method({"GET"})
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function __invoke(Request $request)
    {
        $rotationDtoAssembler = new RotationDtoAssembler($request);

        return new JsonResponse($this->rotationService->getPreset($rotationDtoAssembler->assemble()));
    }
}
