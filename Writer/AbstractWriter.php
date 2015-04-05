<?php
/**
 * SimpleLogger
 *
 * @author	Jasmine
 * @link	https://github.com/jasbulilit/Logger
 * @package	SimpleLogger
 */
namespace SimpleLogger;

abstract class AbstractWriter implements WriterInterface {

	/**
	 * @var LogLevel
	 */
	protected $level;

	/**
	 * @var boolean
	 */
	protected $enabled = true;

	/**
	 * @var array
	 */
	protected $target_classes = array();

	/**
	 * @return boolean
	 */
	public function isEnabled() {
		return $this->enabled;
	}

	/**
	 * @param boolean $enabled
	 * @return void
	 */
	public function setEnabled($enabled) {
		$this->enabled = (boolean) $enabled;
	}

	/**
	 * @return LogLevel
	 */
	public function getLevel() {
		return $this->level;
	}

	/**
	 * @param integer $level
	 */
	public function setLevel($level) {
		$this->level = new LogLevel($level);
	}

	/**
	 * Add target class for logging
	 *
	 * @param string $class_nm
	 */
	public function addTargetClass($class_nm) {
		$this->target_classes[] = $class_nm;
	}

	/**
	 * Filter log item, return true to accept log
	 *
	 * @param LogItem $log
	 * @return boolean
	 */
	public function filter(LogItem $log) {
		if (! $this->isEnabled()) {
			return false;
		}
		if (! $this->filterByLevel($log->level)) {
			return false;
		}
		if (! $this->filterByClass($log->class)) {
			return false;
		}

		return true;
	}

	/**
	 * Write a log message
	 *
	 * @param LogItem $log
	 * @return void
	 */
	public function write(LogItem $log) {
		$this->doWrite($this->format($log));
	}

	/**
	 * Format log
	 *
	 * @param LogItem $log
	 * @return string
	 */
	protected function format(LogItem $log) {
		return sprintf(
			'[%s] %s: %s%s in %s on line %s',
			$log['timestamp']->format('Y/m/d H:i:s'),
			$log['level']->getSeverity(),
			(isset($log['caller']['method'])) ? $log['caller']['method'] . ': ' : '',
			$log['message'],
			$log['caller']['file'],
			$log['caller']['line']
		);
	}

	/**
	 * Write a message to the log
	 *
	 * @param string $formatted_log
	 * @return void
	 */
	abstract protected function doWrite($formatted_log);

	/**
	 * Filter log by log level
	 *
	 * @param LogLevel $level
	 * @return boolean
	 */
	protected function filterByLevel(LogLevel $level) {
		return ($level->comparePriority($this->getLevel()) >= 0);
	}

	/**
	 * Filter log by caller class
	 *
	 * @param string $class_nm
	 * @return boolean
	 */
	protected function filterByClass($class_nm) {
		if (empty($this->target_classes)) {
			return true;
		}
		return in_array($class_nm, $this->target_classes);
	}
}
