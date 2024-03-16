<?php

namespace App\interface;

interface CsvReaderInterface
{
    public function readCsv(string $filename): array;
}
