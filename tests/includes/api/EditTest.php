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

namespace HclearBot\test;

use PHPUnit\Framework\TestCase;

class EditTest extends TestCase {
	public function test1() {
		global $gClient, $gAccessToken;
		$editToken = json_decode( $gClient->makeOAuthCall( $gAccessToken,
				'https://zh.wikipedia.org/w/api.php?action=tokens&format=json'
				) )->tokens->edittoken;
		$apiParams = [
			'action' => 'edit',
			'pageid' => 548296,
			'summary' => 'test',
			'text' => 'This is a test.',
			'token' => $editToken,
			'format' => 'json',
		];
		$gClient->setExtraParams( $apiParams );
		echo $gClient->makeOAuthCall(
			$gAccessToken,
			'https://zh.wikipedia.org/w/api.php',
			true,
			$apiParams
		);
	}
}