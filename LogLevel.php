<?php
/**
 * SimpleLogger
 *
 * @author jasbulilit
 */
namespace SimpleLogger;

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
		self::CRITICAL	=> '',
		self::ERROR		=> 'ERROR',
		self::WARNING	=> 'WARNING',
		self::NOTICE	=> 'NOTICE',
		self::INFO		=> 'INFO',
		self::DEBUG		=> 'DEBUG'
	);

	public function __construct($level) {
		if (! isset(self::$levels[$level])) {
			throw new \InvalidArgumentException('Invalid level.');
		}
		$this->_level = $level;
	}

	public function getName() {
		return self::$levels[$this->_level];
	}

	public function getLevel() {
		return $this->_level;
	}

	public function getPriority() {
		return $this->_level;
	}

	public function comparePriority(LogLevel $log_level) {
		if ($this->getPriority() == $log_level->getPriority()) {
			return 0;
		}
		return ($this->getPriority() < $log_level->getPriority()) ? 1 : -1;
	}
}
