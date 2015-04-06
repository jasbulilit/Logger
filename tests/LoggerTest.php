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
		$writer = new Writer\MockWriter(LogLevel::DEBUG);

		$logger = new Logger();
		$logger->addWriter($writer);

		$writers = $logger->getWriters();

		$this->assertEquals($writer, $writers[0]);
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
				return true;
			}));

		$logger = new Logger();
		$logger->addWriter($writer);
		$logger->log($level, $message);
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
				return true;
			}));

		$logger = new Logger();
		$logger->addWriter($writer);
		$logger->{$method}($message);
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