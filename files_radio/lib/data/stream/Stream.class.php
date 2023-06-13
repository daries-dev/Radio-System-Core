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
     * Returns the config by name in the specified category.
     */
    public function getConfig(string $name, string $category): mixed
    {
        return $this->config[$category][$name] ?? null;
    }

    /**
     * Returns the shoutcast config by name.
     */
    public function getShoutcastConfig(string $name): mixed
    {
        return $this->getConfig($name, 'shoutcast');
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->streamname;
    }

    /**
     * Returns the transcoder config by name.
     */
    public function getTranscoderConfig(string $name): mixed
    {
        return $this->getConfig($name, 'transcoder');
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
