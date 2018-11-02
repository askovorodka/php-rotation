<?php

namespace AppBundle\Action\Admin;

use AppBundle\Entity\DealOffer;
use AppBundle\Entity\Hotel;
use Doctrine\Common\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Связываение акций и отелей
 *
 * @package AppBundle\Action
 */
class ConnectHotelAction
{
    /**
     * @var ManagerRegistry
     */
    private $managerRegistry;

    /**
     * UploadAction constructor.
     *
     * @param ManagerRegistry $managerRegistry
     */
    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    /**
     * @Route(
     *     name="connect_hotel",
     *     path="/api/v3/deal-offers/{dealOfferId}/connect-hotel/{hotelId}",
     *     requirements={
     *         "dealOfferId"= "\d+",
     *         "hotelId"= "\d+",
     *     },
     *     defaults={"_api_collection_operation_name"="connect_hotel", "_api_resource_class"=DealOffer::class}
     * )
     *
     * @Method({"POST"})
     */
    public function __invoke(Request $request, $hotelId, $dealOfferId)
    {
        $em = $this->managerRegistry->getManager();

        $dealOfferRepository = $em->getRepository(DealOffer::class);
        $hotelRepository = $em->getRepository(Hotel::class);

        if (!($dealOffer = $dealOfferRepository->find(['id' => $dealOfferId]))) {
            throw new NotFoundHttpException("deal-offer with `id`={$dealOfferId} not found");
        }

        if (!($hotel = $hotelRepository->find(['id' => $hotelId]))) {
            throw new NotFoundHttpException("hotel with `id`={$hotelId} not found");
        }

        // Привязанные акции делаем неактивными
        $connectedDealOffers = $dealOfferRepository->findBy(['hotel' => $hotel]);
        /** @var DealOffer $connectedDealOffer */
        foreach ($connectedDealOffers as $connectedDealOffer) {
            $connectedDealOffer->setIsActive(false);
            $em->persist($connectedDealOffer);
        }

        // Привязываем указанную акцию
        $dealOffer->setHotel($hotel);
        $dealOffer->setIsActive(true);

        $em->flush();

        return $dealOffer;
    }
}
