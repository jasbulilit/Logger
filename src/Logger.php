<?php
/**
 * SimpleLogger
 *
 * @author	Jasmine
 * @link	https://github.com/jasbulilit/Logger
 * @package	SimpleLogger
 */
namespace SimpleLogger;

use Psr\Log\LoggerInterface;

class Logger implements LoggerInterface {

	/**
	 * @var array
	 */
	protected $writers;

	/**
	 * @var string
	 */
	private $_name;

	private $_caller_skip_count = 0;

	/**
	 * @param string $name	logger channel name
	 */
	public function __construct($name = null, WriterInterface $writer = null) {
		$this->_name = $name;

		if (isset($writer)) {
			$this->addWriter($writer);
		}
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->_name;
	}

	/**
	 * @return WriterInterface
	 */
	public function getWriters() {
		return $this->writers;
	}

	/**
	 * Add writer
	 *
	 * @param WriterInterface $writer
	 */
	public function addWriter(WriterInterface $writer) {
		$this->writers[] = $writer;
	}

	/**
	 * @param integer $skip_count
	 */
	public function addCallerSkipCount($skip_count) {
		$this->_caller_skip_count += $skip_count;
	}

	/**
	 * @return integer
	 */
	public function getCallerSkipCount() {
		return $this->_caller_skip_count;
	}

	/**
	 * Add a emergency level message as a log entry
	 *
	 * @param string $message
	 * @param array $context
	 * @return void
	 */
	public function emergency($message, array $context = array()) {
		$this->log(LogLevel::EMERGENCY, $message, $context);
	}

	/**
	 * Add a alert level message as a log entry
	 *
	 * @param string $message
	 * @param array $context
	 * @return void
	 */
	public function alert($message, array $context = array()) {
		$this->log(LogLevel::ALERT, $message, $context);
	}

	/**
	 * Add a critical level message as a log entry
	 *
	 * @param string $message
	 * @param array $context
	 * @return void
	 */
	public function critical($message, array $context = array()) {
		$this->log(LogLevel::CRITICAL, $message, $context);
	}

	/**
	 * Add a error level message as a log entry
	 *
	 * @param string $message
	 * @param array $context
	 * @return void
	 */
	public function error($message, array $context = array()) {
		$this->log(LogLevel::ERROR, $message, $context);
	}

	/**
	 * Add a warning level message as a log entry
	 *
	 * @param string $message
	 * @param array $context
	 * @return void
	 */
	public function warning($message, array $context = array()) {
		$this->log(LogLevel::WARNING, $message, $context);
	}

	/**
	 * Add a notice level message as a log entry
	 *
	 * @param string $message
	 * @param array $context
	 * @return void
	 */
	public function notice($message, array $context = array()) {
		$this->log(LogLevel::NOTICE, $message, $context);
	}

	/**
	 * Add a info level message as a log entry
	 *
	 * @param string $message
	 * @param array $context
	 * @return void
	 */
	public function info($message, array $context = array()) {
		$this->log(LogLevel::INFO, $message, $context);
	}

	/**
	 * Add a debug level message as a log entry
	 *
	 * @param string $message
	 * @param array $context
	 * @return void
	 */
	public function debug($message, array $context = array()) {
		$this->log(LogLevel::DEBUG, $message, $context);
	}

	/**
	 * Add a message as a log entry
	 *
	 * @param integer $level
	 * @param string $message
	 * @param array $context
	 * @return void
	 */
	public function log($level, $message, array $context = array()) {
		assert(! is_object($message) || method_exists($message, '__toString'));
		if (is_array($message)) {
			$message = var_export($message, true);
		}

		$caller = $this->_getCaller();

		$log = new LogItem(array(
			'timestamp'	=> new \DateTime(),
			'level'		=> $level,
			'message'	=> $this->interpolate((string) $message, $context),
			'class'		=> $caller['class'],
			'caller'	=> $caller,
			'name'		=> $this->getName()
		));
		if (isset($context['exception'])
			&& $context['exception'] instanceof \Exception) {
			$log->set('exception', $context['exception']);
			unset($context['exception']);
		}
		if (! empty($context)) {
			$log->set('context', $context);
		}

		// notify to writer
		foreach ($this->getWriters() as $writer) {
			if ($writer->filter($log)) {
				$writer->write($log);
			}
		}
	}

	/**
	 * Interpolates context values into the message placeholders.
	 *
	 * @param string $message
	 * @param array $context
	 * @return string
	 * @link http://www.php-fig.org/psr/psr-3/
	 */
	protected function interpolate($message, array $context = array()) {
		$replace = array();
		foreach ($context as $key => $value) {
			$replace['{' . $key . '}'] = $value;
		}
		return strtr($message, $replace);
	}

	/**
	 * Get caller info
	 *
	 * @return array
	 * 	The possible returned elements are file/line/class/method/args
	 */
	private function _getCaller() {
		$trace_list = debug_backtrace(false);
		array_shift($trace_list);	// _getCaller()

		$trace	= array_shift($trace_list);	// log()
		if (isset($trace_list[0])
			&& $trace_list[0]['class'] == __CLASS__) {
			// methods of the eight log levels
			$trace = array_shift($trace_list);
		}

		for ($i=0; $i<$this->_caller_skip_count; $i++) {
			$trace = array_shift($trace_list);
		}

		$caller	= array(
			'file'		=> $trace['file'],
			'line'		=> $trace['line'],
			'class'		=> null,
			'method'	=> null,
			'args'		=> null
		);
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