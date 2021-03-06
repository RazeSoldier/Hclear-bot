<?php
/**
 * Used to handle Job-related config
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

class JobConfig extends Config {
	/**
	 * @var int
	 */
	public $maxJob;

	public function __construct() {
		global $gMaxJob;
		$this->maxJob = $gMaxJob;

		$this->checkMaxJob();
	}

	private function checkMaxJob() {
		$default = -1;
		if ( empty( $this->maxJob ) ) {
			$this->maxJob = $default;
		} else {
			if ( !is_int( $this->maxJob ) ) {
				throw new \DomainException( '$gMaxJob must is an integer number of seconds', 111 );
			}
		}
	}
}