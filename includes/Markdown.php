<?php
/**
 * Used to generate markdown statements
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

class Markdown {
	/**
	 * To generate h1 statement
	 * @param string $text
	 * @return string
	 */
	static public function h1(string $text) : string {
		return self::generateTitle( $text, 1 );
	}

	/**
	 * To generate h2 statement
	 * @param string $text
	 * @return string
	 */
	static public function h2(string $text) : string {
		return self::generateTitle( $text, 2 );
	}

	/**
	 * To generate h3 statement
	 * @param string $text
	 * @return string
	 */
	static public function h3(string $text) : string {
		return self::generateTitle( $text, 3 );
	}

	/**
	 * To generate h4 statement
	 * @param string $text
	 * @return string
	 */
	static public function h4(string $text) : string {
		return self::generateTitle( $text, 4 );
	}

	/**
	 * To generate h5 statement
	 * @param string $text
	 * @return string
	 */
	static public function h5(string $text) : string {
		return self::generateTitle( $text, 5 );
	}

	/**
	 * To generate h6 statement
	 * @param string $text
	 * @return string
	 */
	static public function h6(string $text) : string {
		return self::generateTitle( $text, 6 );
	}

	/**
	 * Newline
	 * @return string
	 */
	static public function newline() : string {
		return '  ' . "\n";
	}

	/**
	 * To generate a code block
	 * @param string $code
	 * @param string $lang Assign language of the $code
	 * @return string A code block
	 */
	static public function codeBlock(string $code, string $lang = null) : string {
		return '```'. $lang . "\n" . $code . "\n" . '```';
	}

	/**
	 * To generate a bold text
	 * @param string $text
	 * @return string
	 */
	static public function bold(string $text) : string {
		return '**' . $text . '**';
	}

	/**
	 * @param string $text
	 * @param int $level Title level,between 1 and 6
	 * @return string
	 */
	static private function generateTitle(string $text, int $level) : string {
		if ( $level < 0 || $level > 6 ) {
			throw new \DomainException( 'Make sure $level is between 1 and 6', 110 );
		}
		return str_repeat( '#', $level ) . ' ' . $text;
	}
}