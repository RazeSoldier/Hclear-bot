<?php
/**
 * This file used to define function
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

namespace HclearBot;

use MediaWiki\OAuthClient\{
	ClientConfig,
	Consumer,
	Client,
	Token
};

/**
 * Used to convert JSON to an array
 * @param string $jsonData
 * @return array
 * @throws \RuntimeException
 */
function jsonToArray(string $jsonData) : array {
	$arr = json_decode( $jsonData, true );
	$errorMsg = json_last_error_msg();
	if ( $errorMsg !== 'No error' ) {
		throw new \RuntimeException( 'Json encoding failed: ' .  $errorMsg );
	}
	return $arr;
}

/**
 * Find the offset of No.$count $find substring in $str
 * @param string $str
 * @param string $find
 * @param int $count
 * @param int $offset
 * @return int
 */
function findSubStr(string $str, string $find, int $count, int $offset = 0) : int {
	$pos = mb_stripos( $str, $find, $offset );
	$count--;
	if ( $count > 0 && $pos !== false ) {
		$pos = findSubStr( $str, $find, $count, $pos + 1 );
	}
	return $pos;
}

/**
 * A user-defined error handler function
 * @param int $errno
 * @param string $errstr
 * @param string $errfile
 * @param int $errline
 * @throws \ErrorException
 */
function errorHandler(int $errno, string $errstr,string $errfile, int $errline) {
    throw new \ErrorException( $errstr, 0, $errno, $errfile, $errline );
}

/**
 * Split a string by line
 * @param string $text
 * @return array
 */
function branchLine(string $text) : array {
	return mb_split( '\n', $text );
}

/**
 * OAuth authorize
 * @global Config $gConfig
 * @return void
 */
function oauthAuthorize() {
	global $gConfig;
	$endpoint = getEndPoint();
	$conf = new ClientConfig( $endpoint );
	$conf->setConsumer( new Consumer( $gConfig->authConfig->consumerKey, $gConfig->authConfig->consumerSecret ) );

	$GLOBALS['gClient'] = new Client( $conf );
	$GLOBALS['gAccessToken'] = new Token( $gConfig->authConfig->accessKey, $gConfig->authConfig->accessSecret );
}

/**
 * Get the last value of an array
 * @param array $arr
 * @param bool $num
 * @return int|string
 */
function getEndKey(array $arr, bool $num = false) {
	end( $arr );
	if ( !$num ) {
		return key( $arr );
	} else {
		if ( !is_int( key( $arr ) ) ) {
			while ( true ) {
				prev( $arr );
				if ( is_int( key( $arr ) ) ) {
					break;
				}
			}
		}
		return key( $arr );
	}
}

/**
 * Do a edit
 * @param string $editType Allow value: page or section
 * @param int|string $page
 * @param string $text
 * @param string|null $summary
 * @return EditResult
 */
function edit(string $editType, $page, string $text, string $summary = null) {
	$editor = Editor::getInstance();
	if ( $editType === 'page' ) {
		$result = $editor->editPage( $page, $text, $summary );
	} elseif ( $editType === 'section' ) {
		$result = $editor->editSection();
	} else {
	    throw new \LogicException(  "Pass undefined option: {$editType}", 10 );
    }
	return $result;
}

/**
 * Checks if an array is one-dimensional
 * @param array $arr
 * @return bool
 */
function isOneDimensionalArray(array $arr) : bool {
	if ( count( $arr ) === count( $arr, COUNT_RECURSIVE ) ) {
		return true;
	}
	return false;
}

/**
 * Get OAuth end point
 * @return string OAuth end point
 */
function getEndPoint() : string {
	static $endPoint;
	if ( $endPoint === null ) {
		$wikiName = Config::getInstance()->entryConfig->entryPoint;
		$endPoint = 'https://' . WMFSite::getSiteDomain( $wikiName )  . '/w/index.php?title=Special:OAuth';
	}
	return $endPoint;
}

/**
 * Get the API point for the entry wiki
 * @return string The API point for the entry wiki
 */
function getApiPoint() {
	static $apiPoint;
	if ( $apiPoint === null ) {
		$wikiName = Config::getInstance()->entryConfig->entryPoint;
		$apiPoint = 'https://' . WMFSite::getSiteDomain( $wikiName )  . '/w/api.php';
	}
	return $apiPoint;
}