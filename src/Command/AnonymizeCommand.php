<?php

declare(strict_types=1);

namespace Labsbh\AnonymizerBundle\Command;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Exception;
use Doctrine\Persistence\ManagerRegistry;
use Labsbh\AnonymizerBundle\AnonymizedConnection;
use Labsbh\AnonymizerBundle\Anonymizer;
use Labsbh\AnonymizerBundle\Config\YamlConfigFileLoader;
use Labsbh\AnonymizerBundle\DependencyInjection\Configuration;
use Labsbh\AnonymizerBundle\GeneratorFactory\GeneratorFactoryInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AnonymizeCommand extends Command
{
    protected static $defaultName = 'labsbh:anonymizer:anonymize';

    private ManagerRegistry $registry;

    private GeneratorFactoryInterface $generatorFactory;

    private Anonymizer $anonymizer;

    private ?array $defaultConfig = null;

    public function __construct(ManagerRegistry $registry, GeneratorFactoryInterface $generatorFactory, Anonymizer $anonymizer, ?array $defaultConfig)
    {
        parent::__construct();
        $this->registry         = $registry;
        $this->generatorFactory = $generatorFactory;
        $this->anonymizer       = $anonymizer;
        $this->defaultConfig    = $defaultConfig;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Anonymize database sensible data')
            ->addOption('config', 'c', InputOption::VALUE_REQUIRED, 'The anonymize config file');
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io      = new SymfonyStyle($input, $output);
        $confirm = $io->ask('Are you sure you want to anonymize your database? [y/N]', 'n');

        if ('y' !== strtolower($confirm) && !$input->getOption('no-interaction')) {
            return self::FAILURE;
        }

        $config = $this->defaultConfig;

        if ($configFile = $input->getOption('config')) {
            $configFilePath = realpath($input->getOption('config'));
            if (!is_file($configFilePath)) {
                $io->error(sprintf('Configuration file "%s" does not exist.', $configFile));

                return self::FAILURE;
            }

            $config = $this->getConfigFromFile($configFilePath);
        }

        $connections = [];
        foreach ($config['connections'] as $name => $tables) {
            if (!array_key_exists($name, $this->registry->getConnectionNames())) {
                $io->error(sprintf('Can‘t find "%s" doctrine connection', $name));

                return self::FAILURE;
            }
            $connection = $this->registry->getConnection($name);
            if (!$connection instanceof Connection) {
                $io->error(sprintf('Invalid connection "%s" (%s)', $name, $connection::class));

                return self::FAILURE;
            }
            $connections[] = new AnonymizedConnection($this->generatorFactory, $connection, $tables);
        }

        foreach ($connections as $connection) {
            ($this->anonymizer)($connection->getConnection(), $connection->getAnonymizedTables());
        }

        return self::SUCCESS;
    }

    /**
     * @throws \Exception
     */
    private function getConfigFromFile(string $path): array
    {
        $fileLocator      = new FileLocator();
        $loaderResolver   = new LoaderResolver([new YamlConfigFileLoader($fileLocator)]);
        $delegatingLoader = new DelegatingLoader($loaderResolver);
        $rawConfig        = $delegatingLoader->load($path);

        $configuration = new Configuration();
        $processor     = new Processor();

        return $processor->processConfiguration($configuration, $rawConfig);
    }

}
