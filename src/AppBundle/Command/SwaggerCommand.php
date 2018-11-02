<?php

namespace AppBundle\Command;

use ApiPlatform\Core\Documentation\Documentation;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceNameCollectionFactoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class SwaggerCommand extends Command
{
    private $documentationNormalizer;
    private $resourceNameCollectionFactory;
    private $apiTitle;
    private $apiDescription;
    private $apiVersion;
    private $apiFormats;

    public function __construct(
        NormalizerInterface $documentationNormalizer,
        ResourceNameCollectionFactoryInterface $resourceNameCollection,
        string $apiTitle,
        string $apiDescription,
        string $apiVersion,
        array $apiFormats
    ) {
        parent::__construct();

        $this->documentationNormalizer = $documentationNormalizer;
        $this->resourceNameCollectionFactory = $resourceNameCollection;
        $this->apiTitle = $apiTitle;
        $this->apiDescription = $apiDescription;
        $this->apiVersion = $apiVersion;
        $this->apiFormats = $apiFormats;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('api:swagger:custom-export')
            ->setDescription('Dump the Swagger 2.0 (OpenAPI) documentation')
            ->addOption('base_url', null, InputOption::VALUE_REQUIRED, 'Base url of api', null);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $documentation = new Documentation($this->resourceNameCollectionFactory->create(), $this->apiTitle,
            $this->apiDescription, $this->apiVersion, $this->apiFormats);
        $data = $this->documentationNormalizer->normalize($documentation, null, array(
            'base_url' => $input->getOption('base_url')
        ));
        $content = json_encode($data, JSON_PRETTY_PRINT);
        $output->writeln($content);
    }
}
