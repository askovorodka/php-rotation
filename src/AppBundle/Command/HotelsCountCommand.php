<?php

namespace AppBundle\Command;

use AppBundle\Services\HotelsList\HotelsListCollectionStorage\HotelsListCollectionStorageInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class HotelsCountCommand extends ContainerAwareCommand
{
    private $storage;

    public function __construct(HotelsListCollectionStorageInterface $storage)
    {
        parent::__construct();
        $this->storage = $storage;
    }

    protected function configure()
    {
        $this
            ->setName('hotels-count')
            ->addOption('ids', null, InputOption::VALUE_REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $hotelsIdsStr = $input->getOption('ids');

        $hotelsIdsFlipped = [];
        if ($hotelsIdsStr) {
            $hotelsIds = explode(',', $hotelsIdsStr);
            $hotelsIdsFlipped = array_flip($hotelsIds);
        }

        $time = -microtime(true);
        $listCollection = $this->storage->getListCollection();
        $time += microtime(true);

        $counters = $listCollection->getCounters($hotelsIdsFlipped);

        $output->write("deserialization time: {$time}s\n" . print_r($counters, true));
    }
}
