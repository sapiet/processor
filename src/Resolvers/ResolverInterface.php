<?php

namespace Sapiet\Processor\Resolvers;

interface ResolverInterface
{
    /**
     * @param callable $process
     * @param array $args
     * @return Resolved
     */
    public function resolve(callable $process, array $args): Resolved;
}
