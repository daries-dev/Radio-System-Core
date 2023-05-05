<?php

namespace radio\data\stream;

use radio\system\stream\type\IStreamType;
use wcf\data\DatabaseObject;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\request\IRouteController;

/**
 * Represents a steam.
 * 
 * @author	Marco Daries
 * @copyright	2023 Daries.dev
 * @license	Attribution-NoDerivatives 4.0 International (CC BY-ND 4.0) <https://creativecommons.org/licenses/by-nd/4.0/>
 * 
 * @property-read   int $streamID       unique id of the stream
 * @property-read   string $objectTypeID        id of the `dev.daries.radio.stream.type` object type
 * @property-read   string  $streamname     name of the stream
 * @property-read   string  $host       host of the stream
 * @property-read   int $port       port of the stream
 * @property-read   array $config      array with config data of the stream entry
 * @property-read   int $showOrder      position of the stream in relation to its siblings
 * @property-read   int $isDisabled     `1` if the stream is disabled 
 */
final class Stream extends DatabaseObject implements IRouteController
{
    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->streamname;
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
