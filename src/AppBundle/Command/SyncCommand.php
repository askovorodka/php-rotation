<?php

namespace AppBundle\Command;

use AppBundle\Services\Sync\QueueService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Запуск приёма синхронизации из монолита
 *
 * @package AppBundle\Command
 */
class SyncCommand extends ContainerAwareCommand
{
    use LockableTrait;

    public const TYPE_DEAL_OFFER = 'deal_offer';
    public const TYPE_DEAL_OFFER_PRICE = 'deal_offer_price';
    public const TYPE_MANUAL = 'manual';
    public const TYPE_PURCHASE = 'purchase';
    public const TYPE_PURCHASE_MANUAL = 'purchase-manual';

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('sync')
            ->setDescription('Sync rabbitmq queues (DealOffer, DealOfferPrice, Purchase)')
            ->addOption('type', 't', InputOption::VALUE_REQUIRED,
                'type of sync (deal_offer|deal_offer_price|manual|purchase|purchase-manual) deal_offer_price - default', null);
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $type = $input->getOption('type');

        if (!$this->lock("sync_{$type}")) {
            $output->writeln('The command is already running in another process.');

            return 0;
        }

        /** @var QueueService $queue */
        $queue = $this->getContainer()->get('app.service.sync.queue');

        switch ($type) {
            case self::TYPE_DEAL_OFFER:
                $queue->bindQueue(QueueService::QUEUE_HOTEL_DOS);
                break;
            case self::TYPE_DEAL_OFFER_PRICE:
                $queue->bindQueue(QueueService::QUEUE_HOTEL_DOPS);
                break;
            case self::TYPE_MANUAL:
                $queue->bindQueue(QueueService::QUEUE_HOTEL_DOS_MANUAL);
                break;
            case self::TYPE_PURCHASE:
                $queue->bindQueue(QueueService::QUEUE_HOTEL_PURCHASES);
                break;
            case self::TYPE_PURCHASE_MANUAL:
                $queue->bindQueue(QueueService::QUEUE_HOTEL_PURCHASES_MANUAL);
                break;
            case null:
                $output->writeln("no type defined");
                break;
            default:
                $output->writeln("unknown type");
                break;
        }

        $this->release();

        return 1;
    }
}
