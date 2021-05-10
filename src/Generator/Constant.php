<?php

declare(strict_types=1);

namespace Labsbh\AnonymizerBundle\Generator;

use Labsbh\AnonymizerBundle\Exception\InvalidConstantException;

class Constant implements GeneratorInterface
{
    private ?string $constant;

    /**
     * @param mixed $constant
     */
    public function __construct(mixed $constant)
    {
        if (null !== $constant && !\is_string($constant)) {
            throw new InvalidConstantException($constant);
        }
        $this->constant = $constant;
    }

    public function __invoke(): ?string
    {
        return $this->constant;
    }
}
