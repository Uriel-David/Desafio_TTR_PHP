<?php

namespace App\lib;

use App\interface\CsvReaderInterface;

class FileCsvReader implements CsvReaderInterface
{
    public function __construct() {}

    public function readCsv(string $filename): array
    {
        if (!file_exists($filename) || !is_readable($filename)) {
            return [];
        }

        $rows = [];
        if (($handle = fopen($filename, "r")) !== FALSE) {
            $position = 1;
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                $rows[] = ['data' => $data, 'position' => $position];
                $position++;
            }
            fclose($handle);
        }
        return $rows;
    }
}
