<?php

namespace Gzfextra\Mvc\EasyMvc\Protocol;

use Zend\Serializer\Serializer;

/**
 * Serialize.php
 *
 * @author   moln.xie@gmail.com
 * @DateTime 12-7-23 下午5:16
 */
class Serialize extends AbstractProtocol
{
    protected $adapter = 'phpSerialize';

    public function handle()
    {
        if (!headers_sent()) {
            switch ($this->getAdapter()) {
                case 'json':
                    header('Content-Type: application/json');;
                    break;
                case 'wddx':
                    header('Content-Type: text/xml');;
                    break;
                default:
                    break;
            }
        }
        echo Serializer::serialize($this->callMethod(), ['adapter' => $this->getAdapter()]);
    }

    public function getAdapter()
    {
        return $this->adapter;
    }

    public function setAdapter($adapter)
    {
        $this->adapter = $adapter;
        return $this;
    }
}
