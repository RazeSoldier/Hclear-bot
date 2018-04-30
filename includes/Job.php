<?php
/**
 * A job
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
 * A job
 * @class
 */
class Job {
	/**
	 * @var callable The code to be executed by this job
	 */
	private $jobCode;

	/**
	 * @var callable|null The code to be executed when shutdown this job
	 */
	private $destructCode;

	/**
	 * @var float
	 */
	private $startTime;

	/**
	 * @var float
	 */
	private $endTime;

	/**
	 * Job constructor
	 * Initializate this job
	 * @param callable $jobCode The code to be executed by this job
	 * @param callable $destructCode The code to be executed when shutdown this job
	 */
	public function __construct(callable $jobCode, callable $destructCode = null) {
		global $gLogger;
		$_SESSION['jobStartTime'] = $this->startTime = microtime( true );
		$logName = 'job' . ( $gLogger->countLogs() + 1 );
		$gLogger->createLog( $logName, 'work' );

		$this->jobCode = $jobCode;
		$this->destructCode = $destructCode;
	}

	/**
	 * To execute this job
	 */
	public function execute() {
		call_user_func( $this->jobCode );
	}

	/**
	 * Shutdown this job
	 */
	public function __destruct() {
		global $gLogger;
		$_SESSION['jobEndTime'] = $this->endTime = microtime( true );
		$log = $gLogger->getLog( 'work' );
		$log->write( Markdown::h2( 'Job finished' ) . "\n" );
		$log->write( 'Finished time: ' . $_SESSION['jobEndTime'] . Markdown::newline() );
		$log->write( 'Duration: ' . ( $_SESSION['jobEndTime'] - $_SESSION['jobStartTime'] ) . Markdown::newline() );
		$log->write( "Edited: {$_SESSION['fixResult']['Edited']}, " .
			"Unchanged edit: {$_SESSION['fixResult']['Unchanged edit']}, Unknown status: {$_SESSION['fixResult']['Unknown status']}, " .
			"Edit failed: {$_SESSION['fixResult']['Edit failed']}");

		echo "One job was finished:\n";
		print_r( $_SESSION );

		if ( is_callable( $this->destructCode ) ) {
			call_user_func( $this->destructCode );
		}
		$_SESSION = array();
	}
}