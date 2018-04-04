<?php
/**
 * Curl connector
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

class CurlConnector {
	/**
	 * @var string Link of resource you want
	 */
	private $url;

	/**
	 * @var string HTTP method
	 */
	private $mode;

	/**
	 * @var resource a resource of cURL
	 */
	private $curlResource;

	/**
	 * @param string $url Link of resource you want
	 */
	public function __construct(string $url) {
		$this->url = $url;
		$this->curlResource = curl_init( $this->url );
	}

	private function main() {
		$this->setCurlOption();
	}

	/**
	 * Set some option for a cURL transfer
	 * @return null
	 */
	private function setCurlOption() {
		curl_setopt( $this->curlResource, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $this->curlResource, CURLOPT_AUTOREFERER, true );
		curl_setopt( $this->curlResource, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $this->curlResource, CURLOPT_FOLLOWLOCATION, true );
		if ( $this->mode === 'POST' ) {
			curl_setopt( $this->curlResource, CURLOPT_POST, true );
		}
	}

	/**
	 * GET HTTP method
	 * @return string|bool
	 */
	public function get() {
		$this->mode = 'GET';
		$this->main();
		$result = curl_exec( $this->curlResource );
		if ( $result === false ) {
			trigger_error( "Download failed: Can't get something from {$this->url}", E_USER_WARNING );
			return false;
		}
		return $result;
	}

	public function post(array $postData) {
		$this->mode = 'POST';
		$this->main();
		curl_setopt( $this->curlResource, CURLOPT_POSTFIELDS, $postData );
		$result = curl_exec( $this->curlResource );
		if ( $result === false ) {
			trigger_error( "Post failed: Can't post something to {$this->url}", E_USER_WARNING );
			return false;
		}
		return $result;
	}

	public function __destruct() {
		if ( is_resource( $this->curlResource ) ) {
			curl_close( $this->curlResource );
		}
	}
}