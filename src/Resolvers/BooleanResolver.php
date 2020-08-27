<?php

namespace Sapiet\Processor\Resolvers;

class BooleanResolver extends BaseResolver implements ResolverInterface
{
    /** @inheritDoc */
    public function resolve(callable $process, array $args): Resolved
    {
        return $this->resolved(true === call_user_func_array($process, $args));
    }
}
