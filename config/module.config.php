<?php
return array(
    'router' => array(
        'routes' => array(
            'module' => array(
                'type'         => 'segment',
                'options'      => array(
                    'route'       => '/:module[/][:controller[/:action]]',
                    'constraints' => array(
                        'module'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults'    => array(
                        'controller' => 'index',
                        'action'     => 'index',
                    ),
                ),
                'child_routes' => array(
                    'params' => array(
                        'type' => 'Wildcard',
                    ),
                ),
            ),
        ),
    ),
);