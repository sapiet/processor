<?php

namespace Sapiet\Processor;

use Sapiet\Processor\Exception\UnrecognizedOptionException;
use Sapiet\Processor\Resolvers\ResolverInterface;
use Sapiet\Processor\Storage\StorageInterface;

class Processor
{
    const ERROR_CALLBACK_DELAY_OPTION = 'ERROR_CALLBACK_DELAY_OPTION';

    const AVAILABLE_OPTIONS = [
        self::ERROR_CALLBACK_DELAY_OPTION,
    ];

    /** @var callable */
    private $process;

    /** @var ResolverInterface */
    private $resolver;

    /** @var StorageInterface */
    private $storage;

    /** @var callable */
    private $errorCallback;

    /** @var callable */
    private $successCallback;

    /** @var callable */
    private $processedCallback;

    /** @var array */
    private $options = [];

    /** @return Processor */
    private function clone(): self
    {
        return clone $this;
    }

    /**
     * Process setter
     *
     * @param callable $process
     * @return Processor
     */
    private function setProcess(callable $process): self
    {
        $this->process = $process;

        return $this;
    }

    /**
     * Storage setter
     *
     * @param StorageInterface $storage
     * @return Processor
     */
    private function setStorage(StorageInterface $storage): self
    {
        $this->storage = $storage;

        return $this;
    }

    /**
     * Resolver setter
     *
     * @param ResolverInterface $resolver
     * @return Processor
     */
    public function setResolver(ResolverInterface $resolver): self
    {
        $this->resolver = $resolver;

        return $this;
    }

    /**
     * Error callback setter
     *
     * @param callable $errorCallback
     * @return Processor
     */
    private function setErrorCallback(callable $errorCallback): self
    {
        $this->errorCallback = $errorCallback;

        return $this;
    }

    /**
     * Success callback setter
     *
     * @param callable $successCallback
     * @return Processor
     */
    private function setSuccessCallback(callable $successCallback): self
    {
        $this->successCallback = $successCallback;

        return $this;
    }

    /**
     * Processed callback setter
     *
     * @param callable $processedCallback
     * @return Processor
     */
    private function setProcessedCallback(callable $processedCallback): self
    {
        $this->processedCallback = $processedCallback;

        return $this;
    }

    /**
     * Option setter
     *
     * @param string $name
     * @param $value
     * @return Processor
     * @throws UnrecognizedOptionException
     */
    private function setOption(string $name, $value): self
    {
        if (false === in_array($name, self::AVAILABLE_OPTIONS)) {
            throw new UnrecognizedOptionException($name, self::AVAILABLE_OPTIONS);
        }

        $this->options[$name] = $value;

        return $this;
    }

    /**
     * Check if an option exists
     *
     * @param string $name
     * @return bool
     */
    private function hasOption(string $name): bool
    {
        return true === array_key_exists($name, $this->options);
    }

    /**
     * Retrieve an option value
     *
     * @param string $name
     * @return mixed|null
     */
    private function getOption(string $name)
    {
        if (true === $this->hasOption($name)) {
            return $this->options[$name];
        }

        return null;
    }

    /**
     * Define a process
     *
     * @param callable $process
     * @return Processor
     */
    public function withProcess(callable $process): self
    {
        return $this->clone()->setProcess($process);
    }

    /**
     * Define a resolver
     *
     * @param ResolverInterface $resolver
     * @return Processor
     */
    public function withResolver(ResolverInterface $resolver): self
    {
        return $this->clone()->setResolver($resolver);
    }

    /**
     * Define a storage
     *
     * @param StorageInterface $storage
     * @return Processor
     */
    public function withStorage(StorageInterface $storage): self
    {
        return $this->clone()->setStorage($storage);
    }

    /**
     * Define an Error callback
     *
     * @param callable $errorCallback
     * @return Processor
     */
    public function onError(callable $errorCallback): self
    {
        return $this->clone()->setErrorCallback($errorCallback);
    }

    /**
     * Define a success callback
     *
     * @param callable $successCallback
     * @return Processor
     */
    public function onSuccess(callable $successCallback): self
    {
        return $this->clone()->setSuccessCallback($successCallback);
    }

    /**
     * Define a processed callback
     *
     * @param callable $processedCallback
     * @return Processor
     */
    public function onProcessed(callable $processedCallback): self
    {
        return $this->clone()->setProcessedCallback($processedCallback);
    }

    /**
     * Add an option
     *
     * @param string $name
     * @param $value
     * @return Processor
     * @throws UnrecognizedOptionException
     */
    public function withOption(string $name, $value): self
    {
        return $this->clone()->setOption($name, $value);
    }

    /**
     * Execute the process
     *
     * @param mixed ...$args
     * @return Processor
     * @throws \Exception
     */
    public function process(...$args): self
    {
        $resolved = $this->resolver->resolve($this->process, $args);

        if (true === $resolved->getState()) {
            $this->processCallbackSuccess($resolved->getBag());
        } else {
            $this->processCallbackError($resolved->getBag());
        }

        $this->processCallback($this->processedCallback, $resolved->getBag());

        return $this;
    }

    /**
     * Handle success
     * @param array $bag
     * @throws \Exception
     */
    private function processCallbackSuccess(array $bag)
    {
        if ($this->storage->isInError()) {
            $this->processCallback($this->successCallback, $bag);
            $this->storage->setIsInError(false, new \DateTimeImmutable());
        }
    }

    /**
     * Handle error
     *
     * @param array $bag
     * @throws \Exception
     */
    private function processCallbackError(array $bag)
    {
        $now = new \DateTimeImmutable();
        $time = $this->storage->getTime();

        $processCallback = false === $this->storage->isInError()
            || (
                $this->hasOption(self::ERROR_CALLBACK_DELAY_OPTION)
                && null !== $time
                && $now->getTimestamp() - $time >= $this->getOption(self::ERROR_CALLBACK_DELAY_OPTION)
            );

        if ($processCallback) {
            $this->processCallback($this->errorCallback, $bag);
            $this->storage->setIsInError(true, $now);
        }
    }

    /**
     * @param callable|null $callback
     * @param array $bag
     */
    private function processCallback(?callable $callback, array $bag): void
    {
        if (null !== $callback) {
            $callback($bag);
        }
    }
}
