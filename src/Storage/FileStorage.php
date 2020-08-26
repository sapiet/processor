<?php

namespace Sapiet\Processor\Storage;

class FileStorage implements StorageInterface
{
    /**
     * @var string
     */
    private $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * @param array $content
     * @return void
     * @throws \Exception
     */
    private function set(array $content): void
    {
        $result = file_put_contents($this->path, json_encode($content));

        if (false === $result) {
            throw new \Exception(sprintf('An error occured while saving "%s"', $this->path));
        }
    }

    /**
     * @param string|null $field
     * @return mixed
     */
    private function get(string $field = null)
    {
        if (true === file_exists($this->path)) {
            $data = json_decode(file_get_contents($this->path), true);

            if (null !== $field) {
                return $data[$field];
            }

            return $data;
        }

        return null;
    }

    /**
     * @return bool
     */
    public function isInError(): bool
    {
        return true === $this->get('error');
    }

    /**
     * @param bool $isInError
     * @param \DateTimeInterface $date
     * @return void
     * @throws \Exception
     */
    public function setIsInError(bool $isInError, \DateTimeInterface $date): void
    {
        $this->set(['error' => $isInError, 'time' => $date->getTimestamp()]);
    }

    /**
     * @return int|null
     */
    public function getTime(): ?int
    {
        return $this->get('time');
    }
}
