<?php

namespace radio\system\stream\type;

use wcf\data\object\type\ObjectType;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\SingletonFactory;
use wcf\system\WCF;

/**
 * Handles stream types.
 * 
 * @author	Marco Daries
 * @copyright	2023 Daries.dev
 * @license	Attribution-NoDerivatives 4.0 International (CC BY-ND 4.0) <https://creativecommons.org/licenses/by-nd/4.0/>
 */
class StreamTypeHandler extends SingletonFactory
{
    /**
     * stream type object types
     * @var ObjectType[]
     */
    protected $objectTypes = [];

    /**
     * stream types
     * @var IStreamType[]
     */
    protected array $streamTypes = [];

    /**
     * Return the stream types for selection.
     */
    public function getStreamTypeSelection(): array
    {
        $selection = [];

        foreach ($this->objectTypes as $objectType) {
            $selection[$objectType->objectType] = WCF::getLanguage()->get('radio.stream.' . $objectType->objectType);
        }

        return $selection;
    }

    /**
     * Return the stream types
     * 
     * @return IStreamType[]
     */
    public function getStreamTypes(): array
    {
        return $this->streamTypes;
    }

    /**
     * @inheritDoc
     */
    protected function init(): void
    {
        $this->objectTypes = ObjectTypeCache::getInstance()->getObjectTypes('dev.daries.radio.stream.type');
        foreach ($this->objectTypes as $objectType) {
            $this->streamTypes[] = $objectType->getProcessor();
        }
    }
}
