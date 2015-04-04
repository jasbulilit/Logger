<?php
/**
 * SimpleLogWriter
 *
 * @author jasbulilit
 */
namespace SimpleLogger;

class FileWriter extends AbstractWriter {

	private $_stream;

	public function __construct($streamOrFile, $level = LogLevel::INFO) {
		if (is_resource($streamOrFile)) {
			if ('stream' != get_resource_type($streamOrFile)) {
				throw new \InvalidArgumentException('Invalid resource type.');
			}
			$this->_stream = $streamOrFile;
		} else {
			$this->stream = fopen($streamOrFile, 'a');
			if (! $this->stream) {
				throw new \RuntimeException('Failed to open file: ' . $streamOrFile);
			}
		}

		$this->setLevel($level);
	}

	protected function format(LogItem $log) {
		return sprintf(
			'[%s] %s: %s%s in %s on line %s',
			$log['timestamp']->format('Y/m/d H:i:s'),
			$log['level']->getName(),
			(isset($log['caller']['method'])) ? $log['caller']['method'] . ': ' : '',
			$log['message'],
			$log['caller']['file'],
			$log['caller']['line']
		);
	}

	protected function doWrite($formatted_log) {
		fwrite($this->stream,  $formatted_log. PHP_EOL);
	}
}