<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Utils;

class Cached
{
    /**
     * 
     */
    private const ROUTE_REGEX_CACHE = CACHE_DIR . 'routeRegexArray.json';

    /**
     * 
     */
    private const CACHE_EXPIRATION_TIME = EXPIRATION_TIME ?? 5;

    /**
     * 
     */
    public static function routeRegexArray(): ?array
    {
        if (!$jsonCache = file_get_contents(self::ROUTE_REGEX_CACHE)) {
            return null;
        }

        $cache = json_decode($jsonCache, true);

        $expired = (time() - $cache['timestamp']) > self::CACHE_EXPIRATION_TIME;

        return !$expired ? $cache['content'] : null;
    }

    /**
     * 
     */
    public static function saveRouteRegexArray(mixed $content): int|false
    {
        $cache = array(
            'timestamp' => time(),
            'content' => $content
        );

        $jsonContent = json_encode($cache);

        return file_put_contents(self::ROUTE_REGEX_CACHE, $jsonContent);
    }
}
