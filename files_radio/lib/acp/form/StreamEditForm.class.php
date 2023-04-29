<?php

namespace radio\acp\form;

use radio\data\stream\Stream;
use wcf\system\exception\IllegalLinkException;

/**
 * Shows the stream edit form.
 * 
 * @author	Marco Daries
 * @copyright	2023 Daries.dev
 * @license	Attribution-NoDerivatives 4.0 International (CC BY-ND 4.0) <https://creativecommons.org/licenses/by-nd/4.0/>
 */
class StreamEditForm extends StreamAddForm
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'radio.acp.menu.link.stream.list';

    /**
     * @inheritDoc
     */
    public $formAction = 'edit';

    /**
     * @inheritDoc
     */
    public function readParameters(): void
    {
        parent::readParameters();

        if (isset($_REQUEST['id'])) {
            $this->formObject = new Stream($_REQUEST['id']);
            if (!$this->formObject->streamID) {
                throw new IllegalLinkException();
            }
        } else {
            throw new IllegalLinkException();
        }

        $this->streamTypeID = $this->formObject->streamTypeID;
    }

    /**
     * @inheritDoc
     */
    protected function readStreamType(): void
    {
        // not required for editing
    }
}
