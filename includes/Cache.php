<?php
/**
 * Package caching-related things
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

class Cache {
	/**
	 * @var string
	 */
	public $cacheFileName;

	/**
	 * @var resource The resource returned by fopen()
	 */
	private $cacheFile;

	/**
	 * @var string Cache directory
	 */
	public $cacheDir;

	/**
	 * @var string
	 */
	public $cacheFilePath;

	/**
	 * Initialize a Cache object
	 * @param string $filename
	 * @return Cache
	 */
	public function __construct(string $filename) {
		$this->cacheFileName = $filename;
		$this->cacheDir = APP_PATH . '/storage';
		$this->cacheFilePath = $this->cacheDir . '/' . $this->cacheFileName;

		if ( !file_exists( $this->cacheDir ) ) {
			if ( !mkdir( $this->cacheDir ) ) {
				throw new \RuntimeException( "Failed to create {$this->cacheDir} folder", 105 );
			}
		}

		$this->cacheFile = fopen( $this->cacheFilePath, 'a+b' );
	}

	/**
	 * Write a string to the cache file
	 * @param string $text
	 * @return bool
	 */
	public function write(string $text) {
		if ( fwrite( $this->cacheFile, $text ) === false ) {
			trigger_error( "Failed to write {$this->cacheFilePath}", E_USER_WARNING );
		}
		return true;
	}

	/**
	 * Read all contents of the cache file
	 * @return string|false All contents of the cache file
	 */
	public function read() {
		return fread( $this->cacheFile, filesize( $this->cacheFilePath ) );
	}

	/**
	 * Close Cache::$cacheFile resource
	 */
	public function __destruct() {
		if ( is_resource( $this->cacheFile ) ) {
			fclose( $this->cacheFile );
		}
	}
}