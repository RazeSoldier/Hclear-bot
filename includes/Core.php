<?php
/**
 * Bot core
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

class Core implements IRunnable {
	/**
	 * Run bot
	 */
	public function run() {
		session_start();
		while ( true ) {
			$this->jobManager();
			$job = new Job( function() {
				/** @var Fixer $workFixer */
				$workFixer = Fixer::init();
				$workFixer->run();
			} );
			$job->run();
			unset( $job );
		}
	}

	private function jobManager() {
		global $gConfig;
		static $limit;
		if ( $limit === null ) {
			$limit = $gConfig->jobConfig->maxJob;
		}

		if ( $limit === -1 ) {
			return;
		}
		static $jobs = 0;
		if ( $jobs++ < $limit ) {
			return;
		}
		die( 0 );
	}
}