<?php
/**
 * This class used to close unclosed format tags
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

class CloseFormatTag {
	/**
	 * @var string
	 */
	private $needCloseTagName;

	/**
	 * @var string
	 */
	private $input;

	/**
	 * @var array
	 */
	private $tag;

	/**
	 * @var array
	 */
	private $tagLen;

	/**
	 * @var string
	 */
	private $value;

	public function __construct(string $input, string $needCloseTagName) {
		$this->input = $input;
		$this->needCloseTagName = $needCloseTagName;
		$this->tag['start'] = "<{$needCloseTagName}>";
		$this->tag['end'] = "</{$needCloseTagName}>";
		$this->tagLen['startTag'] = mb_strlen( $this->tag['start'] );
		$this->tagLen['endTag'] = mb_strlen( $this->tag['end'] );
	}

	public function doClose() {
		$startTagOffset = mb_strpos( $this->input, $this->tag['start'] );
		$endTagOffset = mb_strpos( $this->input, $this->tag['end'] );
		if ( $endTagOffset === false ) {
			echo $this->scenario1($startTagOffset);
		}
		die;
		if ( $startTagOffset < $endTagOffset ) {
			$this->value = mb_substr( $this->input, $startTagOffset, $endTagOffset - $startTagOffset );
			
		} else {
			
		}
	}

	/**
	 * Used to handle case that without close tags and only with multiple start tags
	 * @param int $startTagOffset Offset of the start tag
	 * @return string
	 */
	private function scenario1(int $startTagOffset) {
		$lastStartTagOffset['original'] = mb_strrpos( $this->input, $this->tag['start'] );
		$withoutStartTag = $this->removeStartTag( $this->catchStr( $this->input,
				$startTagOffset + $this->tagLen['startTag'], $lastStartTagOffset['original'] ) );
		$value = $this->replaceStr( $this->input, $withoutStartTag,
				$startTagOffset + $this->tagLen['startTag'], $lastStartTagOffset['original'] );
		$lastStartTagOffset['processed'] = mb_strrpos( $value, $this->tag['start'] );
		return $this->replaceStr( $value, $this->tag['end'], $lastStartTagOffset['processed'],
				$lastStartTagOffset['processed'] + $this->tagLen['endTag'] - 1 );
	}

	/**
	 * Catch the given string
	 * @param string $string
	 * @param int $startOffset
	 * @param int $endOffset
	 * @return string
	 */
	private function catchStr(string $string, int $startOffset, int $endOffset) {
		return mb_substr( $string, $startOffset, $endOffset - $startOffset );
	}

	/**
	 * Remove all start tags in the given string
	 * @param string $input
	 * @return string
	 */
	private function removeStartTag(string $input) {
		return mb_ereg_replace( $this->tag['start'], null, $input );
	}

	/**
	 * Remove all end tags in the given string
	 * @param string $input
	 * @return string
	 */
	private function removeEndTag(string $input) {
		return mb_ereg_replace( $this->tag['end'], null, $input );
	}

	private function replaceStr(string $input, string $replacement, int $startOffset, int $endOffset) {
		$start = mb_substr( $input, 0, $startOffset );
		$end = mb_substr( $input, $endOffset );
		return $start . $replacement . $end;
	}
}