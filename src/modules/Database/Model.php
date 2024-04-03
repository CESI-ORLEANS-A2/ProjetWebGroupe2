<?php

class Model {
    private array $schema;
    private array $data;
    private array $oldData;

    public function __construct(array $data, array $schema) {
        $this->data = $data;
        $this->oldData = $data;
        $this->schema = $schema;

        if ($this->getID() == null)
            $this->data['ID'] = null;

        if (!$this->validate())
            throw new Exception("Invalid data");
    }

    public function equals(Model $model): bool {
        foreach ($this->schema as $key => $value) {
            if ($key == 'ID')
                continue;
            if ($this->get($key) != $model->get($key))
                return false;
        }
        return true;
    }

    public function getID() {
        if (isset($this->data['ID']))
            return $this->data['ID'];
        return null;
    }

    protected function setID($id) {
        $this->data['ID'] = $id;
    }

    public function get(string $key) {
        if (!$this->exists($key))
            throw new Exception("Invalid key");
        if ($this->isDefined($key))
            return $this->data[$key];
        return null;
    }

    public function set(string $key, $value) {
        if (
            $this->exists($key)
            && !$this->isReadOnly($key)
            && $this->checkType($key, $value)
        )
            $this->data[$key] = $value;
        else
            throw new Exception("Invalid key or value");
    }

    protected function exists(string $key): bool {
        return isset($this->schema[$key]);
    }

    protected function isDefined(string $key): bool {
        return isset($this->data[$key]);
    }

    protected function isReadOnly(string $key): bool {
        return isset($this->schema[$key]['readonly']) && $this->schema[$key]['readonly'];
    }

    protected function isRequired(string $key): bool {
        return isset($this->schema[$key]['required']) && $this->schema[$key]['required'];
    }

    protected function checkType(string $key, $value): bool {
        if (!isset($this->schema[$key]['type']))
            return true;
        switch ($this->schema[$key]['type']) {
            case 'string':
                return is_string($value);
            case 'int':
                return is_int($value);
            case 'float':
                return is_float($value);
            case 'bool':
                return is_bool($value);
            case 'array':
                return is_array($value);
            case 'object':
                return is_object($value);
            case 'datetime':
                return $value instanceof DateTime;
            default:
                return false;
        }
    }

    public function isUpdated(string $key): bool {
        return $this->isDefined($key) && $this->data[$key] != $this->oldData[$key];
    }

    protected function validate(): bool {
        foreach ($this->schema as $key => $value) {
            if ($this->isRequired($key) && !$this->isDefined($key)) {
                // echo "key $key is required but not defined";
                return false;
            }
            if ($this->isDefined($key) && !$this->checkType($key, $this->data[$key])) {
                // echo "key $key has invalid type";
                return false;
            }
        }
        return true;
    }

    public function toArray(): array {
        return $this->data;
    }

    protected function updateOldData() {
        $this->oldData = $this->data;
    }

    public function remove() {
        throw new Exception("Not implemented");
    }

    public function save() {
        throw new Exception("Not implemented");
    }

    // public function save() {
    //     if (!$this->validate())
    //         throw new Exception("Invalid data");

    //     $dbh = Database::getInstance();
    //     if ($this->getID() == null)
    //         $dbh->insert($this->schema['table'], $this->data);
    //     else
    //         $dbh->update(
    //             $this->schema['table'],
    //             $this->data,
    //             ':ID_' + get_class($this),
    //             [':ID' => $this->getID()]
    //         );
    // }

    // public function delete() {
    //     if ($this->getID() == null)
    //         throw new Exception("Invalid ID");

    //     $dbh = Database::getInstance();
    //     $dbh->delete(
    //         $this->schema['table'],
    //         ':ID_' + get_class($this),
    //         [':ID' => $this->getID()]
    //     );
    // }
}
