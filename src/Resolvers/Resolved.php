<?php

namespace Sapiet\Processor\Resolvers;

class Resolved
{
    /** @var bool */
    private $state;

    /** @var array */
    private $bag;

    /**
     * @return bool
     */
    public function getState(): bool
    {
        return $this->state;
    }

    /**
     * @param bool $state
     * @return Resolved
     */
    public function setState(bool $state): self
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return array
     */
    public function getBag(): array
    {
        return $this->bag;
    }

    /**
     * @param array $bag
     * @return Resolved
     */
    public function setBag(array $bag): self
    {
        $this->bag = $bag;

        return $this;
    }

}
