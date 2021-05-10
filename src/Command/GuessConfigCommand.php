<?php

declare(strict_types=1);

namespace Labsbh\AnonymizerBundle\Command;

use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;
use Labsbh\AnonymizerBundle\Config\ConfigGuesser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Yaml;

class GuessConfigCommand extends Command
{
    protected static $defaultName = 'labsbh:anonymizer:guess';

    private ManagerRegistry $registry;

    private ConfigGuesser $configGuesser;

    private string $projectDir;

    public function __construct(ManagerRegistry $registry, ConfigGuesser $configGuesser, string $projectDir)
    {
        parent::__construct();
        $this->registry      = $registry;
        $this->configGuesser = $configGuesser;
        $this->projectDir    = $projectDir;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Guess config from database connections')
            ->addOption('dest', 'd', InputOption::VALUE_REQUIRED, 'Output file path', '%kernel.project_dir%/config/packages/dev/labsbh_anonymizer.yaml');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $config = ['labsbh_anonymizer' => []];

        $dest = str_replace('%kernel.project_dir%', $this->projectDir, $input->getOption('dest'));

        if (file_exists($dest)) {
            $override = $io->ask('Destination file already exists. Do you want to override it? [y/N]', 'n');

            if ('y' !== strtolower($override) && !$input->getOption('no-interaction')) {
                return self::FAILURE;
            }

            $config = Yaml::parse(file_get_contents($dest));
        }

        $hints = [];

        foreach ($this->registry->getConnectionNames() as $name => $id) {
            $connection = $this->registry->getConnection($name);
            if (!$connection instanceof Connection) {
                $io->error(sprintf('Invalid connection "%s" (%s)', $name, $connection::class));

                return self::FAILURE;
            }
            $hints[$name] = $this->configGuesser->guess($connection);
        }

        $config['labsbh_anonymizer']['connections'] = $hints;

        file_put_contents($dest, Yaml::dump($config, 8, 4));

        return self::SUCCESS;
    }

}
