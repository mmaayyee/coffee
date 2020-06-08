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
                                'subject' => 'erptest console log message',
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
                                'subject' => 'erppre console log message',
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
                                'subject' => 'erp console log message',
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
