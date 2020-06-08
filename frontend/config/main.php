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
    'id'                  => 'app-frontend',
    'basePath'            => dirname(__DIR__),
    'controllerNamespace' => 'frontend\controllers',
    'defaultRoute'        => '/site/index',
    'modules'             => [
        'api' => [
            'class' => 'frontend\modules\api\Module',
        ],
    ],
    'components'          => [
        'user'         => [
            'identityClass'   => 'frontend\models\User',
            'enableAutoLogin' => true,
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
