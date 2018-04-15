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
 * @throws ErrorException
 */
function exception_error_handler(int $errno, string $errstr,string $errfile, int $errline) {
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