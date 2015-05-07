<?php
/**
 * SimpleLogger
 *
 * @author	Jasmine
 * @link	https://github.com/jasbulilit/Logger
 * @package	SimpleLogger
 */
namespace SimpleLogger\Writer;

use SimpleLogger\WriterInterface;
use SimpleLogger\LogLevel;
use SimpleLogger\LogItem;

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
	 * @param array
	 */
	public function getTargetClassList() {
		return $this->target_classes;
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
		$method = '';
		$caller_info = '';
		if ($log->has('caller')) {
			$caller = $log->caller;

			if (isset($caller['method'])) {
				$method = $caller['method'] . ': ';
			}
			$caller_info = sprintf(' in %s on line %s', $caller['file'], $caller['line']);
		}

		$context_info = '';
		$trace = '';
		if ($log->has('context')) {
			$context = $log->context;
			if (isset($context['exception'])
				&& $context['exception'] instanceof \Exception) {
				$trace = PHP_EOL . $context['exception']->getTraceAsString();
				unset($context['exception']);
			}
			$context_info = PHP_EOL . var_export($context, true);
		}

		return sprintf(
			'[%s] %s: %s%s%s%s%s',
			$log->timestamp->format('Y/m/d H:i:s'),
			$log->level->getSeverity(),
			$method,
			$log->message,
			$context_info,
			$caller_info,
			$trace
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
