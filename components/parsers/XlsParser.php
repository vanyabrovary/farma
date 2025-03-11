<?php

namespace app\components\parsers;

use PhpOffice\PhpSpreadsheet\IOFactory;

class XlsParser
{
    public function parseFile(string $filePath, callable $mapper = null): array
    {
        $sheet = IOFactory::load($filePath)->getActiveSheet();

        $data = [];

        foreach ($sheet->getRowIterator() as $row) {
            $rowData = [];
            foreach ($row->getCellIterator() as $cell) {
                $rowData[] = $cell->getFormattedValue();
            }
            $data[] = $mapper ? call_user_func($mapper, $rowData) : $rowData;
        }

        return $data ?? [];
    }
}
