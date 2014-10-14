<?php

namespace Gzfextra\Mvc\EasyMvc\Protocol;

/**
 * Xml.php
 *
 * @author   maomao
 * @DateTime 12-7-24 上午11:00
 * @version  $Id: Xml.php 1210 2013-08-08 01:49:52Z maomao $
 */
class Json extends AbstractProtocol
{
    /**
     * @param string $root
     * @return $this
     */
    public function setRoot($root)
    {
        $this->root = $root;
        return $this;
    }

    public function handle()
    {
        if (!headers_sent()) {
//            header('Content-Type: application/json');
        }
        echo json_encode($this->callMethod());
    }
}
