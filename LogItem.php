<?php
/**
 * SimpleLogger
 *
 * @author jasbulilit
 */
namespace SimpleLogger;

class LogItem extends \ArrayObject {
	public function __construct($log) {
		assert($log['timestamp'] instanceof \DateTime);
		assert(isset($log['message']));
		assert(isset($log['class']));

		$log['level'] = new LogLevel($log['level']);

		parent::__construct($log, \ArrayObject::ARRAY_AS_PROPS);
	}
}
