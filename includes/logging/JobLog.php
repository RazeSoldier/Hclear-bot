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
class JobLog extends LogBase {
	/**
	 * Initialize this log
	 */
	public function init() {
		global $gConfig;
		$this->write( Markdown::h1( ucfirst( $this->logName ) ) . "\n" ); // Write a title for this log

		// Write a base information for this job
		$this->write( Markdown::h2( 'Job information' ) . "\n" );
		$this->write( 'Start time: ' . $_SESSION['jobStartTime'] . Markdown::newline() );
		$this->write( 'Fix type: ' . $gConfig->fixerConfig->fixType . Markdown::newline() );
	}
}