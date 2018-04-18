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

class Config {
	/**
	 * @var object OAuthConfig
	 */
	public $authConfig;

	/**
	 * Initialize a Config object
	 * @return object Config
	 */
	public function __construct() {
		$this->authConfig = new OAuthConfig();
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
