<?php

/**
 * @author	Marco Daries
 * @copyright	2023 Daries.dev
 * @license	Attribution-NoDerivatives 4.0 International (CC BY-ND 4.0) <https://creativecommons.org/licenses/by-nd/4.0/>
 */
// @codingStandardsIgnoreFile

$scServShellCommand = sprintf('chmod a+x %s', __DIR__ . '/../shoutcast/sc_serv');
$scTransShellCommand = sprintf('chmod a+x %s', __DIR__ . '/../shoutcast/sc_trans');

shell_exec($scServShellCommand);
shell_exec($scTransShellCommand);
