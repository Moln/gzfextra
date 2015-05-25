<?php

namespace Gzfextra\Mvc\Controller\Plugin;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;


/**
 * ServiceManager Get
 * @package Gzfextra\Mvc\Controller\Plugin
 * @author Xiemaomao
 * @version $Id$
 */
class Get extends AbstractPlugin implements ServiceManagerAwareInterface
{
    protected $serviceManager;

    /**
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * @param ServiceManager $serviceManager
     * @return $this
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
        return $this;
    }

    public function __invoke($name, $usePeeringServiceManagers = true)
    {
        return $this->getServiceManager()->get($name, $usePeeringServiceManagers);
    }
}