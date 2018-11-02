<?php

namespace AppBundle\Command;

use AppBundle\Entity\Purchase;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CountPurchasesCommand extends ContainerAwareCommand
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
            ->setName('count-purchases')
            ->setDescription('Подсчитывает количество попкупок по каждому отелю и сохраняет результат в поле purchases_count в таблице hotel');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $time = -microtime(true);

        $affectedRowsCnt = $this->conn->executeUpdate('
            update
                hotel h,
                (select do.hotel_id, sum(p.quantity) as purchases_count
                 from purchase p
                        inner join deal_offer do on p.deal_offer_id = do.id and do.hotel_id is not null
                 where p.status = :purchaseStatus
                 group by do.hotel_id) src
            set h.purchases_count = src.purchases_count
            where h.id = src.hotel_id
        ', [
            'purchaseStatus' => Purchase::STATUS_PAID
        ]);

        $time += microtime(true);

        $output->writeln("$affectedRowsCnt rows updated | Spent time: $time sec");
    }


}
