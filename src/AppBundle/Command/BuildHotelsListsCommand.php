<?php

namespace AppBundle\Command;

use AppBundle\Services\HotelsList\HotelsListBuilder\HotelsListBuilderInterface;
use AppBundle\Services\HotelsList\HotelsListCollection;
use AppBundle\Services\HotelsList\HotelsListCollectionStorage\HotelsListCollectionStorageInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BuildHotelsListsCommand extends ContainerAwareCommand
{
    /**
     * @var HotelsListBuilderInterface[]
     */
    private $hotelsListBuilders;

    /**
     * @var HotelsListCollectionStorageInterface
     */
    private $listCollectionStorage;

    /**
     * CalculateCountersCommand constructor.
     *
     * @param HotelsListBuilderInterface[]         $hotelsListBuilders
     * @param HotelsListCollectionStorageInterface $listCollectionStorage
     */
    public function __construct(array $hotelsListBuilders, HotelsListCollectionStorageInterface $listCollectionStorage)
    {
        parent::__construct();
        $this->hotelsListBuilders = $hotelsListBuilders;
        $this->listCollectionStorage = $listCollectionStorage;
    }

    protected function configure()
    {
        $this
            ->setName('build-hotels-lists')
            ->setDescription('Пересчитывает счетчики: направления, тип жилья, удобства отелей, удобства номеров');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $totalTime = -microtime(true);

        $listCollection = new HotelsListCollection();
        $listsBuildTimes = [];

        foreach ($this->hotelsListBuilders as $hotelsListBuilder) {
            $listName = $hotelsListBuilder->getListName();

            $buildTime = -microtime(true);
            $list = $hotelsListBuilder->buildList();
            $buildTime += microtime(true);

            $listCollection->addList($listName, $list);
            $listsBuildTimes[] = "$listName in {$buildTime}s";
        }

        $this->listCollectionStorage->save($listCollection);

        $totalTime += microtime(true);

        $output->writeln('Successfully build lists:' . PHP_EOL .
            implode("\n", $listsBuildTimes) .
            "\nTotal time: {$totalTime}s"
        );
    }

}
