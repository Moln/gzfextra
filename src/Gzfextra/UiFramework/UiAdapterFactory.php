<?php

namespace Gzfextra\UiFramework;


use Zend\ServiceManager\Config;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class UiAdapterFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $configs = $serviceLocator->get('config');

        $managerConfig = null;
        if (isset($configs['gzfextra']['ui_adapter_manager'])) {
            $managerConfig = new Config($configs['gzfextra']['ui_adapter_manager']);
        }

        return new UiAdapterManager($managerConfig);
    }
}