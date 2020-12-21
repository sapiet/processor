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
        $dirname = dirname($this->path);

        if (false === is_dir($dirname)) {
            throw new \Exception(sprintf('Path "%s" is not a directory.', $dirname));
        }

        if (false === is_writable($dirname)) {
            throw new \Exception(sprintf('Directory "%s" is not writable.', $dirname));
        }

        if (true === file_exists($this->path) && false === is_writable($this->path)) {
            throw new \Exception(sprintf('File "%s" is not writable.', $this->path));
        }

        $result = file_put_contents($this->path, json_encode($content));

        if (false === $result) {
            throw new \Exception(sprintf('An error occured while saving "%s"', $this->path));
        }
    }

    /**
     * @param string|null $field
     * @return mixed
     * @throws \Exception
     */
    private function get(string $field = null)
    {
        if (true === file_exists($this->path)) {
            if (false === is_readable($this->path)) {
                throw new \Exception(sprintf('File "%s" is not readable.', $this->path));
            }

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
     * @throws \Exception
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
     * @throws \Exception
     */
    public function getTime(): ?int
    {
        return $this->get('time');
    }
}
