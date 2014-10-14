<?php

namespace Gzfextra\Mvc;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\Mvc\MvcEvent;


/**
 * Class GlobalModuleRouteListener
 * @package Gzfextra\Mvc
 * @author Xiemaomao
 * @version $Id$
 */
class GlobalModuleRouteListener implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    /**
     * Attach one or more listeners
     *
     * Implementors may add an optional $priority argument; the EventManager
     * implementation will pass this to the aggregate.
     *
     * @param EventManagerInterface $events
     *
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, array($this, 'onRoute'));
    }

    /**
     * Global Route /module/ctrl/action
     * @param MvcEvent $e
     */
    public function onRoute(MvcEvent $e)
    {
        $matches    = $e->getRouteMatch();
        $module     = $matches->getParam('module');
        $controller = $matches->getParam('controller');

        if ($module && $controller && strpos($controller, '\\') === false) {
            $matches->setParam('controller_name', $controller);

            /** @var \Zend\Mvc\Controller\ControllerManager $controllerLoader */
            $controllerLoader = $e->getApplication()->getServiceManager()->get('ControllerLoader');

            $ctrlClass = ucfirst($module) . '\\Controller\\';
            $ctrlClass .= str_replace(' ', '', ucwords(str_replace('-', ' ', $controller)));
            $controller = $ctrlClass;
            $matches->setParam('controller', $controller);

            $ctrlClass .= 'Controller';
            if (!$controllerLoader->has($controller) && class_exists($ctrlClass)) {
                $controllerLoader->setInvokableClass($controller, $ctrlClass);
                $e->setController($controller);
                $e->setControllerClass($ctrlClass);
            }
        }
    }
}