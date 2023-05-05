<?php

namespace radio\system\stream\option;

use radio\data\stream\Stream;
use wcf\system\form\builder\container\TabTabMenuFormContainer;
use wcf\system\form\builder\data\IFormDataHandler;

/**
 * Default interface for stream options.
 * 
 * @author	Marco Daries
 * @copyright	2023 Daries.dev
 * @license	Attribution-NoDerivatives 4.0 International (CC BY-ND 4.0) <https://creativecommons.org/licenses/by-nd/4.0/>
 */
interface IStreamOption
{
    /**
     * Adds new options to the option tab menu.
     */
    public function addOptions(TabTabMenuFormContainer $optionsTab): void;

    /**
     * Adds form data processor to this form data handler.
     */
    public function addProcessors(IFormDataHandler $dataHandler): void;
}
