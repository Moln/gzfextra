<?php

namespace Gzfextra\Mvc\EasyMvc\Controller;

use Gzfextra\Mvc\EasyMvc\Application;
use Zend\Mvc\Controller\PluginManager;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\DispatchableInterface;
use Zend\Stdlib\RequestInterface;
use Zend\Stdlib\ResponseInterface;


/**
 * Class AbstractController
 *
 * @package Gzfextra\Mvc\EasyMvc\Controller
 * @author  Xiemaomao
 * @version $Id$
 */
abstract class AbstractController implements DispatchableInterface
{
    protected $request;
    protected $serviceLocator;
    protected $event;
    protected $plugins;

    /**
     * @param mixed $event
     */
    public function setEvent($event)
    {
        $this->event = $event;
    }

    /**
     * @return \Zend\Mvc\MvcEvent
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Request
     *
     * @return \Gzfextra\Mvc\EasyMvc\Http\Request
     */
    protected function getRequest()
    {
        return $this->request;
    }

    protected function params($name, $default = null)
    {
        $rm = $this->getEvent()->getRouteMatch();
        if (($value = $rm->getParam($name)) !== null) {
            return $value;
        }
        return $this->getRequest()->getParam($name, $default);
    }

    /**
     * @return ServiceManager
     */
    public function getServiceLocator()
    {
        if (!$this->serviceLocator) {
            $this->serviceLocator = Application::getInstance()->getServiceManager();
        }

        return $this->serviceLocator;
    }

    /**
     * Method overloading: return/call plugins
     *
     * If the plugin is a functor, call it, passing the parameters provided.
     * Otherwise, return the plugin instance.
     *
     * @param  string $method
     * @param  array $params
     * @return mixed
     */
    public function __call($method, $params)
    {
        $plugin = $this->plugin($method);
        if (is_callable($plugin)) {
            return call_user_func_array($plugin, $params);
        }

        return $plugin;
    }

    /**
     * Get plugin manager
     *
     * @return PluginManager
     */
    public function getPluginManager()
    {
        if (!$this->plugins) {
            $this->setPluginManager(new PluginManager());
        }

        $this->plugins->setController($this);
        return $this->plugins;
    }

    /**
     * Set plugin manager
     *
     * @param  PluginManager $plugins
     * @return AbstractController
     */
    public function setPluginManager(PluginManager $plugins)
    {
        $this->plugins = $plugins;
        $this->plugins->setController($this);

        return $this;
    }

    /**
     * Get plugin instance
     *
     * @param  string $name        Name of plugin to return
     * @param  null|array $options Options to pass to plugin constructor (if not already instantiated)
     * @return mixed
     */
    public function plugin($name, array $options = null)
    {
        return $this->getPluginManager()->get($name, $options);
    }

    /**
     * Dispatch a request
     *
     * @param RequestInterface $request
     * @param null|ResponseInterface $response
     *
     * @return \Zend\Stdlib\Response|mixed
     */
    public function dispatch(RequestInterface $request, ResponseInterface $response = null)
    {
        $this->request = $request;

        $e = $this->getEvent();

        $routeMatch = $e->getRouteMatch();

        $action = $routeMatch->getParam('action', 'not-found');
        $method = static::getMethodFromAction($action);

        if (!method_exists($this, $method)) {
            $method = 'notFoundAction';
        }

        $e->setResult($this->$method());
    }

    public static function getMethodFromAction($action)
    {
        $method = str_replace(array('.', '-', '_'), ' ', $action);
        $method = ucwords($method);
        $method = str_replace(' ', '', $method);
        $method = lcfirst($method);
        $method .= 'Action';

        return $method;
    }

    public function notFoundAction()
    {
        header("HTTP/1.0 404 Not Found");
        echo 'Page not found.';
        exit;
    }
}