<?php

namespace Gzfextra\Mvc\Controller;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


/**
 * Class AbstractControllerLoaderFactory
 *
 * @package Gzfextra\Mvc\Controller
 * @author  Xiemaomao
 * @version $Id$
 */
class ControllerLoaderAbstractFactory implements AbstractFactoryInterface
{
    protected $classes = [];

    /**
     * Determine if we can create a service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param $name
     * @param $requestedName
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        if (!isset($this->classes[$requestedName])) {
            $names    = explode('\\', $requestedName);
            $ctrlName = end($names) . 'Controller';

            array_splice($names, -1, 1, array('Controller', $ctrlName));

            $class    = implode('\\', $names);

            $this->classes[$requestedName] = $class;
        } else {
            $class = $this->classes[$requestedName];
        }

        return class_exists($class);
    }

    /**
     * Create service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param $name
     * @param $requestedName
     * @return mixed
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return new $this->classes[$requestedName];
    }
}