<?php

namespace Gzfextra\Mvc\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;


/**
 * Class Cache
 *
 * @package Gzfextra\Mvc\Controller\Plugin
 * @author  Xiemaomao
 * @version $Id$
 */
class Cache extends AbstractPlugin
{

    public function __invoke($message, $code = 1, $more = array())
    {
        if ($message) {
            $more['msg'] = $message;
        }

    }
}