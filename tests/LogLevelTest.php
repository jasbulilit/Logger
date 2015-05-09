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
 * @coversDefaultClass \SimpleLogger\LogLevel
 */
class LogLevelTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @covers ::__construct
	 * @covers ::getLevel
	 * @dataProvider logLevelProvider
	 */
	public function testConstructor($level) {
		$log_level = new LogLevel($level);
		$this->assertEquals($level, $log_level->getLevel());
	}

	/**
	 * @covers ::__construct
	 * @expectedException \InvalidArgumentException
	 */
	public function testConstructorWithInvalidLogLevel() {
		new LogLevel(9);
	}

	/**
	 * @covers ::getSeverity
	 * @dataProvider logLevelProvider
	 */
	public function testGetSeverity($level, $serverity) {
		$log_level = new LogLevel($level);
		$this->assertEquals($serverity, $log_level->getSeverity());
	}

	/**
	 * @covers ::getPriority
	 * @dataProvider logLevelProvider
	 */
	public function testGetPriority($level) {
		$log_level = new LogLevel($level);
		$this->assertEquals($level, $log_level->getPriority());
	}

	/**
	 * @covers ::comparePriority
	 * @dataProvider logLevelProvider
	 */
	public function testComparePriority($level) {
		$log_level	= new LogLevel($level);

		$this->assertEquals(0, $log_level->comparePriority(new LogLevel($level)));

		if ($level !== 8) {
			$this->assertEquals(-1, $log_level->comparePriority(new LogLevel($level + 1)));
		}
		if ($level !== 1) {
			$this->assertEquals(1, $log_level->comparePriority(new LogLevel($level - 1)));
		}
	}

	public function logLevelProvider() {
		return array(
			array(8, 'DEBUG'),
			array(7, 'INFO'),
			array(6, 'NOTICE'),
			array(5, 'WARNING'),
			array(4, 'ERROR'),
			array(3, 'CRITICAL'),
			array(2, 'ALERT'),
			array(1, 'EMERGENCY'),
		);
	}
}
