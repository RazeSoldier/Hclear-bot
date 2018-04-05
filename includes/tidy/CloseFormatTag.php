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

	/**
	 * @var int
	 */
	private $checkStartOffset = 0;

	public function __construct(string $input, string $needCloseTagName) {
		$this->value = $this->input = $input;
		$this->needCloseTagName = $needCloseTagName;
		$this->tag['start'] = "<{$needCloseTagName}>";
		$this->tag['end'] = "</{$needCloseTagName}>";
		$this->tagLen['startTag'] = mb_strlen( $this->tag['start'] );
		$this->tagLen['endTag'] = mb_strlen( $this->tag['end'] );
	}

	public function doClose() {
		// Initialize a process in the loop
		$needCheckText = mb_substr( $this->value, $this->checkStartOffset );
		$startTagOffset = mb_strpos( $needCheckText, $this->tag['start'] );
		$endTagOffset = mb_strpos( $needCheckText, $this->tag['end'] );
		$preCheckText = mb_substr( $this->value, 0, $this->checkStartOffset );
		$check['diff'] = 0;

		// Match Scenario 1
		if ( $startTagOffset !== false && $endTagOffset === false ) {
			$check = $this->scenario1( $needCheckText, $startTagOffset );
			$this->value = $preCheckText . $check['text'];
		}

		// Match Scenario 2
		if ( $startTagOffset < $endTagOffset ) {
			$check = $this->scenario2( $needCheckText, $startTagOffset, $endTagOffset );
			$this->value = $preCheckText . $check['text'];
		}

		// Changes the offset of start checkpoint
		$this->checkStartOffset = $this->checkStartOffset + $endTagOffset + $this->tagLen['endTag'] + $check['diff'];

		// Start loop
		if ( $this->checkStartOffset < mb_strlen( $this->value ) ) {
			$this->doClose();
		}

		return $this->value;
	}

	/**
	 * Used to handle case that without close tags and only with multiple start tags
	 * @param int $startTagOffset Offset of the start tag
	 * @return array
	 */
	private function scenario1(string $needCheckText, int $startTagOffset) {
		$lastStartTagOffset['original'] = mb_strrpos( $needCheckText, $this->tag['start'] );
		$withoutStartTag = new TextNode( $this->removeStartTag( $this->catchStr( $needCheckText,
				$startTagOffset + $this->tagLen['startTag'], $lastStartTagOffset['original'] ) ) );
		$value = $this->replaceStr( $needCheckText, $withoutStartTag,
				$startTagOffset + $this->tagLen['startTag'], $lastStartTagOffset['original'] );
		$lastStartTagOffset['processed'] = mb_strrpos( $value, $this->tag['start'] );
		$result['text'] = new TextNode( $this->replaceStr( $value, $this->tag['end'], $lastStartTagOffset['processed'],
				$lastStartTagOffset['processed'] + $this->tagLen['endTag'] - 1 ) );
		$result['diff'] = $result['text']->strLen - mb_strlen( $needCheckText );
		return $result;
	}

	/**
	 * Used to handle case that there a start tag between a set of tags
	 * @param string $needCheckText
	 * @param int $startTagOffset
	 * @param int $endTagOffset
	 * @return array
	 */
	private function scenario2(string $needCheckText, int $startTagOffset, int $endTagOffset) {
		$text = new TextNode( $this->catchStr( $needCheckText, $startTagOffset + $this->tagLen['startTag'],
					$endTagOffset ) );
		$count =  mb_substr_count( $text, $this->tag['start'] );
		if ( $count === 0 ) {
			// Ignore
			$result['text'] = $needCheckText;
			$result['diff'] = 0;
		} elseif ( $count % 2 === 1 ) {
			// Remove the second tag
			$withoutStartTag = new TextNode( $this->removeStartTag( $text ) );
			$result['text'] = $this->replaceStr( $needCheckText, $withoutStartTag,
					$startTagOffset + $this->tagLen['startTag'], $endTagOffset );
			$result['diff'] = $text->strLen - $withoutStartTag->strLen;
		} elseif ( $count % 2 === 0 ) {
			// Close the second tag
			$firstTagOffset = mb_strpos( $text, $this->tag['start'] );
			$fixedStr = new TextNode( $this->closeTag( $text, $firstTagOffset ) );
			$result['text'] = $this->replaceStr( $needCheckText, $fixedStr,
					$startTagOffset + $this->tagLen['startTag'], $endTagOffset );
			$result['diff'] = $fixedStr->strLen - $text->strLen;
		}
		return $result;
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

	/**
	 * Replace the target field with the provided parameter - $replacement
	 * @param string $input
	 * @param string $replacement The content that need to replace
	 * @param int $startOffset The starting offset of the target field
	 * @param int $endOffset The ending offset of the target field
	 * @return string
	 */
	private function replaceStr(string $input, string $replacement, int $startOffset, int $endOffset) {
		$start = mb_substr( $input, 0, $startOffset );
		$end = mb_substr( $input, $endOffset );
		return $start . $replacement . $end;
	}

	/**
	 * Close tag in a given string
	 * @param string $text
	 * @param int $offset
	 * @return string
	 */
	private function closeTag(string $text, int $offset = 0) {
		return $this->replaceStr( $text, $this->tag['end'], $offset,
				$offset + $this->tagLen['startTag'] );
	}
}