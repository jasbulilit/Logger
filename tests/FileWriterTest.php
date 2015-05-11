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
	 * @covers ::doWrite
	 */
	public function testDoWrite() {
		$stream = tmpfile();
		$writer = new Writer\FileWriter($stream);

		for ($i=1; $i<=5; $i++) {
			$writer->write(new LogItem($this->_getDummyLog('Test message ' . $i)));
		}

		fseek($stream, 0);
		for ($i=1; $i<=5; $i++) {
			$this->assertContains('Test message ' . $i, fgets($stream));
		}
	}

	private function _getDummyLog($message = 'Test message') {
		return array(
			'timestamp'	=> new \DateTime(),
			'level'		=> LogLevel::INFO,
			'message'	=> $message,
			'class'		=> null,
			'name'		=> 'DummyLoggers'
		);
	}
}