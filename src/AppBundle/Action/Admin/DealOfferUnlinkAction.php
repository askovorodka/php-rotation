<?php

namespace AppBundle\Action\Admin;

use AppBundle\Entity\DealOffer;
use AppBundle\Entity\DealOfferPrice;
use AppBundle\Entity\DealOfferPriceRoom;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

final class DealOfferUnlinkAction
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
     * @param DealOffer $dealOffer
     * @throws \Exception
     * @Method({"DELETE"})
     * @Route(
     *     name="deal_offer_delete_action",
     *     path="/api/v3/deal-offer/{id}/delete",
     *     defaults={"_api_resource_class"=DealOffer::class}
     *     )
     */
    public function __invoke(DealOffer $dealOffer)
    {
        $this->em->getConnection()->beginTransaction();
        try {
            /** @var DealOfferPrice $dealOfferPrice */
            foreach ($dealOffer->getDealOfferPrices() as $dealOfferPrice) {
                /** @var DealOfferPriceRoom $dealOfferPriceRoom */
                foreach ($dealOfferPrice->getDealOfferRooms() as $dealOfferPriceRoom) {
                    $this->em->remove($dealOfferPriceRoom);
                }
            }
            $dealOffer->setHotel(null);
            $this->em->flush();
            $this->em->commit();
        } catch (\Exception $exception) {
            $this->em->rollback();
            $this->logger->error($exception->getMessage());
            throw new \Exception($exception->getMessage());
        }
    }

}
