<?php
$params = array_merge(
    require (__DIR__ . '/../../common/config/params.php'),
    require (__DIR__ . '/params.php'),
    require (__DIR__ . '/params-local.php')
);
switch (YII_ENV) {
    case 'dev':
        $params = array_merge(
            $params,
            require (__DIR__ . '/../../common/config/params-dev.php')
        );
        break;
    case 'test':
        $params = array_merge(
            $params,
            require (__DIR__ . '/../../common/config/params-test.php')
        );
        break;
    case 'pre':
        $params = array_merge(
            $params,
            require (__DIR__ . '/../../common/config/params-pre.php')
        );
        break;
    case 'prod':
        $params = array_merge(
            $params,
            require (__DIR__ . '/../../common/config/params-prod.php')
        );
        break;
    default:
        break;
}

return [
    'id'                  => 'app-backend',
    'basePath'            => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'defaultRoute'        => '/site/index',
    'modules'             => [
        'gridview' => [
            'class' => '\kartik\grid\Module',
        ],
        'redactor' => [
            'class'                => 'yii\redactor\RedactorModule',
            'uploadDir'            => './uploads/shop-goods',
            'uploadUrl'            => 'http://bb.erp.com/uploads/shop-goods',
            'imageAllowExtensions' => ['jpg', 'png', 'gif'],
        ],
        'service'  => [
            'class' => 'backend\modules\service\Module',
        ],
    ],
    'components'          => [
        'authManager'  => [
            'class' => 'yii\rbac\DbManager',
        ],
        'user'         => [
            'identityClass'   => 'backend\models\Manager',
            'enableAutoLogin' => true,
        ],
        'assetManager' => [
            'assetMap' => [
                'jquery.js' => '@web/js/jquery-1.9.1.min.js',
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'request'      => [
            'cookieValidationKey' => 'jnOO5bIphDJrj0OMYPVB0AqJEFMFMPm0',
        ],
    ],
    'params'              => $params,
];
