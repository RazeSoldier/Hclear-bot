<?php
/**
 * MultipleUnclosedFormattingTags API
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

class APIMultipleUnclosedFormattingTags extends ApiBase {
	public function __construct(string $query, $options) {
		global $gConfig;
		$wiki = $gConfig->entryConfig->entryPoint;
		switch ( $query ) {
			case 'batch':
				if ( !is_array( $options ) ) {
					throw new \RuntimeException( '$extra is not an array.', 104 );
				}
				$this->apiURL = $this->spliceApiURL( 'action=query&format=json&list=linterrors'
				. "&lntcategories=multiple-unclosed-formatting-tags&lntlimit={$options['limit']}&lntfrom={$options['from']}", $wiki );
				break;
			case 'alone':
				if ( !is_int( $options ) ) {
					throw new \RuntimeException( '$extra is not an integer.', 103 );
				}
				$this->apiURL = $this->spliceApiURL( 'action=query&format=json&list=linterrors'
				. "&lntcategories=multiple-unclosed-formatting-tags&lntpageid={$options}", $wiki );
				break;
			case 'list':
				if ( !is_array( $options ) ) {
					throw new \RuntimeException( '$extra is not an array.', 104 );
				}
				$queryList = null;
				foreach($options as $value ) {
					if ( $queryList === null ) {
						$queryList = $value;
					} else {
						$queryList = $queryList . '|' . $value;
					}
				}
				$queryList = rawurlencode( $queryList );
				$this->apiURL = $this->spliceApiURL( 'action=query&format=json&list=linterrors'
				. "&lntcategories=multiple-unclosed-formatting-tags&lntpageid={$queryList}", $wiki );
				break;
		}
		$c = new Curl( $this->apiURL );
		$this->apiResponseData = jsonToArray( $c->get() );
	}
}