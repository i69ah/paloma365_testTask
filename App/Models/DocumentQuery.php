<?php

namespace App\Models;

/**
 * Class DocumentQuery
 * Модель с запросами для формирования отчетов
 *
 * @package App\Models
 */
class DocumentQuery
{
    /** @var int $id - ID записи */
    protected int $id;

    /** @var string $query_string - sql запрос для формирования отчета */
    protected string $query_string;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getQueryString(): string
    {
        return $this->query_string;
    }
}
