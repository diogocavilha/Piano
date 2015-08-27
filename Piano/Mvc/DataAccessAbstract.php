<?php

/**
 * @author Diogo Alexsander Cavilha <diogocavilha@gmail.com>
 * @package Piano
 */

namespace Piano\Mvc;

use PDO;
use RuntimeException;

abstract class DataAccessAbstract
{
    /**
     * Table name.
     * @var string
     */
    protected $table;

    /**
     * @var PDO
     */
    protected $pdo;

    /**
     * Model namespace.
     * @var string
     */
    protected $model = null;

    /**
     * Create a record into database.
     *
     * Example:
     *
     *      insert(array(
     *          'name' => ':name',
     *          'email' => ':email',
     *      ), array(
     *          array(':name', 'John Doe', PDO::PARAM_STR),
     *          array(':email', 'john@domain.com', PDO::PARAM_STR),
     *      ))
     *
     * @param array $data Data to insert.
     */
    public function insert(array $data, array $dataBind)
    {
        if (count($dataBind) == count($dataBind, COUNT_RECURSIVE)) {
            throw new RuntimeException('Data bind must be a recursive array.');
        }

        $fields = implode(', ', array_keys($data));
        $values = implode(', ', array_values($data));
        $sql = 'INSERT INTO ' . $this->table . ' (' . $fields . ') VALUES (' . $values . ')';

        $stmt = $this->pdo->prepare($sql);
        $stmt = $this->bindValues($stmt, $dataBind);

        if ($stmt->execute()) {
            return (int) $this->pdo->lastInsertId();
        }

        return false;
    }

    /**
     * Change a database record.
     *
     * Example:
     *
     *      update(array('name' => ':new_name'), 'name = :where_name', array(
     *          array(':new_name', 'Azaghal', PDO::PARAM_STR),
     *          array(':where_name', 'Linuz', PDO::PARAM_STR),
     *      ))
     *
     * @param array $data  Data to be changed.
     * @param string $where Where clause.
     * @return boolean Execution status.
     */
    public function update(array $data, $where, array $dataBind)
    {
        if (count($dataBind) == count($dataBind, COUNT_RECURSIVE)) {
            throw new RuntimeException('Data bind must be a recursive array.');
        }

        $fields = array();
        foreach ($data as $key => $value) {
            $fields[] = $key . ' = ' . $value;
        }

        $fields = implode(', ', $fields);

        $sql = 'UPDATE ' . $this->table . ' SET ' . $fields . ' WHERE ' . $where;

        $stmt = $this->pdo->prepare($sql);
        $stmt = $this->bindValues($stmt, $dataBind);

        return $stmt->execute();
    }

    /**
     * Delete a record from database.
     *
     * Example:
     *
     *      delete('id = :id', array(
     *          array(':id', 2, PDO::PARAM_INT)
     *      ))
     *
     * or
     *
     *      delete('id = 2')
     *
     * @param  string $where Where clause.
     * @param  array $dataBind Pdo data bind.
     * @return boolean Execution status.
     */
    public function delete($where, array $dataBind = array())
    {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE $where");

        if (!empty($dataBind)) {
            if (count($dataBind) == count($dataBind, COUNT_RECURSIVE)) {
                throw new RuntimeException('Data bind must be a recursive array.');
            }

            $stmt = $this->bindValues($stmt, $dataBind);
        }

        return $stmt->execute();
    }

    /**
     * Perform a query in order to return all database records.
     *
     * Example:
     *
     *      $configData = array(
     *          'fetchClass' => false, // fetch class if false or omitted
     *          'columns' => '*',
     *          'condition' => 'id=:id',
     *          'values' => array(
     *              array(':id', 1, PDO::PARAM_INT)
     *          )
     *      )
     *
     * @param  string $configData  Query configuration.
     * @param  string $order Order
     * @param  integer $count  From
     * @param  integer $offset To
     *
     */
    public function getAll($configData = null, $order = null, $count = null, $offset = null)
    {
        $model = null;
        $stmt = $this->getSelectStatement($configData, $order, $count, $offset);

        if ($stmt->execute()) {
            $model = $stmt->fetchAll();

            $stmt->closeCursor();
        }

        return $model;
    }

    /**
     * Perform a query in order to return one database record.
     *
     * Example:
     *
     *      $configData = array(
     *          'fetchClass' => false, // fetch class if false or omitted
     *          'columns' => '*',
     *          'condition' => 'id=:id',
     *          'values' => array(
     *              array(':id', 1, PDO::PARAM_INT)
     *          )
     *      )
     *
     * @param  string $configData  Query configuration.
     * @param  string $order Order
     * @param  integer $count  From
     * @param  integer $offset To
     *
     */
    public function getFirst($configData = null, $order = null)
    {
        $model = null;
        $stmt = $this->getSelectStatement($configData, $order);

        if ($stmt->execute()) {
            $model = $stmt->fetch();

            $stmt->closeCursor();
        }

        return $model;
    }

    private function getSelectStatement($configData = null, $order = null, $count = null, $offset = null)
    {
        $stmt = null;
        $sql = 'select * from ' . $this->table;

        if (!is_null($configData) && array_key_exists('columns', $configData)) {
            $sql = 'select ' . $configData['columns'] . ' from ' . $this->table;
        }

        if (!is_null($configData) && array_key_exists('condition', $configData)) {
            $sql .= ' where ' . $configData['condition'];
        }

        if (!is_null($order)) {
            $sql .= ' order by ' . $order;
        }

        $limit = '';
        if (!is_null($count) && !is_null($offset)) {
            $limit = ' limit ' . $offset . ', ' . $count;
        } elseif (!is_null($count)) {
            $limit = ' limit 0, ' . $count;
        }

        $sql .= $limit;

        $stmt = $this->pdo->prepare($sql);

        if (is_null($configData) || !array_key_exists('fetchClass', $configData) || $configData['fetchClass'] === true) {
            $stmt->setFetchMode(PDO::FETCH_ASSOC);

            if (!is_null($this->model)) {
                $stmt->setFetchMode(PDO::FETCH_CLASS, $this->model);
            }
        }

        if (!is_null($configData) && array_key_exists('fetchClass', $configData) && $configData['fetchClass'] === false) {
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
        }

        if (!is_null($configData) && array_key_exists('values', $configData) && is_array($configData['values'])) {
            $stmt = $this->bindValues($stmt, $configData['values']);
        }

        return $stmt;
    }

    private function bindValues($stmt, $dataBind)
    {
        foreach ($dataBind as $value) {
            $pdoParamType = null;

            if (array_key_exists(2, $value)) {
                $pdoParamType = $value[2];
            }

            $stmt->bindValue($value[0], $value[1], $pdoParamType);
        }

        return $stmt;
    }
}
