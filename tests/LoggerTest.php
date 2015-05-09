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
 * @coversDefaultClass \SimpleLogger\Logger
 */
class LoggerTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @covers ::__construct
	 */
	public function testConstructorWithWriter() {
		$writer = $this->getMock('\SimpleLogger\WriterInterface');
		$logger = new Logger('hoge', $writer);

		$writers = $logger->getWriters();
		$this->assertEquals($writer, $writers[0]);
	}

	/**
	 * @covers ::__construct
	 * @covers ::getName
	 */
	public function testGetName() {
		$logger = new Logger('hoge');
		$this->assertEquals('hoge', $logger->getName());
	}

	/**
	 * @covers ::addWriter
	 * @covers ::getWriters
	 */
	public function testAddWriter() {
		$writer = $this->getMock('\SimpleLogger\WriterInterface');

		$logger = new Logger();
		$logger->addWriter($writer);

		$writers = $logger->getWriters();

		$this->assertEquals($writer, $writers[0]);
	}

	/**
	 * @covers ::addCallerSkipCount
	 * @covers ::getCallerSkipCount
	 */
	public function testCallerSkipCount() {
		$logger = new Logger();
		$this->assertEquals(0, $logger->getCallerSkipCount());

		$logger->addCallerSkipCount(1);
		$this->assertEquals(1, $logger->getCallerSkipCount());

		$logger->addCallerSkipCount(2);
		$this->assertEquals(3, $logger->getCallerSkipCount());

		$logger->addCallerSkipCount(3);
		$this->assertEquals(6, $logger->getCallerSkipCount());
	}

	/**
	 * @dataProvider logMethodProvider
	 * @covers ::log
	 */
	public function testLog($method, $level) {
		$message = 'Test message';

		$writer = $this->getMock('\SimpleLogger\WriterInterface');
		$writer->method('filter')->willReturn(true);
		$writer->expects($this->once())
			->method('write')
			->with($this->callback(function(LogItem $log) use ($level, $message) {
				if ($log->level->getLevel() != $level) {
					return false;
				}
				if ($log->message != $message) {
					return false;
				}
				if ($log->has('context')) {
					return false;
				}
				return true;
			}));

		$logger = new Logger();
		$logger->addWriter($writer);
		$logger->log($level, $message);
	}

	/**
	 * @dataProvider logMethodProvider
	 * @covers ::log
	 * @covers ::interpolate
	 */
	public function testLogWithContext($method, $level) {
		$message = 'Test message Group={group} User={user_id}';
		$context = array(
				'user_id'	=> 1,
				'group'		=> 'dummy group'
		);
		$expected_message = "Test message Group={$context['group']} User={$context['user_id']}";

		$writer = $this->getMock('\SimpleLogger\WriterInterface');
		$writer->method('filter')->willReturn(true);
		$writer->expects($this->once())
			->method('write')
			->with($this->callback(function(LogItem $log) use ($level, $expected_message) {
				if ($log->level->getLevel() != $level) {
					return false;
				}
				if ($log->message != $expected_message) {
					return false;
				}
				if (! $log->has('context')) {
					return false;
				}
				return true;
			}));

		$logger = new Logger();
		$logger->addWriter($writer);
		$logger->log($level, $message, $context);
	}

	/**
	 * @covers ::log
	 */
	public function testLogWithException() {
		$exception = new \Exception('Dummy Exception');

		$test_case = $this;
		$writer = $this->getMock('\SimpleLogger\WriterInterface');
		$writer->method('filter')->willReturn(true);
		$writer->expects($this->once())
			->method('write')
			->with($this->callback(function(LogItem $log) use ($test_case, $exception) {
				if ($log->has('context')) {
					return false;
				}
				if (! $log->has('exception')) {
					return false;
				}

				$test_case->assertEquals($exception, $log->exception);
				return true;
			}));

		$logger = new Logger();
		$logger->addWriter($writer);
		$logger->log(LogLevel::INFO, 'Test message', array('exception' => $exception));
	}

	/**
	 * @covers ::log
	 */
	public function testLogWithArrayMessage() {
		$message = array(
			'foo'	=> 'FOO',
			'bar'	=> 'BAR'
		);

		$writer = $this->getMock('\SimpleLogger\WriterInterface');
		$writer->method('filter')->willReturn(true);
		$writer->expects($this->once())
			->method('write')
			->with($this->callback(function(LogItem $log) use ($message) {
				if ($log->message != var_export($message, true)) {
					return false;
				}
				return true;
			}));

		$logger = new Logger();
		$logger->addWriter($writer);
		$logger->log(LogLevel::INFO, $message);
	}

	/**
	 * @covers ::_getCaller
	 */
	public function testGetCallerMethod() {
		$message = 'Test message';

		$test_case = $this;
		$writer = $this->getMock('\SimpleLogger\WriterInterface');
		$writer->method('filter')
			->willReturn(true)
			->with($this->callback(function(LogItem $log) use ($test_case) {
				$context = $log->context;
				$caller  = $log->caller;

				$test_case->assertEquals($context['class'], $caller['class'], 'Caller class is not expected.');
				$test_case->assertEquals($context['method'], $caller['method'], 'Caller method is not expected.');
				$test_case->assertEquals($context['line'], $caller['line'], 'Caller line is not expected.');

				return true;
			}));

		$logger = new Logger();
		$logger->addWriter($writer);
		$logger->log(1, $message, array('class' => __CLASS__, 'method' => __METHOD__, 'line' => __LINE__));
		$logger->log(2, $message, array('class' => __CLASS__, 'method' => __METHOD__, 'line' => __LINE__));
		$logger->log(3, $message, array('class' => __CLASS__, 'method' => __METHOD__, 'line' => __LINE__));
		$logger->log(4, $message, array('class' => __CLASS__, 'method' => __METHOD__, 'line' => __LINE__));
		$logger->log(5, $message, array('class' => __CLASS__, 'method' => __METHOD__, 'line' => __LINE__));
		$logger->log(6, $message, array('class' => __CLASS__, 'method' => __METHOD__, 'line' => __LINE__));
		$logger->log(7, $message, array('class' => __CLASS__, 'method' => __METHOD__, 'line' => __LINE__));
		$logger->log(8, $message, array('class' => __CLASS__, 'method' => __METHOD__, 'line' => __LINE__));
		$logger->emergency($message, array('class' => __CLASS__, 'method' => __METHOD__, 'line' => __LINE__));
		$logger->alert($message, array('class' => __CLASS__, 'method' => __METHOD__, 'line' => __LINE__));
		$logger->critical($message, array('class' => __CLASS__, 'method' => __METHOD__, 'line' => __LINE__));
		$logger->error($message, array('class' => __CLASS__, 'method' => __METHOD__, 'line' => __LINE__));
		$logger->warning($message, array('class' => __CLASS__, 'method' => __METHOD__, 'line' => __LINE__));
		$logger->notice($message, array('class' => __CLASS__, 'method' => __METHOD__, 'line' => __LINE__));
		$logger->info($message, array('class' => __CLASS__, 'method' => __METHOD__, 'line' => __LINE__));
		$logger->debug($message, array('class' => __CLASS__, 'method' => __METHOD__, 'line' => __LINE__));
	}

	/**
	 * @covers ::_getCaller
	 */
	public function testGetCallerWithWrapper() {
		$message = 'Test message';

		$test_case = $this;
		$writer = $this->getMock('\SimpleLogger\WriterInterface');
		$writer->method('filter')
			->willReturn(true)
			->with($this->callback(function(LogItem $log) use ($test_case) {
				$context = $log->context;
				$caller  = $log->caller;

				$test_case->assertEquals($context['class'], $caller['class'], 'Caller class is not expected.');
				$test_case->assertEquals($context['method'], $caller['method'], 'Caller method is not expected.');
				$test_case->assertEquals($context['line'], $caller['line'], 'Caller line is not expected.');

				return true;
			}));

		LoggerWrapper::setLogger(new Logger('dummy', $writer));
		LoggerWrapper::log(1, $message, array('class' => __CLASS__, 'method' => __METHOD__, 'line' => __LINE__));
		LoggerWrapper::log(2, $message, array('class' => __CLASS__, 'method' => __METHOD__, 'line' => __LINE__));
		LoggerWrapper::log(3, $message, array('class' => __CLASS__, 'method' => __METHOD__, 'line' => __LINE__));
		LoggerWrapper::log(4, $message, array('class' => __CLASS__, 'method' => __METHOD__, 'line' => __LINE__));
		LoggerWrapper::log(5, $message, array('class' => __CLASS__, 'method' => __METHOD__, 'line' => __LINE__));
		LoggerWrapper::log(6, $message, array('class' => __CLASS__, 'method' => __METHOD__, 'line' => __LINE__));
		LoggerWrapper::log(7, $message, array('class' => __CLASS__, 'method' => __METHOD__, 'line' => __LINE__));
		LoggerWrapper::log(8, $message, array('class' => __CLASS__, 'method' => __METHOD__, 'line' => __LINE__));
		LoggerWrapper::emergency($message, array('class' => __CLASS__, 'method' => __METHOD__, 'line' => __LINE__));
		LoggerWrapper::alert($message, array('class' => __CLASS__, 'method' => __METHOD__, 'line' => __LINE__));
		LoggerWrapper::critical($message, array('class' => __CLASS__, 'method' => __METHOD__, 'line' => __LINE__));
		LoggerWrapper::error($message, array('class' => __CLASS__, 'method' => __METHOD__, 'line' => __LINE__));
		LoggerWrapper::warning($message, array('class' => __CLASS__, 'method' => __METHOD__, 'line' => __LINE__));
		LoggerWrapper::notice($message, array('class' => __CLASS__, 'method' => __METHOD__, 'line' => __LINE__));
		LoggerWrapper::info($message, array('class' => __CLASS__, 'method' => __METHOD__, 'line' => __LINE__));
		LoggerWrapper::debug($message, array('class' => __CLASS__, 'method' => __METHOD__, 'line' => __LINE__));
	}

	/**
	 * @dataProvider logMethodProvider
	 * @covers ::log
	 */
	public function testLogFilter($method, $level) {
		$message = 'Test message';

		$writer = $this->getMock('\SimpleLogger\WriterInterface');
		$writer->method('filter')->willReturn(false);
		$writer->expects($this->never())->method('write');

		$logger = new Logger();
		$logger->addWriter($writer);
		$logger->log($level, $message);
	}

	/**
	 * @dataProvider logMethodProvider
	 * @covers ::debug
	 * @covers ::info
	 * @covers ::notice
	 * @covers ::warning
	 * @covers ::error
	 * @covers ::critical
	 * @covers ::alert
	 * @covers ::emergency
	 */
	public function testLogMethods($method, $level) {
		$message = 'Test message';

		$writer = $this->getMock('\SimpleLogger\WriterInterface');
		$writer->method('filter')->willReturn(true);
		$writer->expects($this->once())
			->method('write')
			->with($this->callback(function(LogItem $log) use ($level, $message) {
				if ($log->level->getLevel() != $level) {
					return false;
				}
				if ($log->message != $message) {
					return false;
				}
				if ($log->has('context')) {
					return false;
				}
				return true;
			}));

		$logger = new Logger();
		$logger->addWriter($writer);
		$logger->{$method}($message);
	}

	/**
	 * @dataProvider logMethodProvider
	 * @covers ::debug
	 * @covers ::info
	 * @covers ::notice
	 * @covers ::warning
	 * @covers ::error
	 * @covers ::critical
	 * @covers ::alert
	 * @covers ::emergency
	 */
	public function testLogMethodsWithContext($method, $level) {
		$message = 'Test message Group={group} User={user_id}';
		$context = array(
			'user_id'	=> 1,
			'group'		=> 'dummy group'
		);
		$expected_message = "Test message Group={$context['group']} User={$context['user_id']}";

		$writer = $this->getMock('\SimpleLogger\WriterInterface');
		$writer->method('filter')->willReturn(true);
		$writer->expects($this->once())
			->method('write')
			->with($this->callback(function(LogItem $log) use ($level, $expected_message) {
				if ($log->level->getLevel() != $level) {
					return false;
				}
				if ($log->message != $expected_message) {
					return false;
				}
				if (! $log->has('context')) {
					return false;
				}
				return true;
			}));

		$logger = new Logger();
		$logger->addWriter($writer);
		$logger->{$method}($message, $context);
	}

	public function logMethodProvider() {
		return array(
			array('debug',		LogLevel::DEBUG),
			array('info',		LogLevel::INFO),
			array('notice',		LogLevel::NOTICE),
			array('warning',	LogLevel::WARNING),
			array('error',		LogLevel::ERROR),
			array('critical',	LogLevel::CRITICAL),
			array('alert',		LogLevel::ALERT),
			array('emergency',	LogLevel::EMERGENCY),
		);
	}
}

class LoggerWrapper {
	private static $_logger;

	public static function __callStatic($method_nm, $args) {
		if ($method_nm == 'log') {
			self::$_logger->$method_nm($args[0], $args[1], $args[2]);
		} else {
			self::$_logger->$method_nm($args[0], $args[1]);
		}
	}

	public static function setLogger($logger) {
		// __callStatic() call self static method internally
		$logger->addCallerSkipCount(2);
		self::$_logger = $logger;
	}
}