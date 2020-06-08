<?php
switch (YII_ENV) {
    case 'dev':
        return [
            'bootstrap' => ['debug', 'gii'],
            'modules'   => [
                'debug' => 'yii\debug\Module',
                'gii'   => 'yii\gii\Module',
            ],
        ];
        break;
    case 'test':
        return [
            'components' => [
                'log' => [
                    'targets' => [
                        'email' => [
                            'class'   => 'yii\log\EmailTarget',
                            'levels'  => ['error'],
                            'message' => [
                                'to'      => ['gangwei.zheng@coffee08.com'],
                                'subject' => 'erptest backend log message',
                            ],
                        ],
                    ],
                ],
            ],
        ];
        break;
    case 'pre':
        return [
            'components' => [
                'log' => [
                    'targets' => [
                        'email' => [
                            'class'   => 'yii\log\EmailTarget',
                            'levels'  => ['error'],
                            'message' => [
                                'to'      => ['gangwei.zheng@coffee08.com'],
                                'subject' => 'erppre backend log message',
                            ],
                        ],
                    ],
                ],
            ],
        ];
        break;
    case 'prod':
        return [
            'components' => [
                'log' => [
                    'targets' => [
                        'email' => [
                            'class'   => 'yii\log\EmailTarget',
                            'levels'  => ['error'],
                            'message' => [
                                'to'      => ['gangwei.zheng@coffee08.com'],
                                'subject' => 'erp backend log message',
                            ],
                        ],
                    ],
                ],
            ],
        ];
        break;
    default:
        return [];
        break;
}
