<?php
namespace Gzfextra\Mvc\EasyMvc\Protocol;

/**
 * ProtocolAbstract.php
 *
 * @author   maomao
 * @DateTime 12-7-23 下午5:18
 * @version  $Id: ProtocolAbstract.php 1218 2013-08-08 09:51:29Z maomao $
 */
abstract class AbstractProtocol implements ProtocolInterface
{
    protected $service;
    protected $params;
    protected $action;

    public function __construct($serviceName)
    {
        $this->service = new $serviceName;
        $this->params  = $_REQUEST;
    }

    /**
     * @param string $method
     *
     * @return AbstractProtocol
     */
    public function setAction($method)
    {
        $this->action = $method;
        return $this;
    }

    public function callMethod()
    {
        $reflect = new \ReflectionClass($this->service);
        $method  = $this->action . 'Action';

        if (!method_exists($this->service, $method)) {
            header("HTTP/1.0 404 Not Found");
            if (APPLICATION_ENV != 'production') {
                echo 'Not found.';
            }
            exit;
        }
        $refMethod = $reflect->getMethod($method);

        $realParams = array();
        foreach ($refMethod->getParameters() as $param) {
            if (isset($this->params[$param->getName()])) {
                $realParams[] = $this->params[$param->getName()];
            } else if ($param->isDefaultValueAvailable()) {
                $realParams[] = $param->getDefaultValue();
            } else {
                throw new \InvalidArgumentException('Unknown param:' . $param->getName());
            }
        }

        return call_user_func_array([$this->service, $method], $realParams);
    }
}