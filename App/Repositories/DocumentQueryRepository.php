<?php

namespace App\Repositories;

use App\Models\DocumentQuery;
use Exception;

/**
 * Class DocumentQueryRepository
 * Репозиторий для получения sql-запросов на сборку отчетов
 *
 * @package App\Repositories
 */
class DocumentQueryRepository
{
    /** @var Db $db - объект для запрос в БД */
    protected Db $db;

    /** @var string $table - имя таблицы */
    protected string $table = 'documents_queries';

    /**
     * DocumentQueryRepository constructor.
     * @param Db $db
     */
    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    /**
     * Ищет запись по id
     * Возвращает объект DocumentQuery или null, если запись не найдена
     *
     * @param int $pk
     * @return DocumentQuery|null
     * @throws Exception
     */
    public function findByPk(int $pk): ?DocumentQuery
    {
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE id=:id';
        $result = $this->db->query($sql, DocumentQuery::class, [ ':id' => $pk, ]);
        if (empty($result)) {
            return null;
        }

        return $result[0];
    }
}
