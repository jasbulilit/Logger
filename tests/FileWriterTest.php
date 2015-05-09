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
 * @coversDefaultClass \SimpleLogger\Writer\FileWriter
 */
class FileWriterTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @covers ::__construct
	 */
	public function testConstructor() {
		$writer = new Writer\FileWriter('php://stdout');
		$this->assertEquals(LogLevel::INFO, $writer->getLevel()->getLevel());
	}

	/**
	 * @covers ::__construct
	 */
	public function testConstructorWithStream() {
		$stream = fopen('php://stdout', 'a');
		$writer = new Writer\FileWriter($stream);
	}

	/**
	 * @covers ::__construct
	 * @expectedException \RuntimeException
	 */
	public function testConstructorWithNonWritableStream() {
		$this->markTestIncomplete();

		$writer = new Writer\FileWriter('php://stdin');
		$writer->write(new LogItem($this->_getDummyLog()));
	}

	private function _getDummyLog() {
		return array(
			'timestamp'	=> new \DateTime(),
			'level'		=> LogLevel::INFO,
			'message'	=> 'Test message',
			'class'		=> null,
			'name'		=> 'DummyLoggers'
		);
	}
}