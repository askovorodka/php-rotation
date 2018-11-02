<?php

namespace AppBundle\Command;

use AppBundle\Services\Geocoder\GeocoderInterface;
use AppBundle\Services\Geocoder\ValueObjects\CoordinatesPoint;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FillHotelsMetroCommand extends ContainerAwareCommand
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
            ->setName('fill-hotels-metro')
            ->setDescription('Команда заполняет пустые значения метро для отелей, у которых указаны координаты')
            ->addOption(
                'areas',
                null,
                InputOption::VALUE_REQUIRED,
                'Список значений поля administrative_area'
            )->addOption(
                'only-empty',
                null,
                InputOption::VALUE_NONE,
                'Обновить только пустые значения'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $query = $this->conn->createQueryBuilder();
        $query->select('id , coordinates')
            ->from('hotel')
            ->where('coordinates is not null');

        if ($areasStr = $input->getOption('areas')) {
            $areas = explode(',', $areasStr);
            $query->andWhere('administrative_area in (:areas)')
                ->setParameter('areas', $areas, Connection::PARAM_STR_ARRAY);
        }

        if ($input->getOption('only-empty')) {
            $query->andWhere('metro is null or metro = \'\'');
        }

        $result = $query->execute()->fetchAll();
        $rowsCnt = \count($result);

        $updateQuery = $this->conn->createQueryBuilder();
        $updateQuery->update('hotel')
            ->set('metro', ':metro')
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
            $hotelCoordinates = $hotelData['coordinates'];

            try {
                [$latitude, $longitude] = explode(',', $hotelCoordinates);
                $coordinatesPoint = new CoordinatesPoint($latitude, $longitude);
            } catch (\Exception $e) {
                $progressBar->clear();
                $output->writeln("Сould not get coordinates for $hotelCoordinates");
                $progressBar->advance();
                $progressBar->display();
                continue;
            }

            $metroDto = $this->geocoder->findNearestMetro($coordinatesPoint);
            if (!$metroDto->metro) {
                $progressBar->clear();
                $output->writeln("Metro for $hotelCoordinates not found");
                $progressBar->advance();
                $progressBar->display();
                continue;
            }

            $metro = preg_replace('/метро\s+/ui', '', $metroDto->metro);

            $parameters = [
                'hotelId' => $hotelId,
                'metro' => $metro,
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
