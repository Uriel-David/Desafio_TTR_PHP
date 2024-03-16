<?php

namespace App\lib;
use App\exception\MissingCsvException;
use App\interface\CsvReaderInterface;

class ProcessData
{
    private CsvReaderInterface $csvReader;

    public function __construct(CsvReaderInterface $csvReader)
    {
        $this->csvReader = $csvReader;
    }

    public function compareCSV(string $newCsv, string $olderCsv): array
    {
        $olderData = $this->csvReader->readCsv($olderCsv);
        $newData = $this->csvReader->readCsv($newCsv);

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
}