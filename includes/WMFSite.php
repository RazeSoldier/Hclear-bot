<?php
/**
 * 
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

class WMFSite {
	/**
	 * @var array A map for all WMF sites
	 */
	private $list = [
		'zhwiki' => 'zh.wikipedia.org',
		'enwiki' => 'en.wikipedia.org'
	];

	/**
	 * @var string Url to the OAuth special page
	 */
	private $endPoint;

	/**
	 * Initialize a WMFSite object
	 * @param string $wiki A wiki abbreviation (E.g. zhwiki, enwiki)
	 * @return WMFSite
	 */
	public function __construct(string $wiki) {
		if ( !isset( $this->list[$wiki] ) ) {
			throw new \UnexpectedValueException( "Invalid wiki abbreviation \"{$wiki}\"", 200 );
		}
		$this->endPoint = "https://{$this->list[$wiki]}/w/index.php?title=Special:OAuth";
	}

	/**
	 * Get end point URL
	 * @return string
	 */
	public function getEndpoint() : string {
		return $this->endPoint;
	}
}