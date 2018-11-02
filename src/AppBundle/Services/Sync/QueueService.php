<?php

namespace AppBundle\Services\Sync;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Сервис для работы с очередями синхронизации
 *
 * @package AppBundle\Services\Sync
 */
class QueueService
{
    const QUEUE_EXCHANGE = 'biglion.sync.hotel';
    const QUEUE_CONSUMER_TAG = 'consumer';
    const QUEUE_HOTEL_DOS = 'hotelDOS';
    const QUEUE_HOTEL_DOPS = 'hotelDOPS';
    const QUEUE_HOTEL_DOS_MANUAL = 'hotelDOSManual';
    public const QUEUE_HOTEL_PURCHASES = 'hotelPurchases';
    public const QUEUE_HOTEL_PURCHASES_MANUAL = 'hotelPurchasesManual';

    /** @var ContainerInterface */
    private $container;

    /** @var \PhpAmqpLib\Channel\AMQPChannel */
    private $channel;

    /**
     * QueueService constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->connection = new AMQPStreamConnection(
            $this->container->getParameter('amqp_host'),
            $this->container->getParameter('amqp_port'),
            $this->container->getParameter('amqp_user'),
            $this->container->getParameter('amqp_pass')
        );
        $this->channel = $this->connection->channel();
    }

    /**
     * Становимся консумером для переданной очереди
     *
     * @param $queue
     * @return void
     */
    public function bindQueue($queue)
    {
        $this->channel->queue_declare(
            $queue,
            false,
            true,
            false,
            false
        );
        $this->channel->queue_bind(
            $queue,
            self::QUEUE_EXCHANGE,
            $queue
        );
        $this->channel->basic_consume(
            $queue,
            self::QUEUE_CONSUMER_TAG,
            false,
            false,
            false,
            false,
            function (AMQPMessage $message) {
                $this->processMessage($message, 0);
            }
        );

        $this->loop();
    }

    /**
     * @param AMQPMessage $message
     * @param int $count
     * @return void
     */
    protected function processMessage(AMQPMessage $message, $count = 10)
    {
        if ($count > 10) {
            $this->ack($message);
            return;
        }
        try {
            /** @var SyncService $syncService */
            $syncService = $this->container->get('app.service.sync');
            $data = json_decode($message->body, true);

            $routingKey = $message->delivery_info['routing_key'];
            switch ($routingKey) {
                case self::QUEUE_HOTEL_DOS:
                case self::QUEUE_HOTEL_DOS_MANUAL:
                    $syncService->processDOS($data);
                    break;
                case self::QUEUE_HOTEL_DOPS:
                    $syncService->processDOPS($data);
                    break;
                case self::QUEUE_HOTEL_PURCHASES:
                case self::QUEUE_HOTEL_PURCHASES_MANUAL:
                    $syncService->processPurchases($data);
                    break;
            }
            $this->ack($message);
        } catch (\Exception $e) {
            $this->processMessage($message, $count + 1);
        }
    }

    /**
     * Посылаем сигнал прочтения и принятия в кролика
     *
     * @param \PhpAmqpLib\Message\AMQPMessage $message
     * @return void
     */
    public function ack(AMQPMessage $message)
    {
        $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
    }

    /**
     * Впадаем в прослушку очереди
     *
     * @return void
     */
    private function loop()
    {
        while (count($this->channel->callbacks)) {
            $this->channel->wait();
        }
    }
}
