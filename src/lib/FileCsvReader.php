<?php

namespace App\lib;

use App\interface\CsvReaderInterface;

class FileCsvReader implements CsvReaderInterface
{
    public function __construct() {}

    public function readCsv(string $filename): array
    {
        $header = [];
        $rows = [];

        if (!file_exists($filename) || !is_readable($filename)) {
            return [];
        }

        if (($handle = fopen($filename, "r")) !== false) {
            $rowIndex = 0;
            while (($data = fgetcsv($handle, 1000, ";")) !== false) {
                if (empty($header)) {
                    $header = $data;
                } else {
                    $rowData = array_combine($header, $data);
                    $rows[] = $rowData;
                }
                $rowIndex++;
            }
            fclose($handle);
        }

        return ['header' => $header, 'rows' => $rows];
    }
}
