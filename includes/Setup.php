<?php
/**
 * Include most things that are needed to make Hclear-bot work.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @file
 */

// Define the absolute path to the root directory of this project
define( 'APP_PATH', dirname(__DIR__) );

# For security, this script can only be run in cli mode
if ( PHP_SAPI !== 'cli' ) {
	echo "For security, this script can only be run in cli mode.\n";
	die( 1 );
}

cli_set_process_title( 'Hclear-bot' );

# Verify the version for PHP, requires PHP version 7.0 or later
if ( version_compare( PHP_VERSION, '7.0.0', '<' ) ) {
	trigger_error( "Hclear-bot requires PHP version 7.0 or later\n", E_USER_ERROR );
}

if ( !extension_loaded( 'curl' ) ) {
	trigger_error( "cURL extension is not available.\n", E_USER_ERROR );
}
if ( !extension_loaded( 'mbstring' ) ) {
	trigger_error( "mbstring extension is not available.\n", E_USER_ERROR );
}

mb_internal_encoding( 'UTF-8' );

if ( !is_readable( APP_PATH . '/config.php' ) ) {
	trigger_error( 'Unable to read configuration file or the file does not exist', E_USER_ERROR );
}
$gIsSemiFix = true;
require_once APP_PATH . '/config.php';

require_once APP_PATH . '/includes/AutoLoader.php';

require_once APP_PATH . '/vendor/autoload.php';

require_once APP_PATH . '/includes/GlobalFunctions.php';

require_once APP_PATH . '/includes/Core.php';

# Using errorHandler() to error messages to ErrorException
set_error_handler( 'HclearBot\errorHandler' );

$gLogger = \HclearBot\Logger::getInstance();

$gConfig = \HclearBot\Config::getInstance();

HclearBot\oauthAuthorize();
