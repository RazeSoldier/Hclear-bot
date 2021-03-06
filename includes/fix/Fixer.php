<?php
/**
 * The base class of the subclass used to repair lint errors
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
 * The base class of the subclass used to repair lint errors
 *
 * @class
 */
abstract class Fixer implements IRunnable {
	/**
	 * @var Log
	 */
	protected $log;

	/**
	 * @var array
	 */
	protected $fixResult = [
		'Edited' => 0,
		'Unchanged edit' => 0,
		'Edit failed' => 0,
		'Unknown status' => 0
	];

	/**
	 * @var array Fixer map
	 */
	static public $fixers = [
		'multiple-unclosed-formatting-tags' => 'FixMultipleUnclosedFormattingTags'
	];

	/**
	 * Initialize the work fixer
	 */
	static public function init() {
		global $gConfig;
		$fixerName = '\HclearBot\\' . self::$fixers[$gConfig->fixerConfig->fixType];
		$fixer = new $fixerName();
		return $fixer;
	}

	/**
	 * Shutdown this fixer
	 */
	public function __destruct() {
		$_SESSION['fixResult'] = $this->fixResult;
	}

	# The following area to place the help methods

	/**
	 * Used to catch HTML
	 * @param string $input
	 * @param int $start
	 * @param int $end
	 * @return string
	 */
	protected function catchHTML(string $input, int $start, int $end) : string {
		return mb_substr( $input, $start, $end - $start );
	}

	/**
	 * 
	 * @param array $data
	 * @return array
	 */
	protected function loggingResult(array $data) {
		if ( isset( $data['sendResult']['edit']['result'] ) ) {
			if ( isset( $data['sendResult']['edit']['nochange'] ) ) {
				$this->log->write( Markdown::bold( 'Fixed result: Unchanged edit' ) . Markdown::newline() );
				$this->fixResult['Unchanged edit']++;
				$result = 'Without diff';
			} elseif ( $data['sendResult']['edit']['result'] === 'Success' ) {
				$result = 'Success';
				$this->log->write( Markdown::bold( 'Fixed result: Edited' ) . Markdown::newline() );
				$this->fixResult['Edited']++;
			} else {
				$result = $data['sendResult']['edit']['result'];
				$this->log->write( Markdown::bold( "Fixed result: {$result}" ) . Markdown::newline() );
				$this->fixResult['Unknown status']++;
			}
		} else {
			$result = 'Edit failed';
			$this->log->write( Markdown::bold( "Fixed result: {$result}" ) . Markdown::newline() );
			$this->fixResult['Edit failed']++;
		}
		if ( !empty( $data['queryResult']['templateInfo'] ) ) {
			if ( !isset( $data['queryResult']['templateInfo']['multiPartTemplateBlock'] ) ) {
				$isViaTemplateOutput = $data['queryResult']['templateInfo']['name'];
			} else {
				$isViaTemplateOutput = 'multiPartTemplateOutput';
			}
		} else {
			$isViaTemplateOutput = false;
		}
		$returnValue =  [
			'pageName' => $data['queryResult']['title'],
			'pageID' => $data['queryResult']['pageid'],
			'isViaTemplateOutput?' => $isViaTemplateOutput,
			'result' => $result
		];
		if ( isset( $data['sendResult']['error'] ) ) {
			$returnValue['errorMsg'] = $data['sendResult']['error'];
		}
		return $returnValue;
	}

	/**
	 * Write a string to a cache file
	 * @param string $filename The filename of a cache file
	 * @param string $data A string need to be cached
	 */
	protected function writeCache(string $filename, string $data) {
		$cache = new Cache( $filename );
		$cache->write( $data );
		unset( $cache );
	}

	/**
	 * Read a string from a cache file
	 * @param string $filename The filename of a cache file
	 * @return string The cached string
	 */
	protected function readCache(string $filename) : string {
		$cache = new Cache( $filename );
		$returnValue = $cache->read();
		unset( $cache );
		return $returnValue;
	}
}