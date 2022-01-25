<?php


namespace App\Services;


use App\Repositories\Db;

class FiltersService
{
    public function checkTypes(string $filters, Db  $db): array
    {
        $sql = 'SELECT `DATA_TYPE` FROM `information_schema`.`COLUMNS` 
                WHERE `TABLE_SCHEMA`=:schema AND `TABLE_NAME`=:table AND `COLUMN_NAME`=:column';
        $schema = 'paloma365';
        $filtersArray = explode(',', $filters);
        $result = [];
        foreach ($filtersArray as $rawFilter) {
            $filterArray = explode('.', $rawFilter);
            $table = $filterArray[0];
            $column = $filterArray[1];
            $type = $db->fetchColumn($sql, [
                ':schema' => $schema,
                ':table' => $table,
                ':column' => $column,
            ]);
            $result[$column] = $type;
        }

        return $result;
    }

    public function buildParamsArray(array $filters, array $postData): array
    {
        $result = [];
        foreach ($filters as $filter => $type) {
            if ('datetime' === $type) {
                $dateBegin = \DateTimeImmutable::createFromFormat('d.m.Y H:i',$postData[$filter]['begin']);
                $dateEnd = \DateTimeImmutable::createFromFormat('d.m.Y H:i', $postData[$filter]['end']);

                if (false === $dateBegin || false === $dateEnd) {
                    return [];
                };

                $result[":{$filter}_begin"] = $dateBegin->format('Y-m-d H:i:s');
                $result[":{$filter}_end"] = $dateEnd->format('Y-m-d H:i:s');
            } else {
                $result[":{$filter}"] = $postData[$filter];
            }
        }

        return $result;
    }
}