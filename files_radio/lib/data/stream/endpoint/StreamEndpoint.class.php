<?php

namespace radio\data\stream\endpoint;

use wcf\data\DatabaseObject;
use wcf\system\request\IRouteController;

/**
 * Represents a steam endpoint.
 * 
 * @author	Marco Daries
 * @copyright	2023 Daries.dev
 * @license	Attribution-NoDerivatives 4.0 International (CC BY-ND 4.0) <https://creativecommons.org/licenses/by-nd/4.0/>
 * 
 * @property-read   int $endpointID     unique id of the stream endpoint
 * @property-read   int $streamID       id of the stream the stream endpoint belongs to
 * @property-read   string  $name       name of the stream endpoint
 * @property-read   string  $path       path of the stream endpoint
 * @property-read   array $config      array with config data of the stream endpoint entry
 * @property-read   int $isDefault      is `1` if the stream endpoint is the default stream endpoint on this stream, otherwise `0`
 * @property-read   int $isDisabled     is `1` if the stream endpoint is disabled and thus neither accessible nor selectable, otherwise `0`
 * @property-read   int $showOrder      position of the stream endpoint in relation on this stream
 */
final class StreamEndpoint extends DatabaseObject implements IRouteController
{
    /**
     * Returns the value of the stream endpoint config with the given name.
     */
    public function getEndpointConfig(string $name): mixed
    {
        return $this->data['config'][$name] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    protected function handleData($data): void
    {
        parent::handleData($data);

        $this->data['config'] = @\unserialize($this->data['config']);
        if (!\is_array($this->data['config'])) {
            $this->data['config'] = [];
        }
    }
}
