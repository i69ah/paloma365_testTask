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

        $sheet->setCellValueByColumnAndRow(1, 1, 'id');
        $sheet->setCellValueByColumnAndRow(2, 1, 'created_at');
        $sheet->setCellValueByColumnAndRow(3, 1, 'warehouse_id');

        $row = 2;
        foreach ($data as $element) {
            $sheet->setCellValueByColumnAndRow(1, $row, $element['id']);
            $sheet->setCellValueByColumnAndRow(2, $row, $element['created_at']);
            $sheet->setCellValueByColumnAndRow(3, $row, $element['warehouse_id']);
            $row++;
        }

        return $spreadsheet;
    }
}
