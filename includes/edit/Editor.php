<?php
/**
 * Editor
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
 * Class that using singleton pattern
 */
class Editor implements ISingleton {
	/**
	 * @var Editor
	 */
	private static $instance;

	/**
	 * @var string Token that used to editing action
	 */
	private static $editToken;

	private function __construct() {
		global $gClient, $gAccessToken, $gWMFSite;
		if ( self::$editToken === null ) {
			self::$editToken = json_decode( $gClient->makeOAuthCall( $gAccessToken,
				$gWMFSite->getApiPoint() . '?action=tokens&format=json'
			) )->tokens->edittoken;
		}
	}

	/**
	 * Get Editor instance
	 * @return Editor
	 */
	public static function getInstance() {
		if ( self::$instance === null ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * To edit a page
	 * @param int|string $page The page name or id
	 * @param string $text
	 * @param string|null $summary
	 * @return EditResult
	 */
	public function editPage($page, string $text, string $summary = null) : EditResult {
		$this->throttle();

		global $gConfig;
		$result = new EditResult();
		$varKey = ( is_int( $page ) ) ? 'pageid' : 'title';
		$result->setPageInfo( [$varKey => $page] );
		$apiParams = [
			'action' => 'edit',
			$varKey => $page,
			'summary' => $summary,
			'text' => $text,
			'token' => self::$editToken,
			'format' => 'json',
			'maxlag' => $gConfig->fixerConfig->maxLag
		];
		$result->setResponse(
			$this->handleResponse( $this->sendRequest( $apiParams ), $apiParams )
		);
		$_SESSION['lastEditTime'] = microtime( true );
		return $result;
	}

	public function editSection() {

	}

	/**
	 * Send a edit request
	 * @param array $apiParams
	 * @return string Response
	 */
	private function sendRequest(array $apiParams) : string {
		global $gClient, $gAccessToken, $gWMFSite;
		$gClient->setExtraParams( $apiParams );
		return $gClient->makeOAuthCall(
			$gAccessToken,
			$gWMFSite->getApiPoint(),
			true,
			$apiParams
		);
	}

	/**
	 * Handle edit response
	 * @param string $response
	 * @param array $req
	 * @param bool $mainCall
	 * @return array|false
	 */
	private function handleResponse( string $response, array $req, bool $mainCall = true ) {
		if ( isset( $response['error'] ) ) {
			if ( $response['error']['code'] === 'maxlag' ) {
				if ( $mainCall ) {
					$this->reSendRequest(3, $req );
				} else {
					return false;
				}
			}
		}
		return $response;
	}

	private function reSendRequest(int $try, array $req) {
		static $retry = 0;
		while ( $retry > $try ) {
			// Waiting server
			sleep( ceil( $req['error']['lag'] ) );
			if ( $this->handleResponse( $this->sendRequest( $req ), $req, false ) !== false ) {
				return;
			};
		}
		throw new \RuntimeException( 'Can not edit, because the lag so long', 108 );
	}

	/**
	 * According $gEditLimit to control the editing frequency
	 * @return void
	 */
	private function throttle() {
		global $gConfig;
		if ( !isset( $_SESSION['lastEditTime'] ) ) {
			$_SESSION['lastEditTime'] = microtime( true );
			return;
		}
		$limit = $gConfig->fixerConfig->editLimit;

		$diff = microtime( true ) - $_SESSION['lastEditTime'];
		if ( $diff < $limit ) {
			usleep( $limit - $diff );
		}
	}
}