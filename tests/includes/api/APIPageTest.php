<?php
/**
 * Test the class can edit a page
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

class APIPageTest extends TestCase {
	public function testTitles() {
		$query = [1003, 1033, 2141];
		$result = ( new \HclearBot\APIPage( 'pageid', $query ) )->getData()['query']['pages'];
		
		foreach( $result as $value ) {
			$titles[] = $value['title'];
		}
		$expected = [
			'Wikipedia:繁簡體問題',
			'Talk:东亚',
			'User talk:Movedcsx'
		];
		$this->assertEquals( $expected, $titles );
	}
}