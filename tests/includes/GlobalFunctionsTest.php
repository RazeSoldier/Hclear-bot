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

use function HclearBot\getEndKey;
use PHPUnit\Framework\TestCase;

class GlobalFuntionsTest extends TestCase {
	/*
	 * Test branchLine()
	 */
	public function testBranchLine() {
		$text = <<<TEXT
line 1
line 2
    line 3
TEXT;
		$expected = [
			'line 1',
			'line 2',
			'    line 3'
		];
		$this->assertEquals( $expected, \HclearBot\branchLine( $text ) );
	}

	/**
	 * Test getEndKey()
	 */
	public function testGetEndKey() {
		// Test $num = false
		$arr[1] = [
			1 => '1',
			3 => '3'
		];
		$expected[1] = 3;
		$this->assertEquals( $expected[1], getEndKey( $arr[1] ) );

		// Test $num = true
		$arr[2] = [
			1 => '1',
			3 => '3',
			5 => '5',
			'test' => '123'
		];
		$expected[2] = 5;
		$this->assertEquals( $expected[2], getEndKey( $arr[2], true ) );
	}
}