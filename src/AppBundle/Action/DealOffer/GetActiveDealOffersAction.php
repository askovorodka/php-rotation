<?php

namespace AppBundle\Action\DealOffer;

use AppBundle\Entity\DealOffer;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class GetActiveDealOffersAction
 * @package AppBundle\Action\DealOffer
 */
final class GetActiveDealOffersAction
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var LoggerInterface $logger
     */
    private $logger;

    /**
     * GetActiveDealOffersAction constructor.
     * @param EntityManager $entityManager
     * @param LoggerInterface $logger
     */
    public function __construct(EntityManager $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    /**
     * @return array|mixed|string
     * @Method({"GET"})
     * @Route(
     *     name="get_active_deal_offers",
     *     path="/api/v3/active-deal-offers/"
     * )
     */
    public function __invoke()
    {
        try {
            $entityRepository = $this->entityManager->getRepository(DealOffer::class);
            return new JsonResponse($entityRepository->getDealOfferIdsWithHotels());
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
            throw new NotFoundHttpException();
        }
    }
}
