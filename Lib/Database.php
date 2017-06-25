<?php

namespace Lib;

use Lib\Traits\Singleton;

class Database
{
    use Singleton;

    const LOGIC_AND = 'AND';
    const LOGIC_OR  = 'OR';

    private $db;

    protected function __construct()
    {
        $dbSettings = Config::instance()->get('database');
        try {
            $this->db = new \PDO(
                'mysql:host=' . $dbSettings['host'] . ':' . $dbSettings['port'] . ';dbname=' . $dbSettings['database'],
                $dbSettings['user'],
                $dbSettings['password'],
                [\PDO::ATTR_PERSISTENT => true]
            );
            $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {}
    }

    public function isConnected()
    {
        return !empty($this->db);
    }

    public function update($table, array $data, array $where = []): bool
    {
        $result = false;
        try {
            $parameters = $this->getParameters($data);

            $update = array_map(function($el1, $el2) {
                return $el1 . ' = ' . $el2;
            }, array_keys($data), array_keys($parameters));

            list($where, $newParameters) = $this->getWhere($where, $prefix = 'where');
            $parameters += $newParameters;

            $query = $this->db->prepare('UPDATE `' . $table . '` SET ' . implode(', ', $update) . ' ' . $where);
            foreach ($parameters as $key => $value) {
                $query->bindValue($key, $value);
            }

            $query->execute();
            $result = $query->rowCount() > 0;
        } catch (\PDOException $e) {
            var_dump($e);
            return false;
        }

        return $result;
    }

    public function select(string $table, array $where = [], array $sort = [], $limit = null): ?array
    {
        $result = [];
        try {
            list($where, $parameters) = $this->getWhere($where);
            $sort                     = $this->getSort($sort);
            $limit                    = $this->getLimit($limit);

            $query = $this->db->prepare('SELECT * FROM `' . $table . '` ' . implode(' ', [$where, $sort, $limit]));
            foreach ($parameters as $key => $value) {
                $query->bindValue($key, $value);
            }

            $query->execute();
            $result = $query->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return false;
        }

        return $result;
    }

    public function selectOne(string $table, int $id)
    {
        $result = false;
        try {
            $query = $this->db->prepare('SELECT * FROM `' . $table . '` WHERE id = :id');
            $query->bindValue(':id', $id);
            $query->execute();
            $result = $query->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return false;
        }

        return $result;
    }

    public function delete(string $table, $where = []): bool
    {
        if (empty($where)) {
            return false; // do not allow to delete everyting
        }

        $result = false;
        try {
            list($where, $parameters) = $this->getWhere($where);

            $query = $this->db->prepare('DELETE FROM `' . $table . '` ' . $where);
            foreach ($parameters as $key => $value) {
                $query->bindValue($key, $value);
            }

            $query->execute();
            $result = $query->rowCount() > 0;
        } catch(\PDOException $e) {
            return false;
        }

        return $result;
    }

    public function insert(string $table, array $data = []): ?int
    {
        try {
            $parameters = $this->getParameters($data);

            $query = $this->db->prepare('INSERT INTO `' . $table . '` (' . implode(',', array_keys($data)) . ') VALUES (' . implode(',', array_keys($parameters)) . ')');
            foreach ($parameters as $key => $value) {
                $query->bindValue($key, $value);
            }

            $query->execute();
        } catch(\PDOException $e) {
            return false;
        }

        return $this->db->lastInsertId();
    }

    protected function getParameters(array $data, $prefix = ''): array
    {
        $result = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $i => $v) {
                    $result[':' . $prefix . $key . $i] = $v;
                }
            } else {
                $result[':' . $prefix . $key] = $value;
            }
        }

        return $result;
    }

    /**
     * Generating WHERE statement
     * @param  array  $where  ['id' => 1, 'name' => 'John Doe'] => WHERE id = 1 AND name = 'John Doe'
     *                        ['id' => [1, 2, 3,]] => WHERE id IN (1,2,3)
     *                        ['id' => 1, 'name' => 'John Doe', '_logic' => 'OR'] => WHERE id = 1 OR name = 'John Doe'
     * @param  string $prefix parameters prefix
     * @return array          generated parametrized WHERE statement
     *                        and parameters array (@see Database::getParameters())
     */
    protected function getWhere(array $where = [], string $prefix = ''): array
    {
        if (empty($where)) {
            return ['', []];
        }

        $logic = static::LOGIC_AND;
        if (isset($where['_logic']) && in_array($where['_logic'], [
            static::LOGIC_AND,
            static::LOGIC_OR
        ])) {
            $logic = $where['_logic'];
        }

        $whereData = [];
        $parameters = [];
        foreach ($where as $key => $value) {
            if ($key == '_logic') {
                continue;
            }

            $tmpParameters = $this->getParameters([$key => $value], $prefix);
            if (is_array($value)) {
                $whereData[] = $key . ' IN (' . implode(',', array_keys($tmpParameters)) . ')';
            } else {
                $whereData[] = $key . ' = ' . key($tmpParameters);
            }

            $parameters += $tmpParameters;
        }

        $whereResult = 'WHERE ' . implode(' ' . $logic . ' ', $whereData);

        return [$whereResult, $parameters];
    }

    protected function getSort(array $sort = []): string
    {
        if (empty($sort)) {
            return '';
        }

        return 'ORDER BY ' . key($sort) . ' ' . current($sort);
    }

    protected function getLimit($limit): string
    {
        if (empty($limit)) {
            return '';
        }

        if (is_array($limit)) {
            return 'LIMIT ' . implode(', ', $limit);
        }

        return 'LIMIT ' . $limit;
    }
}