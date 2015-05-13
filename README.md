# SimpleLogger
[![Build Status](https://travis-ci.org/jasbulilit/Logger.svg?branch=master)](https://travis-ci.org/jasbulilit/Logger)
[![Code Coverage](https://scrutinizer-ci.com/g/jasbulilit/Logger/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/jasbulilit/Logger/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/jasbulilit/Logger/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/jasbulilit/Logger/?branch=master)

最低限の機能だけのシンプルなロガー
- ファイル(Stream)を指定してログを記録
- 呼び出し元を自動判定し、ログに情報を付与
- Writer毎にログレベル、呼び出し元クラス名でログのフィルタリングが可能
- Psr\Log\LoggerInterface(PSR-3)を実装

## Usage
``` php
require_once 'vendor/autoload.php';

use SimpleLogger\Logger;
use SimpleLogger\LogLevel;
use SimpleLogger\Writer\FileWriter;

$logger_name = 'sample';
$writer = new FileWriter('/path/to/log_file', LogLevel::DEBUG);
$logger = new Logger($logger_name, $writer);

$logger->emergency('emergency message');
$logger->alert('alert message');
$logger->critical('critical message');
$logger->error('error message');
$logger->warning('warning message');
$logger->notice('notice message');
$logger->info('info message');
$logger->debug('debug message');
```

## Requirements
PHP5.3.3+
