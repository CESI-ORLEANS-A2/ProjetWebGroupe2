<?php

class ModelManager implements Iterator {
    protected array $models;
    private int $position;

    public function __construct($array = array()) {
        $this->models = $array;
    }

    public function rewind(): void {
        $this->position = 0;
    }

    public function current(): Model {
        return $this->models[$this->position];
    }

    public function key(): int {
        return $this->position;
    }

    public function next(): void {
        ++$this->position;
    }

    public function valid(): bool {
        return isset($this->models[$this->position]);
    }

    public function save() {
        foreach ($this->models as $model) {
            $model->save();
        }
    }

    public function __get($index) {
        if (isset($this->models[$index]))
            return $this->models[$index];
        return null;
    }

    public function __set($index, $value) {
        $this->models[$index] = $value;
    }

    public function append(Model $model) {
        foreach ($this->models as $m) {
            if ($m->equals($model))
                return;
        }
        $this->models[] = $model;
    }

    public function pop() {
        return array_pop($this->models);
    }

    public function shift() {
        return array_shift($this->models);
    }

    public function unshift($model) {
        array_unshift($this->models, $model);
    }

    public function count() {
        return count($this->models);
    }

    public function get($index) {
        return $this->models[$index];
    }

    public function set($index, $value) {
        $this->models[$index] = $value;
    }

    public function remove($index) {
        array_splice($this->models, $index, 1);
    }

    public function clear() {
        $this->models = array();
    }

    public function find($key, $value) {
        foreach ($this->models as $index => $model) {
            if ($model->get($key) == $value)
                return $index;
        }
        return null;
    }

    public function __toString() {
        return implode(', ', $this->models);
    }

    public function popAndSave() {
        $model = $this->pop();
        $model->save();
        return $model;
    }

    public function shiftAndSave() {
        $model = $this->shift();
        $model->save();
        return $model;
    }

    public function unshiftAndSave($model) {
        $this->unshift($model);
        $model->save();
    }

    public function saveAll() {
        foreach ($this->models as $model) {
            $model->save();
        }
    }

    public function removeAndSave($index) {
        $model = $this->models[$index];
        unset($this->models[$index]);
        $model->remove();
        return $model;
    }

    public function clearAndSave() {
        foreach ($this->models as $model) {
            $model->remove();
        }
        $this->clear();
    }
}
