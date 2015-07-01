<?php

namespace Gzfextra\Mvc\Controller\Plugin;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;


/**
 * ServiceManager Get
 * @package Gzfextra\Mvc\Controller\Plugin
 * @author Xiemaomao
 * @version $Id$
 */
class Get extends AbstractPlugin
{
    public function __invoke($name, $usePeeringServiceManagers = true)
    {
        return $this->getController()->getServiceLocator()->get($name, $usePeeringServiceManagers);
    }
}