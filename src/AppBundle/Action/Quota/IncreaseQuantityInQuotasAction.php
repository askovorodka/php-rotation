<?php

namespace AppBundle\Action\Quota;

use AppBundle\Entity\Quota;
use AppBundle\Entity\Room;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Services\Quota\QuotasEditorServiceInterface;
use AppBundle\Services\Quota\ValueObjects\DayType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class IncreaseQuantityInQuotasAction
 *
 * @package AppBundle\Action\Quota
 */
final class IncreaseQuantityInQuotasAction
{
    /**
     * @var QuotasEditorServiceInterface
     */
    private $quotasEditor;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * IncreaseQuantityInQuotasAction constructor.
     *
     * @param EntityManagerInterface       $em
     * @param QuotasEditorServiceInterface $quotasEditor
     */
    public function __construct(
        EntityManagerInterface $em,
        QuotasEditorServiceInterface $quotasEditor
    ) {
        $this->em = $em;
        $this->quotasEditor = $quotasEditor;
    }

    /**
     * @Route(
     *     name="increase_quantity",
     *     path="/api/v3/quotas/increase",
     *     defaults={"_api_collection_operation_name"="increase_quantity", "_api_resource_class"=Quota::class},
     * )
     *
     * @Method({"POST"})
     *
     * @param Request $request
     * @return Quota[]|array
     * @throws \Exception
     */
    public function __invoke(Request $request)
    {
        $requestParams = json_decode($request->getContent(), true);

        $roomId = isset($requestParams['roomId']) ? (int)$requestParams['roomId'] : null;
        $delta = isset($requestParams['delta']) ? (int)$requestParams['delta'] : null;
        $beginDateRaw = $requestParams['beginDate'] ?? null;
        $endDateRaw = $requestParams['endDate'] ?? null;
        $dayTypeRaw = $requestParams['dayType'] ?? null;

        $beginDate = new \DateTimeImmutable($beginDateRaw);
        $endDate = new \DateTimeImmutable($endDateRaw);
        $dayType = $dayTypeRaw ? new DayType($dayTypeRaw) : null;

        $today = (new \DateTimeImmutable())->setTime(0, 0, 0);

        $roomRepository = $this->em->getRepository(Room::class);
        $room = $roomRepository->find($roomId);

        if ($beginDate < $today) {
            throw new InvalidArgumentException('beginDate must be equal or greater then today');
        }

        if (!$room) {
            throw new InvalidArgumentException('Room not found');
        }

        $this->em->beginTransaction();
        try {
            $quotasRepository = $this->em->getRepository(Quota::class);
            $existingQuotas = $quotasRepository->getQuotasForRoomByInterval($room, $beginDate, $endDate, true);

            $newQuotas = $this->quotasEditor->createMissedQuotasByInterval($room, $beginDate, $endDate, $existingQuotas);

            $quotas = array_merge($existingQuotas, $newQuotas);

            $affectedQuotas = $this->quotasEditor->increaseQuantityInQuotas($quotas, $delta, $dayType);

            foreach ($affectedQuotas as $affectedQuota) {
                $this->em->persist($affectedQuota);
            }
            $this->em->flush();
            $this->em->commit();

        } catch (\Exception $e) {
            $this->em->rollback();
            throw $e;
        }

        return $affectedQuotas;
    }
}
