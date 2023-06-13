<?php

namespace radio\data\stream\endpoint;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of stream endpoints.
 * 
 * @author	Marco Daries
 * @copyright	2023 Daries.dev
 * @license	Attribution-NoDerivatives 4.0 International (CC BY-ND 4.0) <https://creativecommons.org/licenses/by-nd/4.0/>
 *
 * @method  StreamEndpoint  current()
 * @method  StreamEndpoint[]    getObjects()
 * @method  StreamEndpoint|null getSingleObject()
 * @method  StreamEndpoint|null search($objectID)
 * @property    StreamEndpoint[]    $objects
 */
class StreamEndpointList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = StreamEndpoint::class;
}
