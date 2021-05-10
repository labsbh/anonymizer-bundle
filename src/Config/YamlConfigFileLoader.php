<?php

declare(strict_types=1);

namespace Labsbh\AnonymizerBundle\Config;

use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Yaml\Yaml;

class YamlConfigFileLoader extends FileLoader
{
    /**
     * {@inheritDoc}
     */
    public function load($resource, string $type = null)
    {
        return Yaml::parse(file_get_contents($resource));
    }

    /**
     * {@inheritDoc}
     */
    public function supports($resource, string $type = null)
    {
        return \is_string($resource) && 'yaml' === pathinfo($resource, PATHINFO_EXTENSION);
    }
}
