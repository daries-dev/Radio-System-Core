<?php

namespace radio\system\stream\type;

use wcf\data\object\type\ObjectType;

/**
 * Abstract implementation of a stream types.
 * 
 * @author	Marco Daries
 * @copyright	2023 Daries.dev
 * @license	Attribution-NoDerivatives 4.0 International (CC BY-ND 4.0) <https://creativecommons.org/licenses/by-nd/4.0/>
 */
abstract class AbstractStreamType implements IStreamType
{
    /**
     * stream type name
     */
    protected string $streamTypeName = '';

    /**
     * Creates an AbstractStreamType object.
     */
    public function __construct(readonly ObjectType $objectType)
    {
    }

    /**
     * @inheritDoc
     */
    public function getStreamTypeName(): string
    {
        return $this->streamTypeName;
    }
}
