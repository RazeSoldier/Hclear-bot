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
		$api = new APIMultipleUnclosedFormattingTags( 'batch', 20 );
		$this->errorList = $api->getData()['query']['linterrors'];
	}

	public function execute() {
		$count = count( $this->errorList );
		for ( $i = 0; $i < $count; $i++ ) {
			$this->main( $this->errorList[$i] );
		}
	}

	private function main(array $data) {
		if ( !empty( $data['templateInfo'] ) ) {
			if ( isset( $data['templateInfo']['multiPartTemplateBlock'] ) ) {
				$this->handleMultiTemplateError( $data );
			} else {
				$this->handleTemplateError( $data['templateInfo']['name'] );
			}
		}
		$revision = new APIRevisions( $data['pageid'] );
		$text = $this->catchHTML( $revision->getContent(), $data['location'][0], $data['location'][1] );
		$tidy = new CloseFormatTag( $text, $data['params']['name'] );
		$result = $this->replaceStr( $revision->getContent(), $tidy->doClose(),
				$data['location'][0], $data['location'][1] );
		$send = ( new APIEdit() )->doEdit($data['pageid'], $result, 'Fix multiple-unclosed-formatting-tags error');
		var_dump(parent::logging( [ 'queryResult' => $data, 'sendResult' => $send ] ));
	}

	private function handleMultiTemplateError(array $data) {
		var_dump($data);
		$revision = new APIRevisions( $data['pageid'] );
		$text = $this->catchHTML( $revision->getContent(), $data['location'][0], $data['location'][1] );
		$templateList = $this->catchTemplateName( $text );
		$pageids = new APIPage( 'title', $templateList );
		foreach( $pageids->getData()['query']['pages'] as $page ) {
			$pageList[] = $page['pageid'];
		}

		$apier = new APIMultipleUnclosedFormattingTags( 'list', $pageList );
		var_dump($apier);die;
		foreach( $templateList as $value ) {
			$apier = new APIMultipleUnclosedFormattingTags( 'list',
					( new APIRevisions( rawurlencode( $value ), true ) )->getPageID() );
			if ( !isset( $apier->getData()['query']['linterrors'] ) ) {
				continue;
			}
			foreach ( $apier->getData()['query']['linterrors'] as $value ) {
				$this->main( $value );
			}
			unset( $apier );
		}
	}

	private function handleTemplateError(string $templateName) {
		$revision = new APIRevisions( $templateName, true );
		$apier = new APIMultipleUnclosedFormattingTags( 'alone', $revision->getPageID() );
		if ( !isset( $apier->getData()['query']['linterrors'] ) ) {
			return;
		}
		foreach ( $apier->getData()['query']['linterrors'] as $value ) {
			$this->main( $value );
		}
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
	 * Capture all template names from $text (wikitext)
	 * @param string $text
	 * @return array
	 */
	private function catchTemplateName(string $text) {
		$pattern1 = '/{{(?<name>((?!\|).)*)([\|]?.*)?}}/';
		preg_match_all( $pattern1, $text, $matches );
		$pattern2 = '/^[T|t]emplate:/';
		foreach( $matches['name'] as $value ) {
			$returnValue[] = 'Template:' . preg_replace( $pattern2, null, $value );
		}
		return $returnValue;
	}
}