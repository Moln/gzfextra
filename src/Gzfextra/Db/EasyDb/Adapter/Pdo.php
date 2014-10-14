<?php

namespace Gzfextra\Db\EasyDb\Adapter;

use Zend\Db\Adapter\Exception\InvalidQueryException;


/**
 * Class Pdo
 *
 * @package Gzfextra\Db\EasyDb\Adapter
 * @author  Xiemaomao
 * @version $Id$
 */
class Pdo extends \PDO implements DbAdapterInterface
{
    public function __construct(array $config)
    {
        $dsn = $username = $password = $hostname = $database = $driver_options = null;
        extract($config);
        parent::__construct($dsn, $username, $password, $driver_options);
    }

    private function quoteTable($table)
    {
        return is_array($table) ? "`{$table[0]}`.`{$table[1]}`" : "`$table`";
    }

    /**
     * 插入到数据表
     *
     * @param string $table
     * @param array $data
     *
     * @return bool
     * @example
     *  $this->insert('tablename', array('id'=>null, 'name'=>'test'))
     */
    public function insert($table, $data)
    {
        $setkeys = $setvals = array();
        $sql     = "INSERT INTO {$this->quoteTable($table)} SET ";
        foreach ($data as $key => $val) {
            if (is_int($key)) {
                $setkeys[] = $val;
                continue;
            }
            $setkeys[] = "`$key`=?";
            $setvals[] = $val;
        }
        $sql .= implode(',', $setkeys);
        return $this->prepare($sql)->execute($setvals);
    }

    /**
     * 当 insert失败，提示主键存在，执行  ON DUPLICATE KEY UPDATE 操作。
     *
     * @param string $table
     * @param array $data
     *
     * @return bool
     * @example
     *
     *  #当ID为主键
     *  $this->save('tablename', array('id'=>1, 'name'=>'test1'));
     *  #时数据表结果是    id=1,name=test1
     *  $this->save('tablename', array('id'=>1, 'name'=>'test2'));
     *  #时数据表结果是    id=1,name=test2
     */
    public function save($table, $data)
    {
        $setkeys = $setvals = array();
        $sql     = "INSERT INTO {$this->quoteTable($table)} SET ";
        foreach ($data as $key => $val) {
            if (is_int($key)) {
                $setkeys[] = $val;
                continue;
            }
            $setkeys[] = "`$key`=?";
            $setvals[] = $val;
        }
        $sql .= implode(',', $setkeys)
            . " ON DUPLICATE KEY UPDATE "
            . implode(',', $setkeys);

        return $this->prepare($sql)->execute(array_merge($setvals, $setvals));
    }

    /**
     * UPDATE 操作
     *
     * @param string $table
     * @param array $data
     * @param array $where
     *
     * @return bool
     * @example
     * $this->update(
     *      'tablename',
     *      array('name'=>'test2','value'=>'1234'),
     *      array('id=?'=>1, ' and name=?'=>'test1', ' or `type`=?'=>'2')
     * );
     * #相当于执行 update `tablename` set name='test2', `value`='1234'
     * #         where id=1 and name='test1' or `type`='2'
     */
    public function update($table, $data, $where)
    {
        $setkeys = $setvals = $wkeys = $wvals = array();

        $sql = "UPDATE {$this->quoteTable($table)} SET ";
        foreach ($data as $key => $val) {
            if (is_int($key)) {
                $setkeys[] = $val;
                continue;
            }
            $setkeys[] = "`$key`=?";
            $setvals[] = $val;
        }

        $sql .= implode(',', $setkeys);

        if ($whereSql = $this->parseWhere($where, $setvals)) {
            $sql .= " WHERE " . $whereSql;
        }

        return $this->prepare($sql)->execute($setvals);
    }

    /**
     * 数据表DELETE操作
     *
     * @param string $table
     * @param array $where
     * @return bool
     * @example
     *      $this->delete(
     *      'tablename',
     *      array('id=?'=>1, ' and name=?'=>'test1', ' or `type`=?'=>'2')
     *      );
     *      #相当于执行 delete `tablename` where id=1 and name='test1' or `type`='2'
     */
    public function delete($table, $where = array())
    {
        $wkeys = $wvals = array();
        $sql   = "DELETE FROM {$this->quoteTable($table)} ";

        if ($whereSql = $this->parseWhere($where, $wvals)) {
            $sql .= " WHERE " . $whereSql;
        }

        return $this->prepare($sql)->execute($wvals);
    }

    public function quoteField($string)
    {
        return str_replace(' ', '', $string);
    }


    /**
     * SELECT 操作
     *
     * @param array $options
     *
     * @throws \Exception
     *
     * @return \PDOStatement
     */
    public function select($options = array())
    {
        if (is_object($options)) {
            $query = $options;
        } else if (is_string($options)) {
            $query = (object)array('queryString' => $options, 'params' => array());
        } else if (is_array($options)) {
            $query = $this->setQuery($options);
        } else {
            throw new \Exception('Error $options type');
        }

        $sth = $this->prepare($query->queryString);

        if (!$sth->execute($query->params)) {
            $errorInfo = $sth->errorInfo();
            throw new InvalidQueryException($errorInfo[2]);
        }
        return $sth;
    }

    /**
     * 设置queryString
     *
     * @param $options
     * @return object {queryString, params}
     */
    public function setQuery($options)
    {
        $sql = $params = array();

        if (!isset($options['column'])) {
            $options['column'] = '*';
        }

        $sql['column'] = 'SELECT ' . $options['column'];
        $sql['from']   = 'FROM ' . $this->quoteTable($options['from']);

        if (!empty($options['where'])) {
            $sql['where'] = 'WHERE ' . $this->parseWhere($options['where'], $params);
        }

        if (!empty($options['group'])) {
            $sql['group'] = 'GROUP BY ' . $options['group'];
        }

        if (!empty($options['having'])) {
            $sql['having'] = 'HAVING ' . $this->parseWhere($options['having'], $params);
        }

        if (!empty($options['order'])) {
            $sql['order'] = 'ORDER BY ' . $options['order'];
        }

        if (!empty($options['limit'])) {
            $sql['limit'] = "\nLIMIT " . $options['limit'];
        }

        return (object)array(
            'queryString' => implode(' ', $sql),
            'params'      => $params
        );
    }

    /**
     * 设置SQL的where条件
     *
     * @param array $where
     * @param array $params
     *
     * @return string
     */
    protected function parseWhere($where, &$params = array())
    {
        $wkeys = array();
        if (is_array($where)) foreach ($where as $key => $val) {
            if (is_int($key)) {
                $wkeys[] = $val;
                continue;
            }
            $wkeys[] = "`$key`=?";
            $params  = array_merge($params, (array)$val);
        }

        return implode(' AND ', $wkeys);
    }
}