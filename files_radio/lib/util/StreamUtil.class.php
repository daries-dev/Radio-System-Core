<?php

namespace radio\util;

use wcf\system\form\builder\field\BooleanFormField;
use wcf\system\form\builder\field\IntegerFormField;

/**
 * Contains Stream-related functions.
 * 
 * @author	Marco Daries
 * @copyright	2023 Daries.dev
 * @license	Attribution-NoDerivatives 4.0 International (CC BY-ND 4.0) <https://creativecommons.org/licenses/by-nd/4.0/>
 */
final class StreamUtil
{
    /**
     * Converts available options to $parameters['data']['configs'] variable
     * 
     * This method is fine used in CustomFormDataProcessor. To pack available options into the `configs` array.     
     */
    public static function convertOptionsToConfigs(IFormDocument $document, array $availableOptions, string $prefix, array &$parameters): void
    {
        if (!isset($parameters['data']['configs'])) {
            $parameters['data']['configs'] = [];
        }

        foreach ($availableOptions as $option => $subOption) {
            $formField = $document->getNodeById($prefix . $option);
            if (!empty($formField->getSaveValue())) {
                $parameters['data']['configs'][$option] = $formField->getSaveValue();
            } else {
                if ($formField instanceof IntegerFormField || $formField instanceof BooleanFormField) {
                    $parameters['data']['configs'][$option] = 0;
                }
            }

            if (
                !empty($subOption) &&
                \is_array($subOption) &&
                !empty($parameters['data']['configs'][$option])
            ) {
                self::convertOptionsToConfigs($document, $subOption, $prefix, $parameters);
            }

            unset($parameters['data'][$prefix . $option]);
        }
    }
}
