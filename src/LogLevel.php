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
 * Log level supports the logging levels described by RFC 5424.
 *
 * @link http://tools.ietf.org/html/rfc5424
 */
class LogLevel {

	const EMERGENCY		= 1;
	const ALERT			= 2;
	const CRITICAL		= 3;
	const ERROR			= 4;
	const WARNING		= 5;
	const NOTICE		= 6;
	const INFO			= 7;
	const DEBUG			= 8;

	protected static $levels = array(
		self::EMERGENCY	=> 'EMERGENCY',
		self::ALERT		=> 'ALERT',
		self::CRITICAL	=> 'CRITICAL',
		self::ERROR		=> 'ERROR',
		self::WARNING	=> 'WARNING',
		self::NOTICE	=> 'NOTICE',
		self::INFO		=> 'INFO',
		self::DEBUG		=> 'DEBUG'
	);

	private $_level;

	/**
	 * @param integer $level
	 * @throws \InvalidArgumentException
	 */
	public function __construct($level) {
		if (! isset(self::$levels[$level])) {
			throw new \InvalidArgumentException('Invalid level: ' . $level);
		}
		$this->_level = $level;
	}

	/**
	 * @return string
	 */
	public function getSeverity() {
		return self::$levels[$this->_level];
	}

	/**
	 * @return integer
	 */
	public function getLevel() {
		return $this->_level;
	}

	/**
	 * @return integer
	 */
	public function getPriority() {
		// currently, priority equal to level
		return $this->_level;
	}

	/**
	 * Compare priority
	 *
	 * @param LogLevel $log_level
	 * @return integer return 1 if $log_level is greater priority than this LogLevel, -1 if less priority, and 0 if equal
	 */
	public function comparePriority(LogLevel $log_level) {
		if ($this->getPriority() == $log_level->getPriority()) {
			return 0;
		}
		return ($this->getPriority() > $log_level->getPriority()) ? 1 : -1;
	}
}
