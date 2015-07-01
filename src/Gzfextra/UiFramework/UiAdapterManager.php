<?php

namespace Gzfextra\UiFramework;


use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ConfigInterface;
use Zend\ServiceManager\Exception;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\Stdlib\InitializableInterface;

class UiAdapterManager extends AbstractPluginManager
{
    protected $invokableClasses = [
        'kendo' => 'Gzfextra\UiFramework\UiAdapter\Kendo'
    ];

    public function __construct(ConfigInterface $configuration = null)
    {
        parent::__construct($configuration);
        $this->addInitializer(function ($instance) {
            if ($instance instanceof ServiceManagerAwareInterface) {
                $instance->setServiceManager($this->getServiceLocator());
            }
            if (method_exists($instance, 'setRequest')) {
                $instance->setRequest($this->getServiceLocator()->get('Request'));
            }

            if ($instance instanceof InitializableInterface) {
                $instance->init();
            }
        });
    }

    /**
     * Validate the plugin
     *
     * Checks that the filter loaded is either a valid callback or an instance
     * of FilterInterface.
     *
     * @param  mixed $plugin
     * @return void
     * @throws Exception\RuntimeException if invalid
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof UiAdapter\UiAdapterInterface) {
            // we're okay
            return;
        }

        throw new Exception\InvalidArgumentException(sprintf(
            'Plugin of type %s is invalid; must extend %s\UiAdapter\UiAdapterInterface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin)),
            __NAMESPACE__
        ));
    }
}