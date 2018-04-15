<?php
/**
 * Revisions API
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

class APIRevisions extends ApiBase {
	public function __construct($pageName, bool $titleOption = false) {
		if ( $titleOption ) {
			$this->apiURL = $this->spliceApiURL( 'action=query&format=json&prop=revisions'
				. "&titles={$pageName}&formatversion=2&rvprop=content&converttitles=1", 'zhwiki' );
		} else {
			$this->apiURL = $this->spliceApiURL( 'action=query&format=json&prop=revisions'
				. "&pageids={$pageName}&formatversion=2&rvprop=content", 'zhwiki' );
		}
		$c = new Curl( $this->apiURL );
		$this->apiResponseData = jsonToArray( $c->get() );
	}

	/**
	 * Get revision content
	 * @return string
	 */
	public function getContent() {
		return $this->apiResponseData['query']['pages'][0]['revisions'][0]['content'];
	}

	/**
	 * Get page ID
	 * @retuan int
	 */
	public function getPageID() {
		return $this->apiResponseData['query']['pages'][0]['pageid'];
	}
}