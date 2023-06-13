<?php

namespace radio\data\stream\endpoint;

use radio\system\cache\builder\StreamEndpointCacheBuilder;
use wcf\system\SingletonFactory;

/**
 * Manages the stream endpoint cache.
 * 
 * @author	Marco Daries
 * @copyright	2023 Daries.dev
 * @license	Attribution-NoDerivatives 4.0 International (CC BY-ND 4.0) <https://creativecommons.org/licenses/by-nd/4.0/>
 */
class StreamEndpointCache extends SingletonFactory
{
    /**
     * cached stream endpoints that are marked as default
     * @var int[]
     */
    protected array $defaults = [];

    /**
     * cached stream endpoints
     * @var StreamEndpoint[]
     */
    protected array $endpoints = [];

    /**
     * cached stream endpoints sorted by streams
     * @var int[][]
     */
    protected array $streamEndpoints = [];

    /**
     * Returns the default stream endpoint from the given stream id
     * or `null` if no stream endpoint is found.
     */
    public function getDefault(int $streamID): ?StreamEndpoint
    {
        return $this->defaults[$streamID] ? $this->getEndpoint($this->defaults[$streamID]) : null;
    }

    /**
     * Returns the stream endpoint with the given stream endpoint id 
     * or `null` if no stream endpoint is found.
     */
    public function getEndpoint(int $endpointID): ?StreamEndpoint
    {
        return $this->endpoints[$endpointID] ?? null;
    }

    /**
     * Return the stream endpoints with the given stream id or `null`
     * if no stream endpoints is found.
     * 
     * @return  StreamEndpoint[]
     */
    public function getEndpoints(int $streamID): array
    {
        $streamEndpoints = $this->streamEndpoints[$streamID] ?? [];

        $list = [];
        foreach ($streamEndpoints as $endpointID) {
            $endpoint = $this->getEndpoint($endpointID);
            if (!$endpoint) continue;

            $list[] = $endpoint;
        }

        return $list;
    }

    /**
     * @inheritDoc
     */
    protected function init(): void
    {
        $cache = StreamEndpointCacheBuilder::getInstance()->getData();
        $this->defaults = $cache['defaults'] ?? [];
        $this->endpoints = $cache['endpoints'] ?? [];
        $this->streamEndpoints = $cache['streamEndpoints'] ?? [];
    }
}
