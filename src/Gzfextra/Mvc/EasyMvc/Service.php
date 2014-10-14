<?php
namespace Gzfextra\Mvc\EasyMvc;

/**
 * ServiceFactory.php
 *
 * @author   maomao
 * @DateTime 12-7-23 下午2:33
 * @version  $Id: Service.php 790 2013-03-15 08:56:56Z maomao $
 */
class Service
{
    public static function factory($protocol, $serviceName)
    {
        $className = __NAMESPACE__ . '\\Protocol\\' . ucfirst($protocol);
        if (!in_array(__NAMESPACE__ . '\\Protocol\ProtocolInterface', class_implements($className))) {
            throw new \RuntimeException('错误协议：' . $protocol);
        }

        /** @var $service Protocol\AbstractProtocol */
        $service = new $className($serviceName);

        return $service;
    }
}
