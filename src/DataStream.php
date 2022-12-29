<?php

declare(strict_types=1);

namespace Arunabraham\TestSqliteKeyValue;

use SQLite3;

class DataStream
{
    private $columns = [];
    public function __construct(private Sqlite3 $sqlite)
    {
        $this->columns = $this->fetchColumns();
    }

    public function __invoke()
    {
        foreach ($this->arraySet() as $val) {
            var_dump($val);
        }
    }

    private function fetchColumns(): array
    {
        $arrayKey = [];
        $sqlite = $this->sqlite;
        $query = $sqlite->query($this->queryforColumns());
        while ($res = $query->fetchArray(SQLITE3_NUM)) {
            $arrayKey[$res[0]] = "";
        }
        $query->reset();
        return $arrayKey;
    }

    private function yieldAllData(): \Generator
    {
        $sqlite = $this->sqlite;
        $query = $sqlite->query($this->fetchDataQuery());
        while ($res = $query->fetchArray(SQLITE3_ASSOC)) {
            yield $res;
        }
        $query->reset();
    }

    private function queryforColumns(): string
    {
        return <<<'Query'
        SELECT key FROM data GROUP BY key ORDER BY id;
        Query;
    }

    private function fetchDataQuery(): string
    {
        return <<<'Query'
        SELECT sku, key, value FROM data ORDER BY sku, id;
        Query;
    }

    private function getColumns(): array
    {
        return $this->columns;
    }

    private function arraySet(): \Generator
    {
        $iterator = $this->yieldAllData();
        $columns = $this->getColumns();

        while ($iterator->valid()) {
            $current = function () use ($iterator) {
                return $iterator->current();
            };

            $next = function () use ($iterator) {
                $iterator->next();
                return $iterator->current();
            };

            $columns[$current()['key']] = $current()['value'];
            
            if ($current()['sku'] !== ($next()['sku'] ?? -1)) {
                yield $columns;
                unset($columns);
                $columns = $this->getColumns();
            }
        }
    }
}
