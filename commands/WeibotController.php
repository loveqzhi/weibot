<?php
namespace app\commands;

use Yii;
use yii\console\Controller;
use BaseComponents\base\Exception;
use BaseComponents\base\CoreHelper;
use Hanson\Weibot\Weibot;
use yii\db\Query;



class WeibotController extends Controller
{


    public $weibot_users = [
        1   => [
            'username'  => 'loveqzhi@hotmail.com',
            'password'  => 'chinazhi2018',
        ],
    ];
    
    //登录
    public function setting($user) {
        $settings = [
            'cookie_path'   => WEBROOT . '/cookies',
            'debug' => []
        ];
        
        $settings = array_merge($settings, $user);
        
        return  new Weibot($settings);
    }
    
    /**
     * 搜索数据
     *
     */
    public function actionSearch() {
        //设置超时
        set_time_limit(0);
        ini_set('memory_limit', '64M');

        $weibo = $this->setting($this->weibot_users[1]);
        $weibo->login();
        $search = $weibo->search;
        $html = $search->getData([
            'keyword' => '美国总统',
            'page' => 1, // 页数
            'start_at' => '2020-04-07-10', # yyyy-mm-dd-h 时间筛选
            'end_at' => '2020-04-07-12',
        ]);
        print_r($html);
        $html = $search->getData([
            'keyword' => '美国总统',
            'page' => 2, // 页数
            'start_at' => '2020-04-07-10', # yyyy-mm-dd-h 时间筛选
            'end_at' => '2020-04-07-12',
        ]);
        print_r($html);
        //print_r($weibo);
        echo "success\n";
    }

}
