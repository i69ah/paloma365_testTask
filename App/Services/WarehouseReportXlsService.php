<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

/**
 * Class WarehouseReportXlsService
 * Сервис для сборки xls отчета по складам
 *
 * @package App\Services
 */
class WarehouseReportXlsService
{
    /**
     * Собирает объект типа Spreadsheet для отчета по складам
     * и возвращает его
     *
     * @param array $data
     * @return Spreadsheet
     */
    public function getXls(array $data): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        foreach ($data['headers'] as $key => $header) {
            $sheet->setCellValueByColumnAndRow($key + 1, 1, $header);
        }

        $row = 2;
        foreach ($data['rows'] as $element) {
            $column = 1;
            foreach ($element as $value) {
                $sheet->setCellValueByColumnAndRow($column++, $row, $value);
            }

            $row++;
        }

        return $spreadsheet;
    }
}
