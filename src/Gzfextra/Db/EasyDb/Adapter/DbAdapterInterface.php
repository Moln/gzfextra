<?php
/**
 *
 * User: xiemaomao
 * Date: 14-4-25
 * Time: 下午1:25
 */

namespace Gzfextra\Db\EasyDb\Adapter;


interface DbAdapterInterface
{
    public function insert($table, $data);

    public function update($table, $data, $where);

    public function select($where);
}