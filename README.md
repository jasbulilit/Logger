# SimpleLogger
[![Build Status](https://travis-ci.org/jasbulilit/Logger.svg?branch=master)](https://travis-ci.org/jasbulilit/Logger)
[![Code Coverage](https://scrutinizer-ci.com/g/jasbulilit/Logger/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/jasbulilit/Logger/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/jasbulilit/Logger/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/jasbulilit/Logger/?branch=master)

最低限の機能だけのシンプルなロガー

## Usage
``` php
require_once 'vendor/autoload.php';

$logger_name = 'sample';
$writer = new \SimpleLogger\Writer\FileWriter('/path/to/log_file');
$logger = new \SimpleLogger\Logger($logger_name, $writer);

$logger->emergency('emergency message');
$logger->alert('alert message');
$logger->critical('critical message');
$logger->error('error message');
$logger->warning('warning message');
$logger->notice('notice message');
$logger->info('info message');
$logger->debug('debug message');
```
