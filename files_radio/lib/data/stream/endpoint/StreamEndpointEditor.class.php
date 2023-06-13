<?php

namespace radio\data\stream\endpoint;

use radio\system\cache\builder\StreamEndpointCacheBuilder;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;

/**
 * Provides functions to edit stream endpoints.
 * 
 * @author	Marco Daries
 * @copyright	2023 Daries.dev
 * @license	Attribution-NoDerivatives 4.0 International (CC BY-ND 4.0) <https://creativecommons.org/licenses/by-nd/4.0/>
 *
 * @method  StreamEndpoint  getDecoratedObject()
 * @mixin   StreamEndpoint
 */
class StreamEndpointEditor extends DatabaseObjectEditor implements IEditableCachedObject
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = StreamEndpoint::class;

    /**
     * @inheritDoc
     */
    public static function resetCache(): void
    {
        StreamEndpointCacheBuilder::getInstance()->reset();
    }
}
