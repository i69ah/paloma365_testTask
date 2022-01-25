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

    /** @var string $filters - список фильтров для интерфейса */
    protected string $filters;

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
    public function getFilters(): string
    {
        return $this->filters;
    }

    /**
     * @return string
     */
    public function getQueryString(): string
    {
        return $this->query_string;
    }
}
