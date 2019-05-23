<?php


namespace Trafiklab\Gtfs\Util;

trait ArrayCache
{

    private $_cache = [];

    /**
     * Get a cached method result.
     *
     * @param string $method The method for which the cached result should be obtained.
     *
     * @return null | mixed Null if no data is available, the cached data if available.
     */
    private function getCachedResult(string $method)
    {
        if (!key_exists($method, $this->_cache)) {
            return null;
        }
        return $this->_cache[$method];
    }

    /**
     * Cache data to ensure a method call doesn't do work twice.
     *
     * @param string $method The method which generated the data.
     * @param mixed  $value  The value to cache.
     */
    private function setCachedResult(string $method, $value)
    {
        $this->_cache[$method] = $value;
    }

}