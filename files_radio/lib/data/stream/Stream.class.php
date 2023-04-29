<?php

namespace radio\data\stream;

use wcf\data\DatabaseObject;
use wcf\system\request\IRouteController;

/**
 * Represents a steam.
 * 
 * @author	Marco Daries
 * @copyright	2023 Daries.dev
 * @license	Attribution-NoDerivatives 4.0 International (CC BY-ND 4.0) <https://creativecommons.org/licenses/by-nd/4.0/>
 * 
 * @property-read   int $streamID       unique id of the stream
 * @property-read   int $objectTypeID       id of the `dev.daries.radio.stream.objectType` object type
 * @property-read   string  $streamName     name of the stream
 * @property-read   string  $host       host of the stream
 * @property-read   int $port       port of the stream
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
        return $this->streamName;
    }
}
