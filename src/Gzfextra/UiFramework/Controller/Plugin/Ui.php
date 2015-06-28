<?php

namespace Gzfextra\UiFramework\Controller\Plugin;

use Gzfextra\UiFramework\UiAdapterManager;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Class Ui
 *
 * @author  Moln Xie
 */
class Ui extends AbstractPlugin
{
    protected $uiAdapterManager;

    public function __invoke()
    {
        $sl = $this->getServiceLocator();
        $configs = $sl->get('config');

        if (isset($configs['gzfextra']['ui_adapter_config']['adapter'])) {
            $uiConfig = $configs['gzfextra']['ui_adapter_config'];
        } else {
            $uiConfig = [
                'adapter' => 'kendo',
                'options' => [
                    'pageSizes' => [15, 20, 50, 100]
                ]
            ];
        }

        return $this->getUiAdapterManager()->get($uiConfig['adapter'], $uiConfig['options']);
    }

    /**
     * @return UiAdapterManager
     */
    public function getUiAdapterManager()
    {
        if (!$this->uiAdapterManager) {
            $sl = $this->getServiceLocator();
            if (!$sl->has('Gzfextra\UiFramework\UiAdapterManager')) {
                $sl->setFactory('Gzfextra\UiFramework\UiAdapterManager', 'Gzfextra\UiFramework\UiAdapterFactory');
            }

            $this->uiAdapterManager = $sl->get('Gzfextra\UiFramework\UiAdapterManager');
        }
        return $this->uiAdapterManager;
    }

    /**
     * @param UiAdapterManager $uiAdapterManager
     * @return $this
     */
    public function setUiAdapterManager(UiAdapterManager $uiAdapterManager)
    {
        $this->uiAdapterManager = $uiAdapterManager;
        return $this;
    }

    /**
     * @return \Zend\ServiceManager\ServiceManager
     */
    private function getServiceLocator()
    {
        return $this->getController()->getServiceLocator();
    }
}