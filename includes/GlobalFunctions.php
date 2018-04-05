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
 */
function jsonToArray(string $jsonData) {
	return json_decode( $jsonData, true );
}

/**
 * Find the offset of No.$count $find substring in $str
 * @param string $str
 * @param string $find
 * @param int $count
 * @param int $offset
 * @return int
 */
function findSubStr(string $str, string $find, int $count, int $offset = 0) {
	$pos = mb_stripos( $str, $find, $offset );
	$count--;
	if ( $count > 0 && $pos !== false ) {
		$pos = findSubStr( $str, $find, $count, $pos + 1 );
	}
	return $pos;
}