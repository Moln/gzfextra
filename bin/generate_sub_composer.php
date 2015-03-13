<?php

$readmeTpl = <<<EOT
Zend framework 2 Db extra
=========================


##Installation using Composer

```
{
    "require": {
        "moln/gzfextra-{component}": "1.*"
    }
}
```
EOT;


$composerJsonTpl = <<<EOT
{
    "name": "moln/gzfextra-{component}",
    "license": "MIT",
    "keywords": ["gzfextra","zendframework", "zf2", "extra"],
    "description": "Zend framework 2 extra",
    "authors": [
        {
            "name": "Moln",
            "email": "xiemaomao520@163.com"
        }
    ],
    "autoload": {
        "psr-4": {
            "Gzfextra\\\\{Component}\\\\": ""
        }
    },
    "target-dir": "Gzfextra/{Component}",
    "require": {
        "php": ">=5.4"{require}
    }
}
EOT;

$requires = array(
    'Db' => ",\n        \"zendframework/zend-db\": \"2.*\""
);

$dir = realpath(__DIR__ . '/../');
foreach (glob($dir . '/src/Gzfextra/*', GLOB_ONLYDIR) as $dir) {
    echo $dir, "\n";
    $name         = basename($dir);
    $lname        = strtolower($name);
    $readme       = str_replace(['{Component}', '{component}'], [$name, $lname], $readmeTpl);
    $composerJson = str_replace(
        ['{Component}', '{component}', '{require}'],
        [$name, $lname, isset($requires[$name]) ? $requires[$name] : ''],
        $composerJsonTpl
    );

    file_put_contents($dir . '/README.md', $readme);
    file_put_contents($dir . '/composer.json', $composerJson);
}