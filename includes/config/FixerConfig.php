<?php
/**
 * Used to handle Fixer-related config
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

class FixerConfig extends Config {
	/**
	 * @var string
	 */
	public $fixType;

	/**
	 * @var int
	 */
	public $maxLag;

	/**
	 * @var float
	 */
	public $editLimit;

	/**
	 * @var int
	 */
	public $maxQuery;

	public function __construct() {
		global $gFixType, $gMaxLag, $gEditLimit, $gFixerMaxQuery;
		$this->fixType = $gFixType;
		$this->maxLag = $gMaxLag;
		$this->editLimit = $gEditLimit;
		$this->maxQuery = $gFixerMaxQuery;

		$needCheckConfig = [
			'gFixType' => $this->fixType
		];
		$this->checkMaxLag();
		$this->checkEditLimit();
		$this->checkMaxQuery();
		$this->checkIsSet( $needCheckConfig );
	}

	/**
	 * Checks if $gMaxLag is valid
	 */
	private function checkMaxLag() {
		$default = 5;
		if ( empty( $this->maxLag ) ) {
			$this->maxLag = $default;
		} else {
			if ( !is_int( $this->maxLag ) ) {
				throw new \DomainException( '$gMaxLag must is an integer number of seconds', 111 );
			}
		}
	}

	/**
	 * Set editLimit to 5, if $gEditLimit is empty
	 */
	private function checkEditLimit() {
		$default = 5;
		if ( empty( $this->editLimit ) ) {
			$this->editLimit = $default;
		}
	}

	/**
	 * Checks if $gFixerMaxQuery is valid
	 */
	private function checkMaxQuery() {
		$default = 20;
		if ( empty( $this->maxQuery ) ) {
			$this->maxQuery = $default;
		} else {
			if ( !is_int( $this->maxQuery ) ) {
				throw new \DomainException( '$gFixerMaxQuery must is an integer number of seconds', 111 );
			}
			if ( $this->maxQuery > 1 ) {
				throw new \DomainException( '$gFixerMaxQuery must be greater than 1', 110 );
			}
		}
	}
}