<?php
return array(
    'console' => array(
        'router' => array(
            'routes' => array(
                'create-model' => array(
                    'options' => array(
                        'route'    => 'g create <tableName> <moduleName> [<db>] [-e|-re] [-t] [--name=] [--schema=]',
                        'defaults' => array(
                            'module'     => 'gzfextra',
                            'controller' => 'console',
                            'action'     => 'create',
                        ),
                    ),
                ),
            ),
        ),
    ),
    'gzfextra_console_create' => array(
        realpath(__DIR__ . '/../view'),
    ),
);