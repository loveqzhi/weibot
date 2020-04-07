<?php
Yii::setAlias('@console', dirname(__DIR__) . '/console');
$params = require __DIR__ . '/params.php';
return [
    'id'                  => 'basic-console',
    'basePath'            => dirname(__DIR__),
    'bootstrap'           => ['log'],
    'controllerNamespace' => 'app\commands',
    'components'          => [
        'log'        => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets'    => [
                [
                    'class'          => 'yii\log\FileTarget',
                    'logFile'        => '/home/wwwlogs/paidui/' . date('Y-m-d') . '.log',
                    //'levels'         => ['error', 'warning', 'info'],
                    'levels'         => ['error'],
                    'logVars'        => [],
                    'exportInterval' => 0,
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName'  => false,
            'rules'           => [
            ],
        ],
        'db' => [
            'class'               => 'yii\db\Connection',
            'dsn'                 => 'mysql:host=127.0.0.1;port=3306;dbname=spiders',
            'emulatePrepare'      => true,
            'username'            => 'root',
            'password'            => '123456',
            'charset'             => 'utf8mb4',
            'enableSchemaCache'   => true,
            'schemaCacheDuration' => 86400,
        ],
        'spiders' => [
            'class'               => 'yii\db\Connection',
            'dsn'                 => 'mysql:host=127.0.0.1;port=3306;dbname=spiders',
            'emulatePrepare'      => true,
            'username'            => 'root',
            'password'            => '123456',
            'charset'             => 'utf8mb4',
            'enableSchemaCache'   => true,
            'schemaCacheDuration' => 86400,
        ],
        'config'       => [
             'class'               => 'yii\db\Connection',
            'dsn'                 => 'mysql:host=127.0.0.1;port=3306;dbname=spiders',
            'emulatePrepare'      => true,
            'username'            => 'root',
            'password'            => '123456',
            'charset'             => 'utf8mb4',
            'enableSchemaCache'   => true,
            'schemaCacheDuration' => 86400,
        ],
        'curl' => [
            'class' => 'yii\curl\Curl'
        ],
        'redis' => [
            'class' => 'BaseComponents\base\RedisConn',
            'hostname'  => '127.0.0.1',
            'port'  => 6379,
        ],
        'remote_server' => [
            'class' => 'app\config\RemoteServer',
        ]
    ],
    'params'              => $params,
];
