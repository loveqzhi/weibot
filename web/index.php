<?php
define('CURRENT_TIMESTAMP', $_SERVER['REQUEST_TIME']);
define('CURRENT_DATETIME', date('Y-m-d H:i:s'));
define('COOKIE_PATH', '/');
define('COOKIE_DOMAIN', 'ydz.hshshyw.com');
define('COOKIE_PREFIX', 'LG_');
define('COOKIE_EXPIRE', 7);
define('WEBROOT', dirname(__DIR__));


if (isset($_SERVER['MB_APPLICATION']) && $_SERVER['MB_APPLICATION'] == 'production') {
    defined('YII_DEBUG') or define('YII_DEBUG', false);
    defined('YII_ENV') or define('YII_ENV', 'pro');
    defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 1);
    $config = require __DIR__ . '/../config/production.php';
} else {
    error_reporting(E_ALL);
    defined('YII_DEBUG') or define('YII_DEBUG', true);
    defined('YII_ENV') or define('YII_ENV', 'dev');
    defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 3);
    $config = require __DIR__ . '/../config/development.php';
}

require WEBROOT . '/framework/vendor/autoload.php';
require WEBROOT . '/framework/vendor/yiisoft/yii2/Yii.php';
# 目录映射
Yii::setAlias('@BaseComponents', WEBROOT . '/framework/components/');

(new yii\web\Application($config))->run();
