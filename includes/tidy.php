<?php
/**
 * Tidy HTML
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

class Tidy extends \tidy {
	/**
	 * @var string Input content
	 */
	private $input;

	public function __construct($input) {
		$this->input = $input;
	}

	private function doTidy() {
		$this->parseString( $this->input );
		$this->cleanRepair();
	}

	/**
	 * Filter <body> tag
	 * @param string $input
	 * @return string
	 */
	private function filterBodyTag(string $input) {
		$search = [ "<body>\r\n", "\r\n</body>\r\n" ];
		return str_replace( $search, null, $input );
	}

	/**
	 * Get tidy HTML
	 * @return string|null
	 */
	public function getTidyHTML() {
		if ( empty( $this->value ) ) {
			$this->doTidy();
		}
		$body = $this->body();
		return $this->filterBodyTag( $body->value );
	}
}