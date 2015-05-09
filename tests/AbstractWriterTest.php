<?php
/**
 * SimpleLogger
 *
 * @author	Jasmine
 * @link	https://github.com/jasbulilit/Logger
 * @package	SimpleLogger
 */
namespace SimpleLogger;

use SimpleLogger\Writer\MockWriter;

/**
 * @coversDefaultClass \SimpleLogger\Writer\AbstractWriter
 */
class AbstractWriterTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var \SimpleLogger\Writer\AbstractWriter
	 */
	private $_writer;

	protected function setUp() {
		$this->_writer = $this->getMockForAbstractClass('\SimpleLogger\Writer\AbstractWriter');
	}

	/**
	 * @covers ::isEnabled
	 * @covers ::setEnabled
	 */
	public function testEnabled() {
		$this->assertTrue($this->_writer->isEnabled());

		$this->_writer->setEnabled(false);
		$this->assertFalse($this->_writer->isEnabled());

		$this->_writer->setEnabled(true);
		$this->assertTrue($this->_writer->isEnabled());
	}

	/**
	 * @covers ::getLevel
	 * @covers ::setLevel
	 * @dataProvider logLevelProvider
	 */
	public function testLogLevel($level) {
		$this->_writer->setLevel($level);
		$this->assertEquals(new LogLevel($level), $this->_writer->getLevel());
	}

	/**
	 * @covers ::addTargetClass
	 * @covers ::getTargetClassList
	 */
	public function testTargetClass() {
		$target_class = array('TargetClass_1', 'TargetClass_2', 'TargetClass_3');
		foreach ($target_class as $class_nm) {
			$this->_writer->addTargetClass($class_nm);
		}
		$this->assertEquals($target_class, $this->_writer->getTargetClassList());
	}

	/**
	 * @covers ::filter
	 * @covers ::setEnabled
	 */
	public function testFilterDisabled() {
		$this->_writer->setLevel(LogLevel::DEBUG);
		$log = $this->_getDummyLog();

		$this->assertTrue($this->_writer->filter($log));

		$this->_writer->setEnabled(false);
		$this->assertFalse($this->_writer->filter($log));

		$this->_writer->setEnabled(true);
		$this->assertTrue($this->_writer->filter($log));
	}

	/**
	 * @covers ::filter
	 * @covers ::filterByLevel
	 * @dataProvider logLevelProvider
	 */
	public function testFilterByLevel($level) {
		$level_list = $this->logLevelProvider();

		$this->_writer->setLevel($level);
		$filter_level = new LogLevel($level);
		foreach ($level_list as $v) {
			$log = $this->_getDummyLog('now', pos($v));
			if ($filter_level->comparePriority($log->level) >= 0) {
				$this->assertTrue($this->_writer->filter($log));
			} else {
				$this->assertFalse($this->_writer->filter($log));
			}
		}
	}

	/**
	 * @covers ::filter
	 * @covers ::filterByClass
	 */
	public function testFilterByClass() {
		$this->_writer->setLevel(LogLevel::DEBUG);
		$log	 = $this->_getDummyLog();
		$log_foo = $this->_getDummyLog('now', LogLevel::INFO, 'Foo');
		$log_bar = $this->_getDummyLog('now', LogLevel::INFO, 'Bar');

		$this->assertTrue($this->_writer->filter($log));
		$this->assertTrue($this->_writer->filter($log_foo));
		$this->assertTrue($this->_writer->filter($log_bar));

		$this->_writer->addTargetClass('Foo');
		$this->assertFalse($this->_writer->filter($log));
		$this->assertTrue($this->_writer->filter($log_foo));
		$this->assertFalse($this->_writer->filter($log_bar));

		$this->_writer->addTargetClass('Bar');
		$this->assertFalse($this->_writer->filter($log));
		$this->assertTrue($this->_writer->filter($log_foo));
		$this->assertTrue($this->_writer->filter($log_bar));
	}

	/**
	 * @covers ::write
	 */
	public function testWrite() {
		$this->_writer->expects($this->once())->method('doWrite');
		$this->_writer->write($this->_getDummyLog());
	}

	/**
	 * @covers ::format
	 * @covers \SimpleLogger\Writer\MockWriter::getLogList
	 * @covers \SimpleLogger\Writer\MockWriter::doWrite
	 */
	public function testFormat() {
		$formatted_time = $this->_getFormattedTime();

		$log = $this->_getDummyLog($formatted_time);

		$expected_log = sprintf(
			'[%s] INFO: DummyClass::dummyMethod: Test message in DummyFile.php on line 10',
			$formatted_time
		);

		$writer = new MockWriter();
		$writer->write($log);
		$this->assertEquals($expected_log, pos($writer->getLogList()));
	}

	/**
	 * @covers ::format
	 */
	public function testFormatWithException() {
		$formatted_time = $this->_getFormattedTime();

		$exception = new \Exception('Dummy Exception');
		$log = $this->_getDummyLog($formatted_time, LogLevel::INFO, 'DummyClass', null, $exception);

		$expected_log = sprintf(
			'[%s] INFO: DummyClass::dummyMethod: Test message in DummyFile.php on line 10%s%s',
			$formatted_time,
			PHP_EOL,
			$exception->getTraceAsString()
		);

		$writer = new MockWriter();
		$writer->write($log);
		$this->assertEquals($expected_log, pos($writer->getLogList()));
	}

	/**
	 * @covers ::format
	 */
	public function testFormatWithContext() {
		$formatted_time = $this->_getFormattedTime();

		$context = array(
			'user_id'	=> 1,
			'group'		=> 'dummy group',
		);
		$log = $this->_getDummyLog($formatted_time, LogLevel::INFO, 'DummyClass', $context);

		$expected_log = sprintf(
			'[%s] INFO: DummyClass::dummyMethod: Test message%s%s in DummyFile.php on line 10',
			$formatted_time,
			PHP_EOL,
			var_export($context, true)
		);

		$writer = new MockWriter();
		$writer->write($log);
		$this->assertEquals($expected_log, pos($writer->getLogList()));
	}

	public function logLevelProvider() {
		return array(
			array(LogLevel::DEBUG),
			array(LogLevel::INFO),
			array(LogLevel::NOTICE),
			array(LogLevel::WARNING),
			array(LogLevel::ERROR),
			array(LogLevel::CRITICAL),
			array(LogLevel::ALERT),
			array(LogLevel::EMERGENCY),
		);
	}

	private function _getFormattedTime() {
		$time = new \DateTime;
		return $time->format('Y/m/d H:i:s');
	}

	private function _getDummyLog($time = 'now', $level = LogLevel::INFO, $class_nm = 'DummyClass', $context = null, $exception = null) {
		$caller = array(
			'class'		=> $class_nm,
			'method'	=> 'DummyClass::dummyMethod',
			'file'		=> 'DummyFile.php',
			'line'		=> 10,
			'args'		=> array('arg1', 'arg2')
		);
		$log = new LogItem(array(
			'timestamp'	=> new \DateTime($time),
			'level'		=> $level,
			'message'	=> 'Test message',
			'class'		=> $caller['class'],
			'caller'	=> $caller,
			'name'		=> 'DummyLoggers'
		));
		if (isset($exception)) {
			$log->exception = $exception;
		}
		if (isset($context)) {
			$log->context = $context;
		}

		return $log;
	}
}