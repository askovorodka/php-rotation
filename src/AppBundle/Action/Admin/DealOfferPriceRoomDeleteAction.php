<?php

namespace AppBundle\Action\Admin;

use AppBundle\Entity\DealOfferPrice;
use AppBundle\Entity\DealOfferPriceRoom;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

final class DealOfferPriceRoomDeleteAction
{
    /** @var EntityManager $em */
    private $em;

    /** @var LoggerInterface $logger */
    private $logger;

    public function __construct(EntityManager $entityManager, LoggerInterface $logger)
    {
        $this->em = $entityManager;
        $this->logger = $logger;
    }

    /**
     * @param DealOfferPrice $dealOfferPrice
     * @throws \Exception
     * @Method({"DELETE"})
     * @Route(
     *     name="deal_offer_price_delete_action",
     *     path="/api/v3/deal-offer-prices/{id}/delete",
     *     defaults={"_api_resource_class"=DealOfferPrice::class}
     *     )
     */
    public function __invoke(DealOfferPrice $dealOfferPrice)
    {
        $this->em->getConnection()->beginTransaction();
        try {
            /** @var DealOfferPriceRoom $dealOfferPriceRoom */
            foreach ($dealOfferPrice->getDealOfferRooms() as $dealOfferPriceRoom) {
                $this->em->remove($dealOfferPriceRoom);
            }
            $this->em->flush();
            $this->em->commit();
        } catch (\Exception $exception) {
            $this->em->rollback();
            $this->logger->error($exception->getMessage());
            throw new \Exception($exception->getMessage());
        }
    }
}
