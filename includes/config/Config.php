<?php
/**
 * Used to handle config
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
 * Class using facade pattern
 * Used to access specific config object
 * @class
 */
class Config {
	/**
	 * @var OAuthConfig
	 */
	public $authConfig;

	/**
	 * @var EntryConfig
	 */
	public $entryConfig;

	/**
	 * @var FixerConfig
	 */
	public $fixerConfig;

	/**
	 * @var JobConfig
	 */
	public $jobConfig;

	/**
	 * Initialize a Config object
	 * @return Config
	 */
	public function __construct() {
		$this->authConfig = new OAuthConfig();
		$this->entryConfig = new EntryConfig();
		$this->fixerConfig = new FixerConfig();
		$this->jobConfig = new JobConfig();
	}

	/**
	 * Check if these config is empty
	 * If a config is empty, a fatal error is thrown
	 * @param array $needCheckConfig Need to check config
	 * @return null
	 */
	protected function checkIsSet(array $needCheckConfig) {
		foreach( $needCheckConfig as $key => $value ) {
			if ( empty( $value ) ) {
				trigger_error( "Missing {$key} configuration", E_USER_ERROR );
			}
		}
	}
}
