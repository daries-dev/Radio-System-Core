<?php

namespace radio\system\stream\type;

use wcf\system\form\builder\container\TabTabMenuFormContainer;

/**
 * Default interface for stream types.
 * 
 * @author	Marco Daries
 * @copyright	2023 Daries.dev
 * @license	Attribution-NoDerivatives 4.0 International (CC BY-ND 4.0) <https://creativecommons.org/licenses/by-nd/4.0/>
 */
interface IStreamType
{
    /**
     * Returns id of this stream type.
     */
    public function getStreamTypeID(): int;

    /**
     * Set the stream options for the given options tab.
     */
    public function setStreamAddOptions(TabTabMenuFormContainer $optionsTab): void;
}
