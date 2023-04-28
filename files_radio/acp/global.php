<?php

/**
 * @author	Marco Daries
 * @copyright	2023 Daries.dev
 * @license	Attribution-NoDerivatives 4.0 International (CC BY-ND 4.0) <https://creativecommons.org/licenses/by-nd/4.0/>
 */

// phpcs:disable PSR1.Files.SideEffects

// Constant to get relative path to the radio-root-dir.
if (!\defined('RELATIVE_RADIO_DIR')) {
    \define('RELATIVE_RADIO_DIR', '../');
}

// include config
require_once(__DIR__ . '/../app.config.inc.php');

// include WCF
require_once(RELATIVE_WCF_DIR . 'acp/global.php');
