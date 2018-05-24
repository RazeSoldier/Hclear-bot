<?php
/**
 * Used to manage a log
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

/**
 * Create a log
 * @class
 */
class Log {
	/**
	 * @var string Log path
	 */
	protected $logPath;

	/**
	 * @var string Log name
	 */
	protected $logName;

	/**
	 * @var \SplFileObject
	 */
	protected $file;

	/**
	 * Initialize a Log object
	 * @param string $logPath The name of the file you want to create
	 */
	public function __construct(string $logPath) {
		$this->logPath = $logPath;
		$this->logName = basename( $this->logPath, '.' . Logger::logExtension );
		if ( file_exists( $this->logPath ) ) {
			throw new \RuntimeException( 'File already exists', 106 );
		} else {
			$this->file = new \SplFileObject( $this->logPath, 'w+b' );
			$this->init( );
		}
	}

	/**
	 * Initialize this log
	 */
	protected function init() {
		$this->write( Markdown::h1( ucfirst( $this->logName ) ) . "\n" ); // Write a title for this log
	}

	/**
	 * Write a string to the file
	 * @param string $text
	 * @return bool Returns true if written successfully, or throw a RuntimeException on error
	 */
	public function write(string $text) {
		if ( $this->file->fwrite( $text ) === 0 ) {
			throw new \RuntimeException( 'Write failed', 107 );
		}
		return true;
	}
}