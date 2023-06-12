<?php

namespace radio\acp\form;

use radio\data\stream\endpoint\StreamEndpoint;
use radio\data\stream\Stream;
use wcf\page\AbstractPage;
use wcf\system\exception\IllegalLinkException;

/**
 * Shows the stream endpoint edit form.
 * 
 * @author	Marco Daries
 * @copyright	2023 Daries.dev
 * @license	Attribution-NoDerivatives 4.0 International (CC BY-ND 4.0) <https://creativecommons.org/licenses/by-nd/4.0/>
 */
class StreamEndpointAddForm extends StreamEndpointAddForm
{
    /**
     * @inheritDoc
     */
    public $formAction = 'edit';

    /**
     * @inheritDoc
     */
    public function readParameters(): void
    {
        AbstractPage::readParameters();

        if (isset($_REQUEST['id'])) {
            $this->formObject = new StreamEndpoint($_REQUEST['id']);
            if (!$this->formObject->endpointID) {
                throw new IllegalLinkException();
            }

            $this->streamID = $this->formObject->streamID;
            $this->stream = new Stream($this->streamID);
        }
    }
}
