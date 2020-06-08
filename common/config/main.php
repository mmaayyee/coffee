<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'name'       => '咖啡零点吧',
    'language'   => 'zh-CN',
    'bootstrap'  => ['log'],
    'components' => [
        'cache'      => [
            'class' => 'yii\caching\FileCache',
        ],
        'db'         => include __DIR__ . '/db.php',
        'log'        => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets'    => [
                [
                    'class'  => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName'  => true,
            'rules'           => [],
        ],
        'mailer'     => [
            'class'            => 'yii\swiftmailer\Mailer',
            'viewPath'         => '@common/mail',
            'useFileTransport' => false,
            'transport'        => [
                'class'      => 'Swift_SmtpTransport',
                'host'       => 'smtphz.qiye.163.com',
                'username'   => 'invoice@coffee08.com',
                'password'   => 'Coffee2015',
                'port'       => '465',
                'encryption' => 'ssl',
            ],
            'messageConfig'    => [
                'charset' => 'UTF-8',
                'from'    => ['invoice@coffee08.com' => '咖啡零点吧'],
            ],
        ],
    ],
];
