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

/**
 * The fixer that used to fix multiple-unclosed-formatting-tags lint errors
 *
 * @class
 */
class FixMultipleUnclosedFormattingTags extends Fixer {
	/**
	 * Used to store a list of pages that contain lint error messages
	 * @var array
	 */
	private $errorList;

	/**
	 * Initialize a FixMultipleUnclosedFormattingTags object
	 * First, read the mess left by the previous transaction from the cache
	 * Then, query the lint error list from the API
	 *
	 * @return object FixMultipleUnclosedFormattingTags
	 */
	public function __construct() {
		$lntfrom = (int)$this->readCache( 'lntfrom' );
		$api = new APIMultipleUnclosedFormattingTags( 'batch', ['limit' => 20, 'from' => $lntfrom] );
		$this->errorList = $api->getData()['query']['linterrors'];
	}

	/**
	 * Run this fixer
	 *
	 * @return null
	 */
	public function execute() {
		$count = count( $this->errorList );
		for ( $i = 0; $i < $count; $i++ ) {
			$this->main( $this->errorList[$i] );
		}
	}

	/**
	 * Fix a page
	 * @param array $data The error message of a page
	 */
	private function main(array $data) {
		// Whether the wrong field is output through the template
		if ( !empty( $data['templateInfo'] ) ) {
			// Whether the wrong field is output through multiple templates
			if ( isset( $data['templateInfo']['multiPartTemplateBlock'] ) ) {
				$this->handleMultiTemplateError( $data );
			} else {
				$this->handleTemplateError( $data['templateInfo']['name'] );
			}
		}

		// Do fix
		$revision = new APIRevisions( $data['pageid'] );
		$text = $this->catchHTML( $revision->getContent(), $data['location'][0], $data['location'][1] );
		$result = $this->replaceStr( $revision->getContent(), $this->loopBranchLine( $text, $data['params']['name'] ),
				$data['location'][0], $data['location'][1] );

		$send = ( new APIEdit() )->doEdit($data['pageid'], $result, 'Fix multiple-unclosed-formatting-tags error' );
		$this->writeCache( 'lntfrom', $data['lintId'] );
		var_dump(parent::logging( [ 'queryResult' => $data, 'sendResult' => $send ] ));
		unset( $revision, $text, $result, $send );
	}

	private function loopBranchLine(string $text, string $needCloseTag) : string {
		$lines = branchLine( $text );
		$returnValue = null;
		foreach ( $lines as $value ) {
			$tidy = new CloseFormatTag( $value, $needCloseTag );
			if ( $returnValue === null ) {
				$returnValue = $tidy->doClose();
			} else {
				$returnValue = $returnValue . "\n" . $tidy->doClose();
			}
			unset( $tidy );
		}
		return $returnValue;
	}

	/**
	 * Used to handle the error field that are output from multiple templates
	 * @param array $data The parameter that passed to main()
	 * @return null
	 */
	private function handleMultiTemplateError(array $data) {
		$revision = new APIRevisions( $data['pageid'] );
		$text = $this->catchHTML( $revision->getContent(), $data['location'][0], $data['location'][1] );
		$templateList = $this->catchTemplateName( $text );
		$pageids = new APIPage( 'title', $templateList );
		foreach( $pageids->getData()['query']['pages'] as $page ) {
			$pageList[] = $page['pageid'];
		}

		$apiData = ( new APIMultipleUnclosedFormattingTags( 'list', $pageList ) )->getData();
		if ( isset( $data['query']['linterrors'] ) ) {
			foreach( $data['query']['linterrors'] as $value ) {
				$this->main( $value );
			}
		}

		unset( $revision, $pageids, $apiData );
	}

	/**
	 * Used to handle the error field that are output from a template
	 * @param string $templateName The name of the template that contain lint errors
	 * @return null
	 */
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
	private function catchTemplateName(string $text) : array {
		$pattern1 = '/{{(?<name>((?!\|)(?!}}).)*)\n?(?<suffix>((?!}}).|\n)*)}}/';
		preg_match_all( $pattern1, $text, $matches );
		$pattern2 = '/^[T|t]emplate:/';
		foreach( $matches['name'] as $value ) {
			$returnValue[] = 'Template:' . preg_replace( $pattern2, null, $value );
		}
		return $returnValue;
	}
}