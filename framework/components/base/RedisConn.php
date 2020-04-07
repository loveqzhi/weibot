<?php

namespace BaseComponents\base;

use Yii;
use Redis;
use BaseComponents\customLog\CustomLogger;
use yii\base\Component;

class RedisConn extends Component {
    /**
     * The redis client
     * @var Redis
     */
    protected static $_client;

    /**
     * The redis server name
     * @var string
     */
    public $hostname = "localhost";

    /**
     * The redis server port
     * @var integer
     */
    public $port=6379;

    /**
     * The redis server auth
     * @var string
     */
    public $auth;

    /**
     * The database to use, defaults to 1
     * @var integer
     */
    public $database=1;

    public function init()
    {
        $this->getClient();
    }

    /**
     * Sets the redis client to use with this connection
     * @param Redis $client the redis client instance
     */
    public function setClient(Redis $client)
    {
        self::$_client = $client;
    }

    /**
     * Gets the redis client
     * @return Redis the redis client
     */
    public function getClient()
    {
        if (self::$_client === null) {
            self::$_client = new Redis;
            self::$_client->connect($this->hostname, $this->port);
            if($this->auth !== null){
                self::$_client->auth($this->auth);
            }
        }
        return self::$_client;
    }

    public function __call($name, $params)
    {   
        if( $name == "keys" ){
            throw new \Exception("禁用redis keys 会引发性能问题", 500);
        }
        for ($i=0; $i < 3; $i++) {
            try {
                $data = call_user_func_array([$this->getClient(), $name], $params);
            } catch (\Exception $e) {
                Yii::error(CustomLogger::formatMessage('redis error: ' . $e->getMessage()), 'redis');
                continue;
            }
            return $data;
        }
        throw new \Exception("Can not connect Redis", 500);
    }

}