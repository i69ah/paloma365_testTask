<?php

namespace App\Repositories;

use Exception;

/**
 * Class Db
 * Класс для запросов в БД
 *
 * @package App\Repositories
 */
class Db
{
    private \PDO $dbh;

    /**
     * Конструктор класса для запросов в БД
     */
    public function __construct()
    {
        $dsl = 'mysql:host=localhost;dbname=paloma365';
        $user = 'root';
        $password = 'root';
        $this->dbh = new \PDO($dsl, $user, $password);
    }

    /**
     * Выполняет sql запрос и возвращает true в случае успеха
     * false в случае неудачи
     *
     * @param string $sql
     * @param array $params
     * @return bool
     * @throws Exception
     */
    public function execute(string $sql, array $params=[]): bool
    {
        if (empty($sql)) {
            throw new Exception('Пустой sql запрос');
        }

        return $this->dbh->prepare($sql)->execute($params);
    }

    /**
     * Выполняет sql запрос и возвращает данные
     *
     * @param string $sql
     * @param string|null $class
     * @param array $params
     * @return array
     * @throws Exception
     */
    public function query(string $sql, ?string $class = null, array $params=[]): array
    {
        if (empty($sql)) {
            throw new Exception('Пустой sql запрос');
        }
        $sth = $this->dbh->prepare($sql);

        if (!$sth->execute($params)) {
            throw new Exception('Error executing sql query: ' . $sth->errorCode());
        }

        if (null === $class) {
            return $sth->fetchAll(\PDO::FETCH_ASSOC);
        }

        return $sth->fetchAll(\PDO::FETCH_CLASS, $class);
    }

    /**
     * @param string $sql
     * @param array $params
     * @return string
     * @throws Exception
     */
    public function fetchColumn(string $sql, array $params=[]): string
    {
        if (empty($sql)) {
            throw new Exception('Пустой sql запрос');
        }
        $sth = $this->dbh->prepare($sql);

        if (!$sth->execute($params)) {
            throw new Exception('Error executing sql query: ' . $sth->errorCode());
        }

        return $sth->fetchColumn();
    }
}
