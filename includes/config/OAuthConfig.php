<?php
/**
 * Used to handle OAuth-related config
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

class OAuthConfig extends Config {
	public $accessKey;
	public $accessSecret;
	public $consumerKey;
	public $consumerSecret;

	/**
	 * Initialize a OAuthConfig object
	 * @return object OAuthConfig
	 */
	public function __construct() {
		global $gAccessKey, $gAccessSecret, $gConsumerKey, $gConsumerSecret;
		$this->accessKey = $gAccessKey;
		$this->accessSecret = $gAccessSecret;
		$this->consumerKey = $gConsumerKey;
		$this->consumerSecret = $gConsumerSecret;

		$needCheckConfig = [
			'$gAccessKey' => $this->accessKey,
			'$gAccessSecret' => $this->accessSecret,
			'$gConsumerKey' => $this->consumerKey,
			'$gConsumerSecret' => $this->consumerSecret
		];
		$this->checkIsSet( $needCheckConfig );
	}
}
