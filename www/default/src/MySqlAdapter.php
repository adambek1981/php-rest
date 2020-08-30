<?php

namespace App;

class MySqlAdapter
{
    /**
     * @return MySqlAdapter
     */
    public static function getInstance(): MySqlAdapter
    {
        static $instance;

        if (!isset($instance)) {
            $instance = new self();
        }

        return $instance;
    }

    ####################################################################################################################

    /** @var \PDO $dbConnection */
    private $dbConnection;

    /** @var \PDOStatement $statement */
    private $statement;

    /**
     * this class constructor
     */
    private function __construct()
    {
        $this->dbConnection = new \PDO("mysql:host=mysql;dbname=app", 'root', 'root');
        $this->dbConnection->exec("set names utf8");
    }

    /**
     * @return bool
     */
    public function beginTransaction(): bool
    {
        return $this->dbConnection->beginTransaction();
    }

    /**
     * @return bool
     */
    public function commitTransaction(): bool
    {
        return $this->dbConnection->commit();
    }

    /**
     * @return bool
     */
    public function rollBackTransaction(): bool
    {
        return $this->dbConnection->rollBack();
    }

    /**
     * @param string $sql
     */
    public function prepareQuery(string $sql)
    {
        $this->statement = $this->dbConnection->prepare($sql);
    }

    /**
     * @param array $params
     *
     * @return bool
     */
    public function executeQuery(array $params = []): bool
    {
        return $this->statement->execute($params);
    }

    /**
     * @return array
     */
    public function getDataAsArray(): array
    {
        $rows = $this->statement->fetchAll(\PDO::FETCH_ASSOC);

        if (false === $rows) {
            $this->statement->closeCursor();
            return [];
        }

        $this->statement->closeCursor();
        return $rows;
    }

    /**
     * @return string
     */
    public function getLastInsertId(): string
    {
        return $this->dbConnection->lastInsertId();
    }
}
