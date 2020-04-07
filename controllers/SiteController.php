<?php

/**
 * Update: 2017-11-27
 * File  : PayAccountController.php
 */

namespace app\controllers;

use Yii;
use BaseComponents\base\BaseController;

class SiteController extends BaseController {

    public function apiIndex() {
        echo "success";exit;
        /*
        $user_id = UserLogic::checkLogin();
        if ($user_id) {
            header("Location: /site/app.html");

        } else {
            header("Location: /site/login.html");

        }
        exit;
        */
    }

    public function apiError(){
        
        throw new Exception(-404,'找不到接口地址');
    }
    
    public function apiTest(){
        Yii::warning("测试输出测试输出");
        return "OK";
    }
    
    public function apiUpload() {
        $file = $_FILES['up_file']; 
        if (!empty($file) && $file['error']==0 && !empty($file['tmp_name'])) {
            $ext = substr(strrchr($file['name'], "."), 1);
            $save_file = "/data/" . date('YmdHis') . rand(100,999) . "." . $ext;
            if(move_uploaded_file($file['tmp_name'], WEBROOT . $save_file)) {
                //$path = $save_file;
            } else {
                throw new Exception(-2,'请选择文件上传');
            }
            
            return array('key' => $file['name'], 'path' => $save_file , 'size' => $file['size']);
        }
        else {
            
            throw new Exception(-1,'请选择文件上传');
        }
        
       
    }
 
}
