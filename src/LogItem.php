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
 */
class LogItem extends \ArrayObject {

	/**
	 * @param array $log
	 * @throws \InvalidArgumentException
	 */
	public function __construct($log) {
		assert($log['timestamp'] instanceof \DateTime);
		assert(isset($log['message']));
		assert(isset($log['class']));

		$log['level'] = new LogLevel($log['level']);

		parent::__construct($log, \ArrayObject::ARRAY_AS_PROPS);
	}

	/**
	 * @param string $key
	 * @throws \InvalidArgumentException
	 * @return mixed
	 */
	public function get($key) {
		if (! $this->offsetExists($key)) {
			throw new \InvalidArgumentException('Invalid key: ' . $key);
		}
		return parent::offsetGet($key);
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
