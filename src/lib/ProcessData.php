<?php

namespace App\lib;

class ProcessData
{
    private $olderCsv;
    private $newCsv;

    public function __construct($newCsv = null, $olderCsv = null)
    {
        $this->newCsv   = $newCsv;
        $this->olderCsv = $olderCsv;
    }

    public function compareCSV()
    {
        $olderData = $this->readCsv($this->olderCsv);
        $newData = $this->readCsv($this->newCsv);

        $result = [
            'unchanged_rows' => [],
            'updated_rows' => [],
            'new_rows' => [],
            'header' => array_merge($olderData['header'], ['Old File Row Index', 'New File Row Index'])
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

    private function readCsv($filename)
    {
        $header = [];
        $rows = [];

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