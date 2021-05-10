<?php

declare(strict_types=1);

namespace Labsbh\AnonymizerBundle\Generator;

interface GeneratorInterface
{
    public function __invoke(): ?string;
}
