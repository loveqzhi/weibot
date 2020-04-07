<?php
require_once WEBROOT . '/framework/vendor/php-console/src/PhpConsole/__autoload.php';

$params = require __DIR__ . '/params.php';
$config = [
    'id'         => 'basic',
    'vendorPath' => WEBROOT . '/framework/vendor',
    'basePath'   => dirname(__DIR__),
    'bootstrap'  => ['log'],
    'components' => [
        'request'      => [
            'enableCookieValidation' => true,
            'enableCsrfValidation'   => false,
            'cookieValidationKey' => '@sdf@sdf#',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer'       => [
            'class'            => 'yii\swiftmailer\Mailer',
            'useFileTransport' => true,
        ],
        'urlManager'   => [
            'enablePrettyUrl' => true,
            'showScriptName'  => false,
            'rules'           => [],
        ],
        'log'          => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets'    => [
                [
                    'class'          => 'BaseComponents\customLog\CustomFileLog',
                    'logFile'        => WEBROOT . '/logs/' . date('Y-m-d') . '.log',
                    'levels'         => ['error', 'warning', 'info'],
                    'logVars'        => [],
                    'exportInterval' => 0,
                ],
            ],
        ],
        'db' => [
            'class'               => 'yii\db\Connection',
            'dsn'                 => 'mysql:host=172.17.0.1;port=3306;dbname=cigarette_case',
            'emulatePrepare'      => true,
            'username'            => 'root',
            'password'            => '123456',
            'charset'             => 'utf8mb4',
            'enableSchemaCache'   => true,
            'schemaCacheDuration' => 86400,
        ],
        'config'       => [
             'class'               => 'yii\db\Connection',
            'dsn'                 => 'mysql:host=172.17.0.1;port=3306;dbname=cigarette_case',
            'emulatePrepare'      => true,
            'username'            => 'root',
            'password'            => '123456',
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
                //'savePath' => '/tmp/yuncaifu',
                /*'class' => 'yii\redis\Session',
                'redis' => [
                      'hostname' => '127.0.0.1',
                      'port'     => '6379',
                      //'password' => Yaconf::get('web_env.REDIS_ETC1_PASSWORD'),
                ] 
                */
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

if (YII_ENV == 'dev') {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][]      = 'debug';
    $config['modules']['debug'] = [
        'class'     => 'yii\debug\Module',
        //'allowedIPs' => ['172.16.*.*', '192.168.*.*'], // 按需调整这里
    ];

    $config['bootstrap'][]    = 'gii';
    $config['modules']['gii'] = [
        'class'      => 'yii\gii\Module',
        'allowedIPs' => ['127.0.0.1', '172.16.*.*', '192.168.*.*','::1'], // 按需调整这里
    ];
}

return $config;
