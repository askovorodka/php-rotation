<?php

namespace AppBundle\Command;

use AppBundle\Services\Geocoder\GeocoderInterface;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class FillHotelsGeodata
 *
 * @package AppBundle\Command
 */
class FillHotelsGeodata extends ContainerAwareCommand
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
     * FillHotelsGeodata constructor.
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

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('fill-hotels-geodata')
            ->setDescription('Команда заполняет геоданные отеля по строке адреса используя сервис геокодинга')
            ->addOption(
                'limit',
                null,
                InputOption::VALUE_REQUIRED,
                'Максимальное кол-во обновляемых записей'
            )
            ->addOption(
                'where-null',
                null,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Список null полей, по которым отбираются записи для обновления 
                            (Если одно из указанных полей null, то будет произведена попытка обновления записи)',
                [
                    'latitude',
                    'longitude'
                ]
            );
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $limitRaw = $input->getOption('limit');
        if ($limitRaw !== null && !ctype_digit($limitRaw)) {
            throw new \InvalidArgumentException('limit must be type int');
        }
        $limit = (int)$limitRaw;

        $nullFields = $input->getOption('where-null');
        if (!$nullFields) {
            throw new \InvalidArgumentException('where-null option must not be empty');
        }

        $selectQuery = $this->conn->createQueryBuilder();
        $selectQuery->select('id , address')
            ->from('hotel')
            ->orderBy('id');

        foreach ($nullFields as $nullField) {
            $selectQuery->orWhere($selectQuery->expr()->isNull($nullField));
        }

        if ($limit) {
            $selectQuery->setMaxResults($limit);
        }

        $data = $selectQuery->execute()->fetchAll();
        $rowsCnt = \count($data);

        $progressBar = new ProgressBar($output);
        $progressBar->setOverwrite(true);
        $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%%  %elapsed:6s%/%estimated:-6s%   %message%');
        $progressBar->setMessage('0 updated');
        $progressBar->start($rowsCnt);

        $updateQuery = $this->conn->createQueryBuilder();
        $updateQuery->update('hotel')
            ->where('id = :hotelId')
            ->set('latitude', ':latitude')
            ->set('longitude', ':longitude')
            ->set('country_code', ':countryCode')
            ->set('city_id', ':cityId')
            ->set('crm_city_id', ':crmCityId')
            ->set('crm_city_name', ':crmCityName');

        $updatedRowsCnt = 0;

        foreach ($data as $row) {
            $hotelId = $row['id'];
            $hotelAddress = $row['address'];

            $addressDto = $this->geocoder->findAddress($hotelAddress);

            if (!$addressDto->latitude || !$addressDto->longitude) {
                $progressBar->clear();
                $output->writeln("Coordinates for $hotelAddress not found");
                $progressBar->advance();
                $progressBar->display();
                continue;
            }

            $parameters = [
                'hotelId' => $hotelId,
                'latitude' => $addressDto->latitude,
                'longitude' => $addressDto->longitude,
                'countryCode' => $addressDto->countryCode,
                'cityId' => $addressDto->cityId,
                'crmCityId' => $addressDto->crmCityId,
                'crmCityName' => $addressDto->crmCityName,
            ];

            $updateQuery->setParameters($parameters);
            $isUpdated = $updateQuery->execute();

            if ($isUpdated) {
                ++$updatedRowsCnt;
                $progressBar->setMessage("$updatedRowsCnt updated");
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $output->writeln('');
    }
}
