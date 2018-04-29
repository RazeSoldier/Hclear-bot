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
	 * @var string Log name
	 */
	private $logName;

	/**
	 * @var SplFileObject
	 */
	private $file;

	/**
	 * Initialize a Log object
	 * @param string $logName The name of the file you want to create
	 * @return Log
	 */
	public function __construct(string $logName) {
		$this->logName = $logName;
		if ( file_exists( $this->logName ) ) {
			throw new \RuntimeException( 'File already exists', 106 );
		} else {
			$this->file = new \SplFileObject( $this->logName, 'bw+' );
		}
	}
}