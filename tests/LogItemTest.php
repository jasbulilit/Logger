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
 * @coversDefaultClass \SimpleLogger\LogItem
 */
class LogItemTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @covers ::__construct
	 */
	public function testConstructor() {
		$log = $this->_getDummyLog();
		$log_item = new LogItem($log);

		foreach ($log as $key => $val) {
			if ($key == 'level') {
				$this->assertEquals($log[$key], $log_item->level->getLevel());
			} else {
				$this->assertEquals($log[$key], $log_item->{$key});
			}
		}
	}

	/**
	 * @covers ::__construct
	 * @expectedException \InvalidArgumentException
	 */
	public function testConstructorWithInvalidLogLevel() {
		$log = $this->_getDummyLog();
		$log['level'] = 'a';

		$log_item = new LogItem($log);
	}

	/**
	 * @covers ::offsetGet
	 */
	public function testOffsetGet() {
		$log = $this->_getDummyLog();
		$log_item = new LogItem($log);

		foreach ($log as $key => $val) {
			if ($key == 'level') {
				$this->assertEquals($log[$key], $log_item->level->getLevel());
			} else {
				$this->assertEquals($log[$key], $log_item->offsetGet($key));
			}
		}
	}

	/**
	 * @covers ::offsetGet
	 * @expectedException \InvalidArgumentException
	 */
	public function testOffsetGetWithUndefinedOffset() {
		$log = $this->_getDummyLog();
		$log_item = new LogItem($log);
		$log_item->offsetGet('foo');
	}

	/**
	 * @covers ::get
	 * @covers ::set
	 * @covers ::has
	 * @dataProvider itemProvider
	 */
	public function testGetAndSetItem($key, $value) {
		$log = $this->_getDummyLog();
		$log_item = new LogItem($log);

		$log_item->set($key, $value);
		$this->assertEquals($value, $log_item->get($key));
		$this->assertTrue($log_item->has($key));
	}

	/**
	 * @covers ::has
	 */
	public function testHasItem() {
		$log = $this->_getDummyLog();
		$log_item = new LogItem($log);

		$this->assertFalse($log_item->has('foo'));

		$log_item->foo = 'bar';
		$this->assertTrue($log_item->has('foo'));
	}

	public function itemProvider() {
		return array(
			array('string', 'dummy'),
			array('integer', 12345),
			array('boolean', true),
			array('array', array('Hoge', 'Foo', 'Bar')),
			array('object', new \EmptyIterator()),
			array('null', null),
			array('zero', 0),
			array('empty_str', ''),
			array('empty_array', array())
		);
	}

	private function _getDummyLog() {
		$caller = array(
			'class'		=> 'DummyClass',
			'method'	=> 'DummyClass::dummyMethod',
			'file'		=> 'DummyFile.php',
			'line'		=> 10,
			'args'		=> array('arg1', 'arg2')
		);
		return array(
			'timestamp'	=> new \DateTime(),
			'level'		=> LogLevel::INFO,
			'message'	=> 'Test message',
			'class'		=> $caller['class'],
			'caller'	=> $caller,
			'name'		=> 'DummyLoggers'
		);
	}
}
