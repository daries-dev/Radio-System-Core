<?php

namespace radio\acp\page;

use radio\data\stream\endpoint\StreamEndpointCache;
use radio\data\stream\endpoint\StreamEndpointList;
use radio\data\stream\Stream;
use wcf\page\AbstractPage;
use wcf\page\SortablePage;
use wcf\system\exception\IllegalLinkException;
use wcf\system\WCF;

/**
 * Shows a list of streams.
 * 
 * @author	Marco Daries
 * @copyright	2023 Daries.dev
 * @license	Attribution-NoDerivatives 4.0 International (CC BY-ND 4.0) <https://creativecommons.org/licenses/by-nd/4.0/>
 *
 * @property    StreamEndpointList  $objectList
 */
class StreamEndpointListPage extends AbstractPage
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'radio.acp.menu.link.stream.list';

    /**
     * list of endpoints
     */
    public array $endpoints = [];

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.radio.stream.canEditEndpoint'];

    /**
     * stream object
     */
    public Stream $stream;

    /**
     * stream id
     */
    public int $streamID = 0;

    /**
     * @inheritDoc
     */
    public function assignVariables(): void
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'endpoints' => $this->endpoints,
            'stream' => $this->stream,
            'streamID' => $this->streamID,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function readParameters(): void
    {
        parent::readParameters();

        if (isset($_REQUEST['id'])) $this->streamID = \intval($_REQUEST['id']);
        $this->stream = new Stream($this->streamID);
        if (!$this->stream->streamID) {
            throw new IllegalLinkException();
        }
    }

    /**
     * @inheritDoc
     */
    public function readData(): void
    {
        parent::readData();

        $this->endpoints = StreamEndpointCache::getInstance()->getEndpoints($this->stream->streamID);
    }
}
