<?php


namespace App\Services;


use App\Repositories\Db;

class FiltersService
{
    public function checkTypes(string $filters, Db  $db): array
    {
        $sql = 'SELECT `DATA_TYPE` AS `type`, `COLUMN_COMMENT` AS `comment` FROM `information_schema`.`COLUMNS` 
                WHERE `TABLE_SCHEMA`=:schema AND `TABLE_NAME`=:table AND `COLUMN_NAME`=:column';
        $schema = 'paloma365';
        $filtersArray = explode(',', $filters);
        $result = [];
        foreach ($filtersArray as $rawFilter) {
            $filterArray = explode('.', $rawFilter);
            $table = $filterArray[0];
            $column = $filterArray[1];
            $typeAndComment = $db->query($sql, null, [
                ':schema' => $schema,
                ':table' => $table,
                ':column' => $column,
            ]);
            $result[$column] = $typeAndComment;
        }

        return $result;
    }

    public function buildParamsArray(array $filters, array $postData): array
    {
        $result = [];
        foreach ($filters as $filter => $typeAndCommentRaw) {
            $typeAndComment = $typeAndCommentRaw[0];
            if ('datetime' === $typeAndComment['type']) {
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

    public function buildHeadersArray(array $filters) :array
    {
        $headers = ['#'];
        foreach ($filters as $filter => $typeAndCommentRaw) {
            $typeAndComment = $typeAndCommentRaw[0];
            $headers[] = $typeAndComment['comment'];
        }

        return $headers;
    }
}