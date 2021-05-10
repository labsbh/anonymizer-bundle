<?php

declare(strict_types=1);

namespace Labsbh\AnonymizerBundle\Exception;

class InvalidConstantException extends \InvalidArgumentException
{
    /**
     * @param mixed $constant
     */
    public function __construct($constant)
    {
        parent::__construct(sprintf('Invalid constant value, should be null or string, "%s" given', gettype($constant)), 0, null);
    }
}
