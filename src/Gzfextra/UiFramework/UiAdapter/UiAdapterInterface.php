<?php
namespace Gzfextra\UiFramework\UiAdapter;

interface UiAdapterInterface
{
    public function filter($fieldMap = array());

    public function sort();

    public function page();

    public function result($data, $total = null, array $dataTypes = null);

    public function errors($messages);
}