<?php

namespace Sapiet\Processor\Exception;

class UnrecognizedOptionException extends \Exception
{
    public function __construct(string $name, array $availableOptions)
    {
        parent::__construct(sprintf(
            'Unrecognized option "%s". Available options: "%s"',
            $name,
            implode('", "', $availableOptions)
        ));
    }
}
