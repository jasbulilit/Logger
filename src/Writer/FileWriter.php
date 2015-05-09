<?php
/**
 * SimpleLogger
 *
 * @author	Jasmine
 * @link	https://github.com/jasbulilit/Logger
 * @package	SimpleLogger
 */
namespace SimpleLogger\Writer;

use SimpleLogger\LogLevel;

/**
 * Write log to file or stram
 */
class FileWriter extends AbstractWriter {

	/**
	 * @var resource
	 */
	private $_stream;

	/**
	 * @param resource|string $streamOrFile	resource or filepath/url to open as a stream
	 * @param integer $level
	 * @throws \InvalidArgumentException|\RuntimeException
	 */
	public function __construct($streamOrFile, $level = LogLevel::INFO) {
		if (is_resource($streamOrFile)) {
			if ('stream' != get_resource_type($streamOrFile)) {
				throw new \InvalidArgumentException('Invalid resource type.');
			}
			$this->_stream = $streamOrFile;
		} else {
			$this->_stream = fopen($streamOrFile, 'a');
			if (! $this->_stream) {
				throw new \RuntimeException('Failed to open file: ' . $streamOrFile);
			}
		}

		$this->setLevel($level);
	}

	/**
	 * Write a message to the log
	 *
	 * @param string $formatted_log
	 * @return void
	 */
	protected function doWrite($formatted_log) {
		fwrite($this->_stream,  $formatted_log . PHP_EOL);
	}
}