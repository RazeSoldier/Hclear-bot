<?php
/**
 * The abstraction for log
 * @file
 */

namespace HclearBot;

/**
 * Abstract for log
 */
abstract class LogBase {
	/**
	 * @var string Log path
	 */
	protected $logPath;

	/**
	 * @var string Log name
	 */
	protected $logName;

	/**
	 * @var \SplFileObject
	 */
	protected $file;

	/**
	 * Initialize a Log object
	 * @param string $logPath The name of the file you want to create
	 */
	public function __construct(string $logPath) {
		$this->logPath = $logPath;
		$this->logName = basename( $this->logPath, '.' . Logger::logExtension );
		if ( file_exists( $this->logPath ) ) {
			throw new \RuntimeException( 'File already exists', 106 );
		} else {
			$this->file = new \SplFileObject( $this->logPath, 'w+b' );
			$this->init( );
		}
	}

	/**
	 * Write a string to the file
	 * @param string $text
	 * @return bool Returns true if written successfully, or throw a RuntimeException on error
	 */
	public function write(string $text) {
		if ( $this->file->fwrite( $text ) === 0 ) {
			throw new \RuntimeException( 'Write failed', 107 );
		}
		return true;
	}

	/**
	 * Initialize this log
	 */
	protected function init() {
		$this->write( Markdown::h1( ucfirst( $this->logName ) ) . "\n" ); // Write a title for this log
	}
}