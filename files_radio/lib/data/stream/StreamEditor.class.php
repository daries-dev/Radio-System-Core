<?php

namespace radio\data\stream;

use wcf\data\DatabaseObjectEditor;

/**
 * Provides functions to edit streams.
 * 
 * @author	Marco Daries
 * @copyright	2023 Daries.dev
 * @license	Attribution-NoDerivatives 4.0 International (CC BY-ND 4.0) <https://creativecommons.org/licenses/by-nd/4.0/>
 *
 * @method  Stream  getDecoratedObject()
 * @mixin   Stream
 */
class StreamEditor extends DatabaseObjectEditor
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = Stream::class;
}
