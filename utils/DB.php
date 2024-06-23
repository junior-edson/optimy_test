<?php

class DB
{
    private \PDO $pdo;
    private static ?DB $instance = null;

    /**
     * @throws Exception
     */
    private function __construct()
    {
        $config = require(__DIR__ . '/../config/db.php');

        $dsn = 'mysql:dbname=' . $config['dbname'] . ';host=' . $config['host'];
        $user = $config['user'];
        $password = $config['password'];

        try {
            $this->pdo = new \PDO($dsn, $user, $password);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new Exception('Database connection failed: ' . $e->getMessage());
        }
    }

    /**
     * @return self
     */
    public static function getInstance(): self
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Execute a SELECT query
     *
     * @param string $sql
     * @param array $params
     * @return array
     * @throws Exception
     */
    public function select(string $sql, array $params = []): array
    {
        try {
            $sth = $this->pdo->prepare($sql);
            $sth->execute($params);

            return $sth->fetchAll(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception('Execution failed: ' . $e->getMessage());
        }
    }

    /**
     *  Execute an INSERT, UPDATE, or DELETE query
     *
     * @param string $sql
     * @param array $params
     * @return bool
     * @throws Exception
     */
    public function exec(string $sql, array $params = []): bool
    {
        try {
            $sth = $this->pdo->prepare($sql);

            return $sth->execute($params);
        } catch (PDOException $e) {
            throw new Exception('Execution failed: ' . $e->getMessage());
        }
    }

    /**
     * Get the last inserted ID
     *
     * @return int
     */
    public function lastInsertId(): int
    {
        return $this->pdo->lastInsertId();
    }

    /**
     * @return void
     */
    public function beginTransaction(): void
    {
        $this->pdo->beginTransaction();
    }

    /**
     * @return void
     */
    public function commit(): void
    {
        $this->pdo->commit();
    }

    /**
     * @return void
     */
    public function rollBack(): void
    {
        $this->pdo->rollBack();
    }
}
