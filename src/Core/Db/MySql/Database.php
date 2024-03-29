<?php namespace Core\Db\MySql;

use Core\Persistence\File;
use PDO;
use PDOException;
use PDOStatement;
use stdClass;

class Database {
    
    private $host;
    private $user;
    private $pass;
    private $name;
    private $port;

    private $connection;

    public function __construct(string $host = null, string $user = null, string $pass = null, string $name = null, int $port = 3306) {
        if (is_null($host) && File::exists("../config/database.config.php")) {
            $config = require "../config/database.config.php";
            extract($config);
        }

        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        $this->name = $name;
        $this->port = $port;

        $this->connect();
    }

    private function connect(): void {
        $dsn = sprintf("mysql:host=%s;dbname=%s;port=%d", $this->host, $this->name, $this->port);

        try {
            $this->connection = new PDO($dsn, $this->user, $this->pass, [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
            ]);
        } catch (PDOException $e) {
            die('Error connecting to database! ' . $e->getMessage());
        }
    }

    public function query(string $sql, array $data = []): PDOStatement {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($data);

        return $stmt;
    }

    public function all(string $table): array {
        $stmt = $this->query("SELECT * FROM $table");
        return $stmt->fetchAll();
    }

    public function where(string $table, $data): array {
        $where = $this->getWhere($data);
        $stmt = $this->query("SELECT * FROM $table WHERE $where", $data);
        return $stmt->fetchAll();
    }

    public function byId(string $table, $id, string $idField = 'id') {
        $resultSet = $this->where($table, [$idField => $id]);
        return isset($resultSet[0]) ? $resultSet[0] : null;
    }

    public function insert(string $table, array $data): stdClass {
        $fields = array_keys($data);
        $values = array_values($data);

        $fieldsAsString = implode(', ', $fields);
        $valuesAsString = ':' . implode(", :", $fields);

        $sql = "INSERT INTO $table ($fieldsAsString) VALUES ($valuesAsString)";

        $stmt = $this->query($sql, $data);

        $result = new stdClass();
        $result->stmt = $stmt;
        $result->lastInsertId = $this->connection->lastInsertId();

        return $result;
    }

    public function update(string $table, array $data, $where, array $whereData = []): PDOStatement {
        $pairs = array_map(function ($key) {
            return "$key = :$key";
        }, array_keys($data));

        $pairsAsString = implode(', ', $pairs);
        $where = $this->getWhere($where);

        $sql = "UPDATE $table SET $pairsAsString WHERE $where";
        return $this->query($sql, array_merge($data, $whereData));
    }

    public function delete(string $table, $where, array $data = []): PDOStatement {
        $where = $this->getWhere($where);
        $sql = "DELETE FROM $table WHERE $where";

        return $this->query($sql, $data);
    }

    public function exists(string $table, string $field, string $value): bool {
        $sql = "SELECT COUNT(*) AS counter FROM $table WHERE $field = :value LIMIT 1";
        $stmt = $this->query($sql, ['value' => $value]);
        $row = $stmt->fetch();

        return $row && $row->counter > 0;
    }

    private function getWhere($where): string {
        if (is_array($where)) {
            $pairs = array_map(function ($key) {
                return "$key = :$key";
            }, array_keys($where));

            return implode(' AND ', $pairs);
        }

        return $where;
    }
}