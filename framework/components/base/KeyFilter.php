<?php
namespace BaseComponents\base;

use yii\base\ActionFilter;
use BaseComponents\base\Exception;

class KeyFilter extends ActionFilter
{

    public $apikey = '';

    public $tokenParam = 'apikey';

    public $extraParam = [];

    public function beforeAction($action)
    {
        if (! $this->checkToken($action)) {
            throw new Exception(400, '签名错误');
        }
        return true;
    }

    /**
     * 检验token
     */
    private function checkToken($action)
    {
        $params = $_REQUEST;
        if (! isset($params[$this->tokenParam])) {
            throw new Exception(ErrorCode::ERR_CHECK_SIGN);
        }
        $token = $params[$this->tokenParam];
        return $token == $this->apikey;
    }
}