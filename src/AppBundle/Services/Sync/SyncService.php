<?php

namespace AppBundle\Services\Sync;

use AppBundle\Dto\Yandex\AddressDto;
use AppBundle\Entity\DealOffer;
use AppBundle\Entity\DealOfferPartnerManager;
use AppBundle\Entity\DealOfferPrice;
use AppBundle\Entity\Purchase;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Сервис обработки сообщений из очередей синхронизации
 *
 * @package AppBundle\Services\Sync
 */
class SyncService
{
    /** @var EntityManager | null */
    private $entityManager;

    /**
     * SyncService constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param $dos
     * @return void
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     */
    public function processDOS($dos)
    {
        $this->getEm()->clear();

        foreach ($dos as $doData) {
            if ($this->processDealOfferData($doData)) {
                if (isset($doData['prices']) && is_array($doData['prices']) && !empty($doData['prices'])) {
                    $this->processDOPS($doData['prices']);
                }
            }
        }
    }

    /**
     * @param $dops
     * @return void
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     */
    public function processDOPS($dops)
    {
        $this->getEm()->clear();

        foreach ($dops as $dopData) {
            $this->processDealOfferPriceData($dopData);
        }
    }

    /**
     * Получаем EntityManager и в случае, если коннекшн протух - обновляем соединение
     *
     * @return EntityManager|null|object
     * @throws \Doctrine\ORM\ORMException
     */
    private function getEm()
    {
        if (!$this->entityManager->isOpen()) {
            $this->entityManager = $this->entityManager->create(
                $this->entityManager->getConnection(),
                $this->entityManager->getConfiguration()
            );
        }

        return $this->entityManager;
    }

    /**
     * Обработка DealOffer
     *
     * Для того, чтобы неизмененная сущность считалась неизмененной,
     * не выполнялся лишний апдейт и не добавлялась запись в лог об изменении сущности
     * в реализации следим за тайп хинтом скалярных значений
     * и не вызываем сеттеры сущности для эквивалентных объектов
     * (например для эквивалентных объектов DateTime)
     *
     * @param array $data
     * @return DealOffer|null|object
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     */
    private function processDealOfferData($data)
    {
        /** @var DealOffer $dealOffer */
        $dealOffer = $this->getEm()->getRepository(DealOffer::class)->find($data['deal_offer_id']);
        if (!$dealOffer) {
            $metadata = $this->getEm()->getClassMetadata(DealOffer::class);
            $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
            $metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());

            $dealOffer = new DealOffer();
            $dealOffer->setId($data['deal_offer_id']);
        }

        if (array_key_exists('address', $data) && $data['address']) {
            $dealOffer->setAddress($data['address']);
        }

        $dealOffer->setDurationDays((int)$data['duration_d']);
        $dealOffer->setIsActive((bool)$data['is_active']);
        $dealOffer->setTitleLink($data['title_link']);
        $dealOffer->setTitle($data['title']);

        $startDate = new \DateTime($data['start_date']);
        if ($dealOffer->getStartAt() != $startDate) {
            $dealOffer->setStartAt($startDate);
        }

        if ($dealOffer->getStartCouponAt() != $startDate) {
            $dealOffer->setStartCouponAt($startDate);
        }

        $validDate = new \DateTime($data['valid_date']);
        if ($dealOffer->getValidAt() != $validDate) {
            $dealOffer->setValidAt($validDate);
        }

        $endDate = new \DateTime($data['end_date']);
        if ($dealOffer->getEndAt() != $endDate) {
            $dealOffer->setEndAt($endDate);
        }

        $partnerId = isset($data['partner_id']) ? (int)$data['partner_id'] : null;
        $dealOffer->setPartnerId($partnerId);

        $managerInfo = [
            'managerId' => isset($data['manager_id']) ? (int)$data['manager_id'] : null,
            'name' => $data['manager_name'] ?? null,
            'surname' => $data['manager_surname'] ?? null,
            'patronymic' => $data['manager_patronymic'] ?? null,
            'email' => $data['manager_email'] ?? null,
        ];
        $dealOffer->setManagerInfo($managerInfo);

        $this->getEm()->persist($dealOffer);
        $this->getEm()->flush();

        $managersEmailsStr = $data['managers_emails'];
        $managersEmails = [];
        if ($managersEmailsStr) {
            $managersEmails = explode(',', $managersEmailsStr);
        }

        $this->processManagersEmails($dealOffer, $managersEmails);

        return $dealOffer;
    }

    /**
     * Актуализирует email'ы менеджеров для DealOffer
     *
     * @param DealOffer $dealOffer
     * @param string[] $emails
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     */
    private function processManagersEmails(DealOffer $dealOffer, array $emails)
    {
        $dealOfferPartnerManagers = $dealOffer->getDealOfferPartnerManagers();

        $existingEmails = [];
        if (!empty($dealOfferPartnerManagers)) {
            $existingEmails = array_map(function (DealOfferPartnerManager $dealOfferPartnerManager) {
                return $dealOfferPartnerManager->getManagerEmail();
            }, $dealOfferPartnerManagers->toArray());
        }

        $removedEmails = array_diff($existingEmails, $emails);
        $addedEmails = array_diff($emails, $existingEmails);

        foreach ($dealOfferPartnerManagers as $dealOfferPartnerManager) {
            $email = $dealOfferPartnerManager->getManagerEmail();
            if (\in_array($email, $removedEmails, true)) {
                $this->getEm()->remove($dealOfferPartnerManager);
            }
        }

        foreach ($addedEmails as $addedEmail) {
            $dealOfferPartnerManager = new DealOfferPartnerManager();
            $dealOfferPartnerManager->setDealOffer($dealOffer);
            $dealOfferPartnerManager->setManagerEmail($addedEmail);
            $this->getEm()->persist($dealOfferPartnerManager);
        }

        $this->getEm()->flush();
    }

    /**
     * Обработка DealOfferPrice
     *
     * Для того, чтобы неизмененная сущность считалась неизмененной,
     * не выполнялся лишний апдейт и не добавлялась запись в лог об изменении сущности
     * в реализации следим за тайп хинтом скалярных значений
     * и не вызываем сеттеры для эквивалентных объектов
     * (например для эквивалентных объектов DateTime)
     *
     * @param array $data
     * @return DealOfferPrice|null|object
     *
     * @throws \Doctrine\ORM\ORMException
     */
    private function processDealOfferPriceData($data)
    {
        /** @var DealOfferPrice $dealOfferPrice */
        if (!$dealOfferPrice = $this->getEm()->getRepository(DealOfferPrice::class)->find($data['deal_offer_price_id'])) {
            $metadata = $this->getEm()->getClassMetaData(DealOfferPrice::class);
            $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
            $metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());

            /** @var DealOffer $dealOffer */
            if (!$dealOffer = $this->getEm()->getRepository(DealOffer::class)->find($data['deal_offer_id'])) {
                return null;
            }

            $dealOfferPrice = new DealOfferPrice();
            $dealOfferPrice->setId($data['deal_offer_price_id']);
            $dealOfferPrice->setDealOffer($dealOffer);
        }

        $dealOfferPrice->setDiscount((int)$data['discount']);
        $dealOfferPrice->setOriginalPrice((float)$data['original_price']);
        $dealOfferPrice->setTitle($data['title']);

        $groupPriceValidDateCandidate = $data['group_price_valid_date'] ?
            \DateTime::createFromFormat('YmdHis', $data['group_price_valid_date']) : null;
        $groupPriceValidDate = $this->calculateDealOfferGroupPriceValidDate($dealOfferPrice, $groupPriceValidDateCandidate);

        if ($dealOfferPrice->getGroupPriceValidDate() != $groupPriceValidDate) {
            $dealOfferPrice->setGroupPriceValidDate($groupPriceValidDate);
        }

        $validDateCandidate = $data['valid_date'] ? \DateTime::createFromFormat('YmdHis', $data['valid_date']) : null;
        $dealOfferPriceValidDate = $this->calculateDealOfferPriceValidDate($dealOfferPrice, $validDateCandidate);

        if ($dealOfferPrice->getValidDate() != $dealOfferPriceValidDate) {
            $dealOfferPrice->setValidDate($dealOfferPriceValidDate);
        }

        if(array_key_exists('max_coupone', $data)) {
            $dealOfferPrice->setMaxCoupone($data['max_coupone']);
        }

        $this->getEm()->persist($dealOfferPrice);
        $this->getEm()->flush();

        return $dealOfferPrice;
    }

    /**
     * @param $purchases
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\ORM\ORMException
     */
    public function processPurchases($purchases)
    {
        $this->getEm()->clear();

        foreach ($purchases as $purchase) {
            $this->processPurchaseData($purchase);
        }
    }

    /**
     * @param $data
     * @return Purchase|null
     * @throws \Doctrine\ORM\ORMException
     */
    private function processPurchaseData($data)
    {
        $purchaseId = $data['purchase_id'] ?? null;
        if (!$purchaseId) {
            return null;
        }

        $dealOfferId = isset($data['deal_offer_id']) ? (int)$data['deal_offer_id'] : null;
        $dealOfferPriceId = isset($data['deal_offer_price_id']) ? (int)$data['deal_offer_price_id'] : null;
        $clClientId = isset($data['cl_client_id']) ? (int)$data['cl_client_id'] : null;
        $quantity = isset($data['quantity']) ? (int)$data['quantity'] : null;
        $quantityReturn = isset($data['quantity_return']) ? (int)$data['quantity_return'] : null;

        $purchaseDate = null;
        $purchaseDateRaw = $data['purchase_date'] ?? null;
        if ($purchaseDateRaw) {
            $purchaseDate = \DateTime::createFromFormat('YmdHis', $purchaseDateRaw);
        }

        $status = $data['status'] ?? null;
        $totalPrice = $data['total_price'];

        /** @var EntityManagerInterface $em */
        $em = $this->getEm();

        $purchase = $em->getRepository(Purchase::class)->find($purchaseId);
        if (!$purchase) {
            $metadata = $em->getClassMetadata(Purchase::class);
            $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
            $metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());

            $purchase = new Purchase();
            $purchase->setId($purchaseId);
        }

        $oldDealOfferId = $purchase->getDealOffer() ? $purchase->getDealOffer()->getId() : null;
        if ($oldDealOfferId != $dealOfferId) {
            /** @var DealOffer $dealOfferRef */
            $dealOfferRef = $em->getReference(DealOffer::class, $dealOfferId);
            $purchase->setDealOffer($dealOfferRef);
        }

        $oldDealOfferPriceId = $purchase->getDealOfferPrice() ? $purchase->getDealOfferPrice()->getId() : null;
        if ($oldDealOfferPriceId != $dealOfferPriceId) {
            /** @var DealOfferPrice $dealOfferPriceRef */
            $dealOfferPriceRef = $em->getReference(DealOfferPrice::class, $dealOfferPriceId);
            $purchase->setDealOfferPrice($dealOfferPriceRef);
        }

        if ($purchase->getPurchaseDate() != $purchaseDate) {
            $purchase->setPurchaseDate($purchaseDate);
        }

        $purchase->setClClientId($clClientId);
        $purchase->setStatus($status);
        $purchase->setTotalPrice($totalPrice);
        $purchase->setQuantity($quantity);
        $purchase->setQuantityReturn($quantityReturn);

        $em->persist($purchase);
        $em->flush();

        return $purchase;
    }

    /**
     * Вычисляет значение groupPriceValidDate для цены
     *
     * @param DealOfferPrice $dealOfferPrice
     * @param \DateTime|null $dateCandidate
     * @return \DateTime|null
     */
    private function calculateDealOfferGroupPriceValidDate(
        DealOfferPrice $dealOfferPrice,
        ?\DateTime $dateCandidate
    ): ?\DateTime {
        if ($dateCandidate) {
            return $dateCandidate;
        }

        $dealOfferValidDate = $dealOfferPrice->getDealOffer()->getValidAt();
        if ($dealOfferValidDate) {
            return clone $dealOfferValidDate;
        }

        return $dateCandidate;
    }

    /**
     * Вычисляет значение validDate для цены
     *
     * @param DealOfferPrice $dealOfferPrice
     * @param \DateTime|null $dateCandidate
     * @return \DateTime|null
     */
    private function calculateDealOfferPriceValidDate(
        DealOfferPrice $dealOfferPrice,
        ?\DateTime $dateCandidate
    ): ?\DateTime {
        if ($dateCandidate) {
            return $dateCandidate;
        }

        $groupPriceValidDate = $dealOfferPrice->getGroupPriceValidDate();
        if ($groupPriceValidDate) {
            return clone $groupPriceValidDate;
        }

        return $this->calculateDealOfferGroupPriceValidDate($dealOfferPrice, $dateCandidate);
    }

}
