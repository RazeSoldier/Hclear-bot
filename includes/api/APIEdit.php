<?php
/**
 * This class used to edit a page
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

class APIEdit extends ApiBase {
	/**
	 * @var string
	 */
	static private $editToken;

	public function __construct() {
		global $gClient, $gAccessToken;
		if ( self::$editToken === null ) {
			self::$editToken = json_decode( $gClient->makeOAuthCall( $gAccessToken,
				$this->spliceApiURL( 'action=tokens&format=json', 'zhwiki' )
				)
			)->tokens->edittoken;
		}
	}

	/**
	 * Edit a page
	 * @global object $gClient
	 * @global object $gAccessToken
	 * @param string|int $pageName A page title or page ID
	 * @param string $text
	 * @param string|null $summary
	 * @return array
	 */
	public function doEdit($pageName, string $text, string $summary = null) {
		global $gClient, $gAccessToken, $gConfig;
		$varKey = ( is_int( $pageName ) ) ? 'pageid' : 'title';
		$apiParams = [
			'action' => 'edit',
			$varKey => $pageName,
			'summary' => $summary,
			'text' => $text,
			'token' => self::$editToken,
			'format' => 'json',
			'maxlag' => $gConfig->fixerConfig->maxLag
		];
		$gClient->setExtraParams( $apiParams );
		$this->apiResponseData = jsonToArray( $gClient->makeOAuthCall(
			$gAccessToken,
			$this->apiURLPrefix['zhwiki'],
			true,
			$apiParams 
			)
		);
		return $this->apiResponseData;
	}
}