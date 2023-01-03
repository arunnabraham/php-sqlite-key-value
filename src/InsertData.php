<?php

declare(strict_types=1);

namespace Arunabraham\TestSqliteKeyValue;

use Clue\React\SQLite\DatabaseInterface;
use Exception;
use Generator;

class InsertData
{

    public function __invoke(DatabaseInterface $db, Generator $iterator)
    {
            try {
                $db->exec('BEGIN;');
                foreach ($iterator as $info) {
                    assert(is_string($info));
                    [$sku, $index, $contents] = explode("[~sep~]", $info);
                    foreach(json_decode($contents, true) as $key => $data)
                    {
                        $db->query($this->defineQuery(),[(int)$sku, $key, (string)$data, $index]);
                    }
                }
                $db->exec('COMMIT;');
            } catch(Exception $e) {
                echo $e->getMessage();
                $db->exec('ROLLBACK;');
            }
    }

    private function defineQuery()
    {
        return <<<'Query'
        INSERT INTO data (sku, key, value, source)
        VALUES (?, ?, ?, ?)
        Query;
    }
}
