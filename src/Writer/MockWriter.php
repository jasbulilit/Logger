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

class MockWriter extends AbstractWriter {

	/**
	 * @var array
	 */
	private $_logs;

	/**
	 * @return string[]
	 */
	public function getLogList() {
		return $this->_logs;
	}

	/**
	 * @param string $formatted_log
	 * @return void
	 */
	protected function doWrite($formatted_log) {
		$this->_logs[] = $formatted_log;
	}
}