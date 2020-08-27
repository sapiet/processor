<?php

namespace Sapiet\Processor\Resolvers;

class BaseResolver
{
    /**
     * @param bool $state
     * @param array $bag
     * @return Resolved
     */
    protected function resolved(bool $state, array $bag = []): Resolved
    {
        return (new Resolved())
            ->setState($state)
            ->setBag($bag);
    }
}
