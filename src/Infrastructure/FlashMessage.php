<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Infrastructure;

class FlashMessage
{
    /**
     * 
     */
    private array $next = [];

    /**
     * 
     */
    private array $previous = [];

    /**
     * 
     */
    public function __construct()
    {
        if (isset($_SESSION[FLASH])) {
            $this->previous = $_SESSION[FLASH];
            unset($_SESSION[FLASH]);
        }
    }

    /**
     * 
     */
    public function __destruct()
    {
        $_SESSION[FLASH] = $this->next;
    }

    /**
     * 
     */
    public function set(array $messages): void
    {
        foreach ($messages as $name => $message) {
            $this->next[$name] = $message;
        }
    }

    /**
     * 
     */
    public function add(array $messages): void
    {
        foreach ($messages as $name => $message) {
            $this->next[$name][] = $message;
        }
    }

    /**
     * 
     */
    public function get(string $name): string
    {
        return $this->previous[$name] ?? '';
    }

    /**
     * 
     */
    public function getArray(string $name): array
    {
        return $this->previous[$name] ?? [];
    }
}
