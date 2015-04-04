<?php
/**
 * SimpleLogger
 *
 * @author jasbulilit
 */
namespace SimpleLogger;

interface WriterInterface {
	public function filter(LogItem $log);	// true to accept log
	public function write(LogItem $log);
}
