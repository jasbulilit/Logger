<?php
/**
 * SimpleLogger
 *
 * @author	Jasmine
 * @link	https://github.com/jasbulilit/Logger
 * @package	SimpleLogger
 */
namespace SimpleLogger;

/**
 * Log Writer Interface
 */
interface WriterInterface {

	/**
	 * Filter log item, return true to accept log
	 *
	 * @param LogItem $log
	 * @return boolean
	 */
	public function filter(LogItem $log);

	/**
	 * Write a log message
	 *
	 * @param LogItem $log
	 * @return void
	 */
	public function write(LogItem $log);
}
