<?php

namespace AppBundle\Command;

use AppBundle\Entity\DealOffer;
use AppBundle\Entity\DealOfferPrice;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ClearUninformativeLogsCommand extends ContainerAwareCommand
{
    /**
     * @var Connection
     */
    private $conn;

    /**
     * FillHotelCoordinatesCommand constructor.
     *
     * @param Connection        $conn
     */
    public function __construct(Connection $conn)
    {
        parent::__construct();
        $this->conn = $conn;
    }

    protected function configure()
    {
        $this
            ->setName('clear-uninformative-logs')
            ->addOption(
                'limit-per-query',
                null,
                InputOption::VALUE_REQUIRED,
                'Максимальнео количество удаляемых записей в одном запросе',
                1000
            )
            ->setDescription('Удаляет кусками неинформативные логи по ценам и акциям');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $limitPerQuery = (int)$input->getOption('limit-per-query');

        $progressBar = new ProgressBar($output);
        $progressBar->setOverwrite(true);
        $progressBar->setFormat('%elapsed:6s% | %message%');
        $this->updateReportMessage($progressBar, 0);
        $progressBar->start();

        $rowsDeletedCnt = 0;

        do {
            $affectedRowsCnt = $this->conn->executeUpdate("
                delete from logs
                where user_id is null
                and action = 'update'
                and object_class in (:object_classes)
                limit $limitPerQuery
            ", [
                'object_classes' => [DealOffer::class, DealOfferPrice::class],
            ], [
                'object_classes' => Connection::PARAM_STR_ARRAY,
            ]);

            $rowsDeletedCnt += $affectedRowsCnt;

            $this->updateReportMessage($progressBar, $rowsDeletedCnt);
            $progressBar->advance();
            sleep(2);
        } while ($affectedRowsCnt);

        $progressBar->finish();
        $output->writeln('');
    }

    private function updateReportMessage(ProgressBar $progressBar, int $rowsDeletedCnt)
    {
        $progressBar->setMessage("$rowsDeletedCnt rows deleted");
    }

}
