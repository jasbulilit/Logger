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