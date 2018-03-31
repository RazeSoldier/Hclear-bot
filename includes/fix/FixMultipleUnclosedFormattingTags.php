<?php
/**
 * Used to fix MultipleUnclosedFormattingTags error
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

class FixMultipleUnclosedFormattingTags extends Fixer {
	private $errorList;

	public function __construct() {
		$api = new APIMultipleUnclosedFormattingTags( 20 );
		$this->errorList = $api->getData()['query']['linterrors'];
	}

	public function execute() {
		$count = count( $this->errorList );
		for ( $i = 0; $i < $count; $i++ ) {
			$this->main( $this->errorList[$i] );
		}
	}

	private function main(array $data) {
		$revision = new APIRevisions( $data['pageid'] );
		$text = $this->catchHTML( $revision->getContent(), $data['location'][0], $data['location'][1] );
	}

	/**
	 * Do fix action
	 * @param string $needFix
	 * @param string $text
	 * @return string
	 */
	private function doFix(string $needFix, string $text) {
		$startTag = "<{$needFix}>";
		$endTag = "</{$needFix}>";
	}
}