<?php
/**
 * Api Base class
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

abstract class ApiBase {
	/**
	 * @var array
	 */
	protected $apiURLPrefix = [
		'zhwiki' => 'https://zh.wikipedia.org/w/api.php'
	];

	/**
	 * @var string
	 */
	protected $apiURL;

	/**
	 * @var array
	 */
	protected $apiResponseData;

    /**
     * @var string Which action to perform.
     */
	protected $action;

    /**
     * @var string The format of the output (Default value: json)
     */
	protected $format = 'json';

	/**
	 * Splicing an API URL according to the provided suffix
	 * @param string $suffix
	 * @param string $wiki
	 * @return string
	 */
	protected function spliceApiURL(string $suffix, string $wiki) {
		return $this->apiURLPrefix[$wiki] . '?' . $suffix;
	}

	/**
	 * Get $this->apiResponseData
	 * @return array
	 */
	public function getData() {
		return $this->apiResponseData;
	}
}