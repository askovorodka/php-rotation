<?php

namespace AppBundle\Command;

use AppBundle\Services\Geocoder\GeocoderInterface;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FillHotelsCoordinatesCommand extends ContainerAwareCommand
{
    /**
     * @var Connection
     */
    private $conn;

    /**
     * @var GeocoderInterface
     */
    private $geocoder;

    /**
     * FillHotelCoordinatesCommand constructor.
     *
     * @param Connection        $conn
     * @param GeocoderInterface $geocoder
     */
    public function __construct(Connection $conn, GeocoderInterface $geocoder)
    {
        parent::__construct();
        $this->conn = $conn;
        $this->geocoder = $geocoder;
    }

    protected function configure()
    {
        $this
            ->setName('fill-hotels-coordinates')
            ->setDescription('Команда заполняет пустые значения ширины и долготы отелей')
            ->addOption(
                'only-empty',
                null,
                InputOption::VALUE_NONE,
                'Обновить только пустые значения'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $query = $this->conn->createQueryBuilder();
        $query->select('id , address')
            ->from('hotel')
            ->where('address is not null');

        if ($input->getOption('only-empty')) {
            $query->andWhere('coordinates is null or coordinates = \'\'');
        }

        $result = $query->execute()->fetchAll();
        $rowsCnt = \count($result);

        $updateQuery = $this->conn->createQueryBuilder();
        $updateQuery->update('hotel')
            ->set('coordinates', ':coordinates')
            ->where('id = :hotelId');

        $updatedRowsCnt = 0;
        $notUpdatedRowsCnt = 0;

        $progressBar = new ProgressBar($output);
        $progressBar->setOverwrite(true);
        $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%%  %elapsed:6s%/%estimated:-6s%   %message%');
        $progressBar->setMessage('0 updated');
        $progressBar->start($rowsCnt);

        foreach ($result as $hotelData) {
            $hotelId = $hotelData['id'];
            $hotelAddress = $hotelData['address'];

            $addressDto = $this->geocoder->findAddress($hotelAddress);

            if (!$addressDto->coordinates) {
                $progressBar->clear();
                $output->writeln("Coordinates for $hotelAddress not found");
                $progressBar->advance();
                $progressBar->display();
                continue;
            }

            $parameters = [
                'hotelId' => $hotelId,
                'coordinates' => $addressDto->coordinates,
            ];

            $updateQuery->setParameters($parameters);
            $isUpdated = $updateQuery->execute();

            if ($isUpdated) {
                ++$updatedRowsCnt;
                $progressBar->setMessage("$updatedRowsCnt updated");
            } else {
                ++$notUpdatedRowsCnt;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $output->writeln('');
    }
}
