<?php
/**
 * Used to test Cache class
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

class CacheTest extends TestCase {
	/**
	 * Test if Cache object can write a string to a cache file
	 */
	public function testWrite() {
		$testFileName = 'test';
		$cache = new \HclearBot\Cache( $testFileName );
		$text = 'This is a test.';

		$this->assertEquals( true, $cache->write( $text ) );
		$this->assertEquals( $text, file_get_contents( $cache->cacheFilePath ) );

		$cache->__destruct();
		unlink( $cache->cacheFilePath );
		rmdir( $cache->cacheDir );
	}

	/**
	 * Test if Cache object can read a string to a cache file
	 */
	public function testRead() {
		mkdir( APP_PATH . '/storage' );
		$text = 'Please read it.';
		file_put_contents( APP_PATH . '/storage/test', $text );

		$cache = new \HclearBot\Cache( 'test' );
		$this->assertEquals( $text, $cache->read() );

		$cache->__destruct();
		unlink( $cache->cacheFilePath );
		rmdir( $cache->cacheDir );
	}
}