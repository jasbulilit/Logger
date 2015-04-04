<?php
/**
 * SimpleLogger
 *
 * @author jasbulilit
 */
namespace SimpleLogger;

class Logger {

	protected $writers;
	private $_name;

	public function __construct($name = null) {
		$this->_name = $name;
	}

	public function getName() {
		return $this->_name;
	}

	public function getWriters() {
		return $this->writers;
	}

	public function addWriter(WriterInterface $writer) {
		$this->writers[] = $writer;
	}

	public function emergency($message, array $context = array()) {
		$this->log(LogLevel::EMERGENCY, $message, $context);
	}

	public function alert($message, array $context = array()) {
		$this->log(LogLevel::ALERT, $message, $context);
	}

	public function critical($message, array $context = array()) {
		$this->log(LogLevel::CRITICAL, $message, $context);
	}

	public function error($message, array $context = array()) {
		$this->log(LogLevel::ERROR, $message, $context);
	}

	public function warning($message, array $context = array()) {
		$this->log(LogLevel::WARNING, $message, $context);
	}

	public function notice($message, array $context = array()) {
		$this->log(LogLevel::NOTICE, $message, $context);
	}

	public function info($message, array $context = array()) {
		$this->log(LogLevel::INFO, $message, $context);
	}

	public function debug($message, array $context = array()) {
		$this->log(LogLevel::DEBUG, $message, $context);
	}

	public function log($level, $message, array $context = array()) {
		$this->_assertMessage($message);
		if (is_array($message)) {
			$message = var_export($message, true);
		}

		$caller = $this->_getCaller();

		$log = new LogItem(array(
			'timestamp'	=> new \DateTime(),
			'level'		=> $level,
			'message'	=> $this->interpolate((string) $message, $context),
			'context'	=> $context,
			'class'		=> $caller['class'],
			'caller'	=> $caller,
			'name'		=> $this->getName()
		));

		// notify to writer
		foreach ($this->getWriters() as $writer) {
			if ($writer->filter($log)) {
				$writer->write($log);
			}
		}
	}

	protected function interpolate($message, array $context = array()) {
		$replace = array();
		foreach ($context as $key => $value) {
			$replace['{' . $key . '}'] = $value;
		}
		return strtr($message, $replace);
	}

	private function _assertMessage($message) {
		assert(! is_object($message) || method_exists($message, '__toString'));
	}

	private function _getCaller() {
		$trace_list = debug_backtrace(false);
		array_shift($trace_list);	// _getCaller()

		$caller = array();

		$trace = array_shift($trace_list);	// log()
		if (isset($trace_list[0])
			&& $trace_list[0]['class'] == __CLASS__) {
			$trace = array_shift($trace_list);
		}
		$caller['file'] = $trace['file'];
		$caller['line'] = $trace['line'];

		if (isset($trace_list[0])) {
			$trace = array_shift($trace_list);

			$method = '';
			if (isset($trace['class'])) {
				$caller['class'] = $trace['class'];
				$method = $trace['class'] . '::';
			}
			$method .= $trace['function'];

			$caller['method'] = $method;
			$caller['args'] = $trace['args'];
		}

		return $caller;
	}
}