<?php

namespace Gzfextra\Db;

use Gzfextra\Mvc\EasyMvc\Application;
use Gzfextra\Stdlib\InstanceTrait;


/**
 * Class AbstractEasyTable
 *
 * @package Gzfextra\Db
 * @author  Xiemaomao
 * @version $Id$
 */
class AbstractEasyTable
{
    protected static $defaultAdapter;
    private static $tableInstances;
    protected $adapter;

    protected $schema;

    protected $table;

    protected $primary;

    public function __construct()
    {
        if (!self::$defaultAdapter) {
            $db = Application::getInstance()->getConfig('db');
            self::setDefaultAdapter($db);
        }

        $this->adapter = self::$defaultAdapter;
    }

    public function setSchema($schema)
    {
        $this->schema = $schema;
    }

    public static function setDefaultAdapter(array $adapter)
    {
        if (strtolower($adapter['driver']) == 'pdo') {
            $adapter = new EasyDb\Adapter\Pdo($adapter);
        } else {
            throw new \RuntimeException('Error driver: ' . $adapter['driver']);
        }

        self::$defaultAdapter = $adapter;
    }

    public function setTable($table)
    {
        $this->table = $table;
        return $this;
    }

    public function getTable()
    {
        return $this->table;
    }

    public function getSchemaTable()
    {
        return isset($this->schema) ? array($this->schema, $this->getTable()) : $this->getTable();
    }

    /**
     * @return EasyDb\Adapter\Pdo
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    public function insert($data)
    {
        return $this->getAdapter()->insert($this->getSchemaTable(), $data);
    }

    public function update($data, $where)
    {
        return $this->getAdapter()->update($this->getSchemaTable(), $data, $where);
    }

    public function select($where)
    {
        $options['from']  = $this->getSchemaTable();
        $options['where'] = $where;

        return $this->getAdapter()->select($options);
    }

    public function find($id)
    {
        return $this->select(array($this->primary => $id))->fetch();
    }

    public function save(& $data)
    {
        if (isset($data[$this->primary])) {
            $rs = $this->update($data, array($this->primary => $data[$this->primary]));
        } else {
            $rs                   = $this->insert($data);
            $data[$this->primary] = $this->getAdapter()->lastInsertId();
        }

        return $rs;
    }

    /**
     * @return static
     */
    public static function getInstance()
    {
        $className = get_called_class();
        if (!isset(self::$tableInstances[$className])) {
            self::$tableInstances[$className] = new static();
        }

        return self::$tableInstances[$className];
    }
}