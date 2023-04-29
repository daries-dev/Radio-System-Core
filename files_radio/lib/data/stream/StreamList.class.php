<?php

namespace radio\data\stream;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of streams.
 * 
 * @author	Marco Daries
 * @copyright	2023 Daries.dev
 * @license	Attribution-NoDerivatives 4.0 International (CC BY-ND 4.0) <https://creativecommons.org/licenses/by-nd/4.0/>
 *
 * @method  Stream  current()
 * @method  Stream[]    getObjects()
 * @method  Stream|null getSingleObject()
 * @method  Stream|null search($objectID)
 * @property    Stream[]    $objects
 */
class StreamList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = Stream::class;
}
