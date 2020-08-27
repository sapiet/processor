<?php

namespace Sapiet\Processor\Resolvers;

class ExceptionResolver extends BaseResolver implements ResolverInterface
{
    /** @inheritDoc */
    public function resolve(callable $process, array $args): Resolved
    {
        try {
            call_user_func_array($process, $args);
        } catch (\Exception $exception) {
            return $this->resolved(false, compact('exception'));
        }

        return $this->resolved(true);
    }
}
