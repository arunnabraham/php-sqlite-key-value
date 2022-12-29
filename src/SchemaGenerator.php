<?php

declare(strict_types=1);

namespace Arunabraham\TestSqliteKeyValue;

use Exception;
use SQLite3;

class SchemaGenerator
{
    public function __invoke()
    {
        $sqlite = new SQLite3(
            __DIR__ . '/../test.db'
        );
        try {
            $sqlite->exec($this->defineQuery());
        } catch(Exception $e) {
            echo $e->getMessage().PHP_EOL;
        }
    }

    private function defineQuery()
    {
        return <<<'Query'
        CREATE table data (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            sku INTEGER  NOT NULL,
            key TEXT NOT NULL,
            value TEXT NOT NULL,
            source TEXT NOT NULL
        );
        CREATE INDEX sku_key on data(sku);
        CREATE INDEX result_key on data(key);
        CREATE INDEX source_key on data(source);
        Query;
    }
}
