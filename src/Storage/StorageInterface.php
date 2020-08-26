<?php

namespace Sapiet\Processor\Storage;

interface StorageInterface
{
    /**
     * @return bool
     */
    public function isInError(): bool;

    /**
     * @param bool $isInError
     * @param \DateTimeInterface $date
     */
    public function setIsInError(bool $isInError, \DateTimeInterface $date);

    /**
     * @return int|null
     */
    public function getTime(): ?int;
}
