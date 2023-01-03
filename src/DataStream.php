<?php

declare(strict_types=1);

namespace Arunabraham\TestSqliteKeyValue;

use Clue\React\SQLite\DatabaseInterface;
use Clue\React\SQLite\Result;
use React\Promise\PromiseInterface;

use function React\Async\async;
use function React\Async\await;

class DataStream
{
    private array $columns;

    public function __invoke(DatabaseInterface $db): PromiseInterface
    {
        return $this->arraySet($db);
    }

    private function fetchColumns(DatabaseInterface $db): PromiseInterface
    {
        $query = $db->query($this->queryforColumns());
        return $query;
    }

    private function yieldAllData(DatabaseInterface $db): PromiseInterface
    {
        $query = $db->query($this->fetchDataQuery());
        return $query;
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

    private function arraySet(DatabaseInterface $db): PromiseInterface
    {
      return async((function() use ($db){
           $fetchColumns = $this->fetchColumns($db)->then(function (Result $res): array {
                $columns = [];
                foreach ($res->rows as $row) {
                    $columns[$row['key']] = "";
                }
    
                return $columns;
            });

            return $this->yieldAllData($db)->then(function (Result $dataObject) use ($fetchColumns): \Generator {
                $iterate = function () use ($dataObject): \Generator {
                    foreach ($dataObject->rows as $result) {
                        yield $result;
                    }
                };
                $columns = await($fetchColumns);
                $this->columns = $columns;
                $iterator = $iterate();

                while ($iterator->valid()) {
                    $current = fn() => $iterator->current();

                    $next = function () use ($iterator) {
                        $iterator->next();
                        return $iterator->current();
                    };

                    $columns[$current()['key']] = $current()['value'];

                    if ($current()['sku'] !== ($next()['sku'] ?? -1)) {
                        yield $columns;
                        unset($columns);
                        $columns = $this->columns;
                    }
                }
            });
        }))();
    }
}
