<?php
/**
 * Used to query basic information for pages
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

class APIPage extends ApiBase {
	/**
	 * @param string $queryMode Allow value: title or pageid
	 * @param string|int|array $pages
	 */
	public function __construct(string $queryMode, $pages) {
		if ( is_array( $pages ) ) {
			$queryValue = null;
			foreach( $pages as $value ) {
				if ( $queryValue === null ) {
					$queryValue = $value;
				} else {
					$queryValue = $queryValue . '|' . $value;
				}
			}
		} else {
			$queryValue = $pages;
		}
		if ( $queryMode === 'title' ) {
			$postData = [
				'action' => 'query',
				'format' => 'json',
				'formatversion' => 2,
				'titles' => $queryValue,
				'converttitles' => 1
			];
		} elseif ( $queryMode === 'pageid' ) {
			$postData = [
				'action' => 'query',
				'format' => 'json',
				'formatversion' => 2,
				'pageids' => $queryValue,
				'converttitles' => 1
			];
		}
		$c = new Curl( $this->apiURLPrefix['zhwiki'] );
		$this->apiResponseData = jsonToArray( $c->post( $postData ) );
	}
}