<?php

namespace Models;

use Lib\Database;

abstract class AbstractModel
{
    /**
     * List of all model properties
     * @var array
     */
    protected $properties = [];

    public static function findById(int $id)
    {
        $fields = Database::instance()->selectOne(static::TABLE_NAME, $id);

        if ($fields) {
            return static::compileEntity($fields);
        }

        return false;
    }

    public static function find(array $where = [], array $sort = [], $limit = null): array
    {
        $data = Database::instance()->select(static::TABLE_NAME, $where, $sort, $limit);

        $result = [];
        foreach ($data as $element) {
            $result[] = static::compileEntity($element);
        }

        return $result;
    }

    public static function delete(int $id): bool
    {
        return Database::instance()->delete(static::TABLE_NAME, ['id' => $id]);
    }

    public function save()
    {
        if (isset($this->id)) {
            if (!Database::instance()->update(static::TABLE_NAME, $this->toArray(), ['id' => $this->id])){
                return false;
            }
        } else {
            if (!$id = Database::instance()->insert(static::TABLE_NAME, $this->toArray())) {
                return false;
            }
            $this->id = $id;
        }

        return $this->id;
    }

    public function toArray(): array
    {
        $result = [];
        foreach ($this->properties as $property) {
            if (is_null($this->{$property})) {
                continue;
            }

            $result[$property] = $this->{$property};
        }

        return $result;
    }

    protected static function compileEntity(array $data = [])
    {
        $entity = new static();

        foreach ($entity->properties as $property) {
            if (!isset($data[$property])) {
                continue;
            }

            $entity->{$property} = $data[$property];
        }

        return $entity;
    }
}