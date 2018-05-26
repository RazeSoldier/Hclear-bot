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
	 */
	public function __construct() {
		global $gConfig;
		// Initialize working log
		if ( !defined( 'PHPUNIT_TEST' ) ) {
			global $gLogger;
			$this->log = $gLogger->getLog( 'work' );
		}
		$lntfrom = (int)$this->readCache( 'lntfrom' );
		$api = new APIMultipleUnclosedFormattingTags( 'batch', ['limit' => $gConfig->fixerConfig->maxQuery
			, 'from' => $lntfrom] );
		$this->errorList = $api->getData()['query']['linterrors'];
		$this->preProcess();
		if ( !defined( 'PHPUNIT_TEST' ) ) {
			$this->log->write(Markdown::h2('Job Start') . "\n");
			$this->log->write(Markdown::h3('Query result') . "\n");
			$this->log->write( Markdown::codeBlock( print_r( $this->errorList, true ) ) . "\n" );
		}
	}

	/**
	 * Pre process the error list before starting repair
	 */
	private function preProcess() {
		$errorList = $this->errorList;
		// First, generate the ID list of the pages included in the error list
		foreach ( $errorList as $error ) {
			$pageIDs[] = $error['pageid'];
		}
		// Second, find duplicate page IDs
		$arrInfo = array_count_values( $pageIDs );
		// After that, duplicate page error information is put into an array
		$repeated = [];
		foreach ( $arrInfo as $pageID => $count ) {
			$repeatedPage = null;
			if ( $count > 1 ) {
				foreach ( $errorList as $key => $error ) {
					if ( $error['pageid'] === $pageID ) {
						$repeatedPage[] = $error;
						unset( $errorList[$key] );
					}
				}
				if ( count( $repeatedPage ) !== $count ) {
					throw new \RuntimeException( 'Unknown error', 1 );
				}
				$repeatedPage['repeated'] = true;
				$repeated[] = $repeatedPage;
			}
		}
		$this->errorList = array_merge( $errorList, $repeated );
		sort( $this->errorList );
	}

	/**
	 * Run this fixer
	 */
	public function execute() {
		$this->log->write( Markdown::h2( 'Working' ) . "\n" );
		$count = count( $this->errorList );
		for ( $i = 0; $i < $count; $i++ ) {
			$this->main( $this->errorList[$i] );
		}
	}

	/**
	 * Fix a page
	 * @param array $data The error message of a page
	 * @param bool Whether to call main() from execute()?
	 * @return string|null
	 */
	private function main(array $data, bool $mainCall = true) {
		if ( isset( $data['repeated'] ) ) {
			unset( $data['repeated'] );
			$this->handleSamePage( $data );
			return null;
		}
		$this->log->write( Markdown::h3( "Fix [[{$data['title']}]]" ) . "\n" );
		$this->log->write( "Page ID: {$data['pageid']}" . Markdown::newline() );
		$this->log->write( "Lint error ID: {$data['lintId']}" . Markdown::newline() );
		$this->log->write( "Unclosed format tag: {$data['params']['name']}" . Markdown::newline() );

		// Whether the wrong field is output through the template
		if ( !empty( $data['templateInfo'] ) ) {
			// Whether the wrong field is output through multiple templates
			if ( isset( $data['templateInfo']['multiPartTemplateBlock'] ) ) {
				$this->log->write( 'Through multiple templates output' . Markdown::newline() );
				$this->handleMultiTemplateError( $data );
			} else {
				$this->log->write( "Through [[{$data['templateInfo']['name']}]] output" . Markdown::newline() );
				$this->handleTemplateError( $data['templateInfo']['name'] );
			}
		} else {
			$this->log->write( 'Through the template: false' . Markdown::newline() );
		}

		// Do fix
		if ( !isset( $data['content'] ) ) {
			$revision = ( new APIRevisions( $data['pageid'] ) )->getContent();
		} else {
			$revision = $data['content'];
		}
		$text = $this->catchHTML( $revision, $data['location'][0], $data['location'][1] );
		if ( $mainCall ) {
			$result = $this->replaceStr( $revision, $this->loopBranchLine( $text, $data['params']['name'] ),
				$data['location'][0], $data['location'][1] );
		} else {
			return $this->loopBranchLine( $text, $data['params']['name'] );
		}

		$send = edit( 'page',$data['pageid'], $result, 'Fix multiple-unclosed-formatting-tags error' )->getResponse();
		$this->writeCache( 'lntfrom', $data['lintId'] );
		$this->loggingResult( [ 'queryResult' => $data, 'sendResult' => $send ] );
		unset( $revision, $text, $result, $send );
	}

	private function handleSamePage(array $errors) {
		$oldVersion = ( new APIRevisions( $errors[0]['pageid'] ) )->getContent();
		foreach ( $errors as $error ) {
			$error['content'] = $oldVersion;
			$result = [
				'text' => $this->main( $error, false ),
				'startPos' => $error['location'][0],
				'endPos' => $error['location'][1]
			];
			$results[] = $result;
		}
		usort( $results, function ($a, $b) {
			if ( $a['startPos'] === $b['startPos'] ) {
				return 0;
			}
			return ( $a['startPos'] < $b['startPos'] ) ? -1 : 1;
		} );
		$newVersion = null;
		foreach ( $results as $key => $result ) {
			if ( $newVersion === null ) {
				// init the new version
				$newVersion = mb_substr( $oldVersion, 0, $result['startPos'] );
			} else {
				$newVersion = $newVersion
					.mb_substr( $oldVersion, $results[$key-1]['endPos'], $result['startPos'] - $results[$key-1]['endPos'] );
			}
			$newVersion = $newVersion . $result['text'];
		}
		$newVersion = $newVersion . mb_substr( $oldVersion, $result['endPos'] );
		$send = edit( 'page',$errors[0]['pageid'], $newVersion, 'Fix multiple-unclosed-formatting-tags error' )->getResponse();
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
	 */
	private function handleMultiTemplateError(array $data) {
		$revision = new APIRevisions( $data['pageid'] );
		$text = $this->catchHTML( $revision->getContent(), $data['location'][0], $data['location'][1] );
		$templateList = $this->catchTemplateName( $text, $revision->getPageTitle() );
		$pageids = new APIPage( 'title', $templateList );
		foreach( $pageids->getData()['query']['pages'] as $page ) {
			$pageList[] = $page['pageid'];
		}

		$apiData = ( new APIMultipleUnclosedFormattingTags( 'list', $pageList ) )->getData();
		if ( isset( $data['query']['linterrors'] ) ) {
			foreach( $data['query']['linterrors'] as $value ) {
				$this->main( $value, false );
			}
		}

		unset( $revision, $pageids, $apiData );
	}

	/**
	 * Used to handle the error field that are output from a template
	 * @param string $templateName The name of the template that contain lint errors
	 */
	private function handleTemplateError(string $templateName) {
		$this->log->write( Markdown::h4( "Fix [[{$templateName}]]" ) . "\n" );
		$revision = new APIRevisions( $templateName, true );
		$apier = new APIMultipleUnclosedFormattingTags( 'alone', $revision->getPageID() );
		if ( !isset( $apier->getData()['query']['linterrors'] ) ) {
			$this->log->write( 'No lint error data has been found' . Markdown::newline() );
			return;
		}
		foreach ( $apier->getData()['query']['linterrors'] as $value ) {
			$this->main( $value, false );
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
	 * @param string $pageTitle
	 * @return array
	 */
	private function catchTemplateName(string $text, string $pageTitle = null) : array {
		$pattern = '/{{(?<name>((?!\|)(?!}}).)*)\n?(?<suffix>((?!}}).|\n)*)}}/';
		preg_match_all( $pattern, $text, $matches );
		foreach( $matches['name'] as $value ) {
			// Match like {{/test}} case
			if ( strpos( $value, '/' ) === 0 ) {
				$returnValue[] = $pageTitle . $value;
			// Match like {{:test}} case
			} elseif ( 0 === $pos = strpos( $value, ':' ) ) {
				$returnValue[] = str_replace( ':', null, $value );
			// Match like {{topic:test}} case
			} elseif ( $pos > 0 ) {
				if ( strpos( $this->catchHTML( $value,0, $pos ), ' ' ) !== false ) {
					$returnValue[] = 'Template:' . $value;
				} else {
					$returnValue[] = $value;
				}
			} else {
				$returnValue[] = 'Template:' . $value;
			}
		}
		return $returnValue;
	}
}