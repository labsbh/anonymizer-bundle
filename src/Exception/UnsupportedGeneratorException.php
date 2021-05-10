<?php

declare(strict_types=1);

namespace Labsbh\AnonymizerBundle\Exception;

namespace Labsbh\AnonymizerBundle\Exception;

class UnsupportedGeneratorException extends \InvalidArgumentException
{
    /**
     * @param mixed $generator
     */
    public function __construct($generator)
    {
        parent::__construct(sprintf('"%s" generator is not known', $generator), 0, null);
    }
}
