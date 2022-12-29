<?php

use Arunabraham\TestSqliteKeyValue\Logger\LoggerInfoInterface;
use Monolog\Handler\StreamHandler;
use SQLite3;
use Monolog\Logger;

return [
    SQLite3::class => DI\autowire()
                            ->constructor(
                                'test.db',
                                SQLITE3_OPEN_READWRITE
                            ),
    LoggerInfoInterface::class => DI\create(Logger::class)
                                ->constructor(
                                    'app.log',
                                    (new StreamHandler('php://stdout', \Monolog\Level::Info))
                                )
];
