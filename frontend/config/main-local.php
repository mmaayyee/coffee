<?php
switch (YII_ENV) {
    case 'dev':
        return [];
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
                                'subject' => 'erptest frontend log message',
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
                                'subject' => 'erppre frontend log message',
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
                                'subject' => 'erp frontend log message',
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
