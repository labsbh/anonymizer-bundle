<?php

declare(strict_types=1);

namespace Labsbh\AnonymizerBundle;

use Labsbh\AnonymizerBundle\DependencyInjection\Compiler\ChainGeneratorFactoryCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class LabsbhAnonymizerBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ChainGeneratorFactoryCompilerPass());
    }

    /**
     * {@inheritdoc}
     */
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
