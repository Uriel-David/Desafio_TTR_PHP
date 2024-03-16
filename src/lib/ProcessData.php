<?php

namespace App\lib;
use App\exception\MissingCsvException;

class ProcessData
{
    private $olderCsv;
    private $newCsv;

    public function __construct(string $newCsv, string $olderCsv)
    {
        $this->newCsv   = $newCsv;
        $this->olderCsv = $olderCsv;
    }

    public function compareCSV(): array
    {
        $olderData = $this->readCsv($this->olderCsv);
        $newData = $this->readCsv($this->newCsv);

        if (empty($newData) || empty($olderData)) {
            throw new MissingCsvException();
        }

        $result = [
            'unchanged_rows' => [],
            'updated_rows' => [],
            'new_rows' => [],
            'header' => array_merge($olderData['header'], ['old_file_row_index', 'new_file_row_index'])
        ];

        foreach ($olderData['rows'] as $index => $oldRow) {
            $foundInNew = false;
            foreach ($newData['rows'] as $newIndex => $newRow) {
                if ($oldRow['cnpj'] == $newRow['cnpj']) {
                    $foundInNew = true;

                    if ($oldRow == $newRow) {
                        $result['unchanged_rows'][] = array_merge($oldRow, [$index + 1, $newIndex + 1]);
                    } else {
                        $result['updated_rows'][] = array_merge($oldRow, [$index + 1, $newIndex + 1]);
                    }

                    unset($newData['rows'][$newIndex]);
                    break;
                }
            }
            if (!$foundInNew) {
                $result['unchanged_rows'][] = array_merge($oldRow, [$index + 1, '']);
            }
        }

        foreach ($newData['rows'] as $newIndex => $newRow) {
            $result['new_rows'][] = array_merge($newRow, ['', $newIndex + 1]);
        }

        return $result;
    }

    private function readCsv(string $filename): array
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