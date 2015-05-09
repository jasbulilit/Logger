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
 * Log item work as array
 *
 * @property \DateTime $timestamp
 * @property LogLevel $level
 * @property string $message
 * @property string $class
 * @property array $caller
 * @property string $name
 * @property array $context
 * @property \Exception $exception
 */
class LogItem extends \ArrayObject {

	/**
	 * @param array $log
	 * @throws \InvalidArgumentException
	 */
	public function __construct($log) {
		assert($log['timestamp'] instanceof \DateTime);
		assert(isset($log['message']));
		assert(array_key_exists('class', $log));

		$log['level'] = new LogLevel($log['level']);

		parent::__construct($log, \ArrayObject::ARRAY_AS_PROPS);
	}

	/**
	 * @param string $key
	 * @throws \InvalidArgumentException
	 * @return mixed
	 */
	public function offsetGet($key) {
		if (! $this->offsetExists($key)) {
			throw new \InvalidArgumentException('Invalid key: ' . $key);
		}
		return parent::offsetGet($key);
	}

	/**
	 * Alias of offsetGet()
	 *
	 * @param string $key
	 * @throws \InvalidArgumentException
	 * @return mixed
	 */
	public function get($key) {
		return $this->offsetGet($key);
	}

	/**
	 * Alias of offsetSet()
	 *
	 * @param string $key
	 * @param mixed $value
	 * @return void
	 */
	public function set($key, $value) {
		$this->offsetSet($key, $value);
	}

	/**
	 * Check if LogItem has the givenkey
	 *
	 * @param string $key
	 * @return boolean
	 */
	public function has($key) {
		return $this->offsetExists($key);
	}
}
