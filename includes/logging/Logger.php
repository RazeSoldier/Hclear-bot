<?php
/**
 * Logger
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
 * Logger
 * @class
 */
class Logger implements ISingleton {
	const logExtension = 'log.md';

    /**
     * @var Logger
     */
    static private $instance;

	/**
	 * @var string Directory path for storing logs
	 */
	private $logDir;

	/**
	 * @var array Logs currently controlled by Logger
	 */
	private $logs = [];

    /**
     * @return Logger
     */
	static public function getInstance() : Logger
    {
        if ( self::$instance === null ) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
	 * Initialize a Logger object
	 */
	private function __construct() {
		$this->logDir = APP_PATH . '/storage/log/';
		if ( !file_exists( $this->logDir ) ) {
			if ( !mkdir( $this->logDir ) ) {
				throw new \RuntimeException( "Failed to create {$this->logDir} folder", 105 );
			}
		}
	}

	/**
	 * Create a log to Logger::$logs
	 * @param string $name Log name
	 * @param int|string $logIndex Log index value
	 * @return int|string Log index value
	 */
	public function createLog(string $name, $logIndex = null) {
		if ( isset( $logIndex ) ) {
			$this->logs[$logIndex] = new JobLog( $this->logDir . $name . '.' . self::logExtension );
			return $logIndex;
		} else {
			$this->logs[] = new JobLog( $this->logDir . $name . '.' . self::logExtension );
			return getEndKey( $this->logs, true );
		}
	}

	/**
	 * Get all logs
	 * @return array
	 */
	public function listLog() : array {
		return $this->logs;
	}

	/**
	 * Get the log
	 * @param int|string $logIndex Log index value
	 * @return Log
	 */
	public function getLog($logIndex) : Log {
		return $this->logs[$logIndex];
	}

	/**
	 * Count logs in the log directory
	 * @return int The number of logs in the log directory
	 */
	public function countLogs() : int {
		return count( scandir( $this->logDir ) ) - 2;
	}
}