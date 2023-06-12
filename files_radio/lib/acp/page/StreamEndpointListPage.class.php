<?php

namespace radio\acp\page;

use radio\data\stream\StreamList;
use wcf\page\SortablePage;

/**
 * Shows a list of streams.
 * 
 * @author	Marco Daries
 * @copyright	2023 Daries.dev
 * @license	Attribution-NoDerivatives 4.0 International (CC BY-ND 4.0) <https://creativecommons.org/licenses/by-nd/4.0/>
 *
 * @property    StreamList  $objectList
 */
class StreamEndpointListPage extends SortablePage
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'radio.acp.menu.link.stream.list';

    /**
     * @inheritDoc
     */
    public $defaultSortField = 'name';

}