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

        $identicalRows = [];
        $updatedRows = [];
        $newRows = [];
        $removedRows = [];

        $oldDataCopy = $olderData;

        foreach ($newData as $newRow) {
            $foundIdentical = false;
            $foundUpdated = false;

            foreach ($oldDataCopy as $key => $oldRow) {
                if ($newRow['data'] == $oldRow['data']) {
                    $identicalRows[] = [
                        'data' => $newRow['data'],
                        'new_position' => $newRow['position'],
                        'old_position' => $oldRow['position']
                    ];
                    unset($oldDataCopy[$key]);
                    $foundIdentical = true;
                    break;
                }
            }

            if (!$foundIdentical) {
                foreach ($olderData as $oldRow) {
                    if ($newRow['data'] != $oldRow['data'] && $newRow['data'][0] == $oldRow['data'][0]) {
                        $updatedRows[] = [
                            'data' => $newRow['data'],
                            'new_position' => $newRow['position'],
                            'old_position' => $oldRow['position']
                        ];
                        $foundUpdated = true;
                        break;
                    }
                }

                if (!$foundUpdated) {
                    $newRows[] = [
                        'data' => $newRow['data'],
                        'new_position' => $newRow['position']
                    ];
                }
            }
        }

        foreach ($oldDataCopy as $remainingOldRow) {
            $removedRows[] = [
                'data' => $remainingOldRow['data'],
                'old_position' => $remainingOldRow['position']
            ];
        }

        return [
            'identical' => $identicalRows,
            'updated' => $updatedRows,
            'new' => $newRows,
            'removed' => $removedRows
        ];
    }
}
