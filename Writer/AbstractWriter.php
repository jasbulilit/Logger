<?php
/**
 * SimpleLogWriter
 *
 * @author jasbulilit
 */
namespace SimpleLogger;

abstract class AbstractWriter implements WriterInterface {

	/**
	 * @var LogLevel
	 */
	protected $level;

	protected $enabled = true;

	protected $target_classes = array();

	public function isEnabled() {
		return $this->enabled;
	}

	public function setEnabled($enabled) {
		$this->enabled = (boolean) $enabled;
	}

	public function getLevel() {
		return $this->level;
	}

	public function setLevel($level) {
		$this->level = new LogLevel($level);
	}

	public function addTargetClass($class_nm) {
		$this->target_classes[] = $class_nm;
	}

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

	public function write(LogItem $log) {
		$this->doWrite($this->format($log));
	}

	abstract protected function format(LogItem $log);

	abstract protected function doWrite($formatted_log);

	protected function filterByLevel(LogLevel $level) {
		return ($level->comparePriority($this->getLevel()) >= 0);
	}

	protected function filterByClass($class_nm) {
		if (empty($this->target_classes)) {
			return true;
		}
		return in_array($class_nm, $this->target_classes);
	}
}
