<?php

namespace radio\system\form\builder\data\processor;

use radio\data\stream\Stream;
use wcf\data\IStorableObject;
use wcf\system\exception\InvalidObjectArgument;
use wcf\system\form\builder\data\processor\AbstractFormDataProcessor;
use wcf\system\form\builder\IFormDocument;

/**
 * Represents a stream form (document).
 * 
 * This class extends the normal form document.
 * 
 * @author	Marco Daries
 * @copyright	2023 Daries.dev
 * @license	Attribution-NoDerivatives 4.0 International (CC BY-ND 4.0) <https://creativecommons.org/licenses/by-nd/4.0/>
 */
final class StreamOptionFormDataProcessor extends AbstractFormDataProcessor
{
    /**
     * Initializes a new StreamOptionFormDataProcessor object.
     */
    public function __construct(
        readonly string $property,
        readonly string $prefix,
        readonly bool $isDataProperty = true,
        readonly ?string $category = null
    ) {
    }

    public function processFormData(IFormDocument $document, array $parameters)
    {
        $property = $this->prefix . $this->property;

        $config = $this->category === null ? $parameters['data']['config'] : $parameters['data']['config'][$this->category];
        if (!isset($config) || !\is_array($config)) {
            $config = [];
        }

        if ($this->isDataProperty) {
            if (\array_key_exists($property, $parameters['data'])) {
                $config[$this->property] = $parameters['data'][$property];
                unset($parameters['data'][$property]);
            }
        } elseif (\array_key_exists($property, $parameters)) {
            $config[$this->property] = $parameters[$property];
            unset($parameters[$property]);
        }

        return $parameters;
    }

    public function processObjectData(IFormDocument $document, array $data, IStorableObject $object): array
    {
        if (!($object instanceof Stream)) {
            throw new InvalidObjectArgument($object, Stream::class);
        }

        $config = $this->category === null ? $data['config'] : $data['config'][$this->category];
        if (isset($config[$this->property])) {
            $data[$this->prefix . $this->property] = $config[$this->property];
        }

        return $data;
    }
}
