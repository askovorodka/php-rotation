<?php

namespace AppBundle\Command;

use AppBundle\Entity\Purchase;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CountPricePurchasesCommand extends ContainerAwareCommand
{
    /**
     * @var Connection
     */
    private $conn;

    /**
     * CountPurchasesCommand constructor.
     *
     * @param Connection $conn
     */
    public function __construct(Connection $conn)
    {
        parent::__construct();

        $this->conn = $conn;
    }

    protected function configure()
    {
        $this
            ->setName('count-price-purchases')
            ->setDescription('Подсчитывает количество попкупок по каждой цене и сохраняет результат в поле purchases_count в таблице deal_offer_price');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $time = -microtime(true);

        $affectedRowsCnt = $this->conn->executeUpdate('
            update deal_offer_price dop,
                (select p.deal_offer_price_id, sum(p.quantity) as purchases_count
                     from purchase p
                     where p.status = :purchaseStatus
                     group by p.deal_offer_price_id) src
                set dop.purchases_count = src.purchases_count
                where dop.id = src.deal_offer_price_id
        ', [
            'purchaseStatus' => Purchase::STATUS_PAID
        ]);

        $time += microtime(true);

        $output->writeln("$affectedRowsCnt rows updated | Spent time: $time sec");
    }

}
