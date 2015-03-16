gzfextra
========

Zend framework 2 extra
## Installation using Composer

```
{
    "require": {
        "moln/gzfextra": "1.0"
    }
}
```

## Db - 数据库抽象调用

ZF2 自建 `TableGateway` 方法比较麻烦, 每个Table 要在 service_manager 加配置.
`Gzfextra\Db\TableGateway\TableGatewayAbstractServiceFactory` 为抽象常用 TableGateway 调用.

### Example - 使用举例

module.config.php
```php
return array(
    'service_manager'    => array(
        'abstract_factories' => array(
            'Gzfextra\Db\TableGateway\TableGatewayAbstractServiceFactory',
        ),
    ),
    'tables' => array(
        'UserTable'  => array(
            'table'     => 'users',
            'invokable' => 'User\\Model\\UserTable',
            'primary'   => 'user_id',
        ),
        'RoleTable' => array(
            'table'     => 'roles',
            'invokable' => 'User\\Model\\RoleTable',
            'primary'   => 'role_id',
        ),
    ),
);
```

User\Model\UserTable.php
```php
namespace User\Model;
use Zend\Db\TableGateway\TableGateway as ZendTableGateway;

/**
 * UserTable
 *
 * 默认带有公共函数特性
 * @method int fetchCount($where)
 * @method \ArrayObject|\Zend\Db\RowGateway\RowGateway find($id)
 * @method \Zend\Paginator\Paginator fetchPaginator($where = null)
 * @method int deletePrimary($key)
 */
class UserTable extends ZendTableGateway
{
}
```

控制器调用
```php
public function indexAction()
{
    $users = $this->getServiceLocator()->get('UserTable');
    $row = $users->find(1);
}
```

## Router - 公共路由

ZF2 自带的 `Zend\Mvc\ModuleRouteListener` 不方便, 每新的 `Controller` 都需要在 `ControllerPlugin` 配置下.
`Gzfextra\Router\GlobalModuleRouteListener`

### Example - 使用举例

```php
class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();

        $gListener = new GlobalModuleRouteListener();
        $eventManager->attach($gListener);
    }

    public function getConfig()
    {
        return GlobalModuleRouteListener::getDefaultRouterConfig();
    }
}
```

默认路由方式: `/module/controller/action/param1/value1/param2/value2/...`


## FileStorage - 文件存储抽象

文件存储模式
- Filesystem (存储文件系统
- Ftp (存储FTP
- Sftp (存储sftp


### Example - 使用举例

module.config.php

工厂方式
```php
return array(
    'service_manager' => array(
        'factories' => array(
            'FileStorage' => '\Gzfextra\FileStorage\StorageFactory'
        )
    ),
    'file_storage'    => array(
        'type'    => 'fileSystem',
        'options' => array(
            'default_path' => realpath('./public/uploads'),
            'validators'   => array(
                'Extension' => array('gif', 'jpg', 'jpeg', 'png'),
                'Size'      => array('max' => 1024 * 1024),
                'IsImage',
            ),
            'filters'      => array(
                'LowerCaseName',
                'RenameUpload' => array(
                    'target'               => 'shop',
                    'use_upload_extension' => true,
                    'randomize'            => true,
                ),
            ),
        ),
    ),
);
```

抽象工厂
```php
return array(
    'service_manager' => array(
        'abstract_factories' => array(
            '\Gzfextra\FileStorage\StorageAbstractFactory'
        ),
    ),
    'file_storage_configs' => array(
        'ImageFileStorage'    => array(
            'type'    => 'fileSystem',
            'options' => array(
                'default_path' => realpath('./public/uploads'),
                'validators'   => array(
                    'Extension' => array('gif', 'jpg', 'jpeg', 'png'),
                    'Size'      => array('max' => 1024 * 1024),
                    'IsImage',
                ),
                'filters'      => array(
                    'LowerCaseName',
                    'RenameUpload' => array(
                        'target'               => 'shop',
                        'use_upload_extension' => true,
                        'randomize'            => true,
                    ),
                ),
            ),
        ),
        'ZipFileStorage'    => array(
            'type'    => 'ftp',
            'options' => array(
                'ftp' => array(
                    'host' => 'localhost',
                    'username' => 'ftpuser',
                    'password' => '123456',
                    'pasv' => true,
                ),
                'default_path' => '/',
                'validators'   => array(
                    'Extension' => array('zip'),
                ),
                'filters'      => array(
                    'LowerCaseName',
                    'RenameUpload' => array(
                        'target'               => 'zipfile',
                        'use_upload_extension' => true,
                        'randomize'            => true,
                    ),
                ),
            ),
        ),
    ),
);
```

控制器调用
```php
public function indexAction()
{
    $fileStorage = $this->getServiceLocator()->get('ImageFileStorage');
    if ($fileStorage->isValid()) {
        $file = $fileStorage->upload($file);
        // var_dump($file);
    }
}
```


## Tool - Gzfextra 工具

依赖 `zendframework/zftool`

## EasyMvc - (正在评估ZF2 框架流程是否能简化部分)

## Mvc - 扩展Mvc模块

- `Gzfextra\Controller\Plugin\Params` 类似 ZF1的 $this->getParam(), 默认取路由, 不存在取POST&GET

## UiFramework

目前限 KendoUi
- KendoUi 的控制器插件
