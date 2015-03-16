<?php
namespace Gzfextra\Mvc\EasyMvc\Protocol;

/**
 * Wsdl.php
 *
 * @author   moln.xie@gmail.com
 * @DateTime 12-7-20 下午2:14
 */
class Wsdl extends AbstractProtocol
{

    protected $serviceName;

    public function __construct($serviceName)
    {
        $this->serviceName = $serviceName;
    }

    public function handle()
    {
        $uri = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $uri .= strpos($uri, '?') === false ? '?wsdl' : '&wsdl';
        if (isset($_REQUEST['wsdl'])) {
            $wsdl = new \Zend_Soap_AutoDiscover();
            $wsdl->setClass($this->serviceName);
            $wsdl->handle();
        } else {
            $server = new \Zend_Soap_Server($uri, array('soap_version' => SOAP_1_2));
            $server->setClass($this->serviceName);
            $server->handle();
        }
    }
}