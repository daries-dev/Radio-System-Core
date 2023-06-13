<?php

namespace radio\system\cache\builder;

use radio\data\stream\endpoint\StreamEndpointList;
use wcf\system\cache\builder\AbstractCacheBuilder;

/**
 * Caches the stream enpoints.
 * 
 * @author	Marco Daries
 * @copyright	2023 Daries.dev
 * @license	Attribution-NoDerivatives 4.0 International (CC BY-ND 4.0) <https://creativecommons.org/licenses/by-nd/4.0/>
 */
class StreamEndpointCacheBuilder extends AbstractCacheBuilder
{
    /**
     * @inheritDoc
     */
    protected function rebuild(array $parameters): array
    {
        $data = [
            'defaults' => [],
            'endpoints' => [],
            'streamEndpoints' => [],
        ];

        $list = new StreamEndpointList();
        $list->sqlOrderBy = 'showOrder ASC';
        $list->readObjects();
        foreach ($list as $endpoint) {
            $data['endpoints'][$endpoint->endpointID] = $endpoint;

            if (!isset($data['streamEndpoints'][$endpoint->streamID])) {
                $data['streamEndpoints'][$endpoint->streamID] = [];
            }
            $data['streamEndpoints'][$endpoint->streamID][] = $endpoint->endpointID;

            if ($endpoint->isDefault) {
                $data['defaults'][$endpoint->streamID] = $endpoint->endpointID;
            }
        }

        return $data;
    }
}
