<?php

declare(strict_types=1);

namespace Arunabraham\TestSqliteKeyValue;

use Exception;
use Generator;
use SQLite3;

class InsertData
{
    public function __construct(private Sqlite3 $sqlite)
    {
    }

    public function __invoke(Generator $iterator)
    {
            try {
                $this->sqlite->exec('BEGIN;');
                foreach ($iterator as $info) {
                    [$sku, $index, $contents] = explode("[~sep~]", $info);
                    foreach(json_decode($contents, true) as $key => $data)
                    {
                        $this->sqlite->exec(sprintf($this->defineQuery(), (int)$sku, $key, (string)$data, $index));
                    }
                }
                $this->sqlite->exec('COMMIT;');
            } catch(Exception $e) {
                echo $e->getMessage();
                $this->sqlite->exec('ROLLBACK;');
            }
    }

    private function defineQuery()
    {
        return <<<'Query'
        INSERT INTO data (sku, key, value, source)
        VALUES (%d, '%s', '%s', '%s');
        Query;
    }
}
