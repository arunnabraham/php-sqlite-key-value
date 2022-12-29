<?php

declare(strict_types=1);

namespace Arunabraham\TestSqliteKeyValue;

use FilesystemIterator;
use Generator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class FileToData
{
    public const BASE_FIXTURE_PATH = '../fixtures/';
    public function getData(): \Generator
    {
        $rtt = new RecursiveDirectoryIterator(__DIR__ . '/../fixtures', FilesystemIterator::SKIP_DOTS);
        $rii = new RecursiveIteratorIterator($rtt);

        foreach ($rii as $info) {
            $filePath = $info->getPathName();
            [$fileIndex, $fileName] = explode('/', explode(self::BASE_FIXTURE_PATH, $filePath)[1]);

            yield basename($fileName, '.json') .'[~sep~]' . $fileIndex.'[~sep~]'.file_get_contents($filePath);
        }
    }
}
