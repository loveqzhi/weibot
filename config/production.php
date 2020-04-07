<?php
$params = require __DIR__ . '/params.php';
$config = [
    'id' => 'basic',
    'vendorPath' => WEBROOT . '/framework/vendor',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        'log'
    ],
    'components' => [
        'request' => [
            'enableCookieValidation' => true,
            'enableCsrfValidation'   => false,
            'cookieValidationKey' => '@sdf@sdf#',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error'
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules'           => [],
        ],
        'log' => [
            'traceLevel' => 3,
            'targets' => [
                [
                    'class' => 'BaseComponents\customLog\CustomFileLog',
                    'logFile' => WEBROOT . '/logs/' . date('Y-m-d') . '.log',
                    'levels' => ['error','warning'],
                    'logVars' => [],
                    'exportInterval' => 0
                ]
            ]
        ],
        'db' => [
            'class'               => 'yii\db\Connection',
            'dsn'                 => 'mysql:host=172.18.0.1;port=3306;dbname=cigarette_case',
            'emulatePrepare'      => true,
            'username'            => 'root',
            'password'            => 'yundingzhi2019',
            'charset'             => 'utf8mb4',
            'enableSchemaCache'   => false,
            'schemaCacheDuration' => 86400,
        ],
        'config'       => [
             'class'               => 'yii\db\Connection',
            'dsn'                 => 'mysql:host=172.18.0.1;port=3306;dbname=cigarette_case',
            'emulatePrepare'      => true,
            'username'            => 'root',
            'password'            => 'yundingzhi2019',
            'charset'             => 'utf8mb4',
            'enableSchemaCache'   => true,
            'schemaCacheDuration' => 86400,
        ],
        'redis'        => [
            'class'    => 'BaseComponents\base\RedisConn',
            'hostname' => '127.0.0.1',
            'port'     => 6379,           
        ],
        'curl'         => [
            'class' => 'yii\curl\Curl',
        ],
        'session' => [
                'class' => 'yii\web\Session',
                //'savePath' => '/tmp/ddz_api',
        ],
    ],
    'modules'    => [
		'member' => [
            'class' => 'app\modules\member\Module',
        ],
        'wechat' => [
            'class' => 'app\modules\wechat\Module',
        ],
        'api' => [
            'class' => 'app\modules\api\Module',
        ],
        'admin' => [
            'class' => 'app\modules\admin\Module',
        ],
	],
    'params'     => $params,
];

return $config;
