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

        foreach ($newData['rows'] as $newIndex => $newRow) {
            $foundInOld = false;

            foreach ($olderData['rows'] as $oldIndex => $oldRow) {
                if ($oldRow['cnpj'] == $newRow['cnpj']) {
                    $foundInOld = true;

                    if ($oldRow == $newRow) {
                        $result['unchanged_rows'][] = array_merge($newRow, [$oldIndex + 1, $newIndex + 1]);
                    } else {
                        $result['updated_rows'][] = array_merge($newRow, [$oldIndex + 1, $newIndex + 1]);
                    }

                    unset($olderData['rows'][$oldIndex]);
                    break;
                }
            }

            if (!$foundInOld) {
                $result['new_rows'][] = array_merge($newRow, [$oldIndex + 1, $newIndex + 1]);
            }
        }

        return $result;
    }
}
