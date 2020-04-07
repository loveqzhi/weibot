<?php

namespace BaseComponents\customLog;

use Yii;
use yii\log\FileTarget;
use yii\log\Logger;
use yii\web\Request;

class CustomFileLog extends FileTarget {

    public function formatMessage($message)
    {
        list($text, $level, $category, $timestamp) = $message;	
		$datetime = date('Y/m/d H:i:s',$timestamp);
		$level = Logger::getLevelName($level);
        $request = Yii::$app->getRequest();
        $ip = $request instanceof Request ? $request->getUserIP() : '-';
        $traces = [];
        $trace_count = 0;
        if (isset($message[4])) {
            foreach($message[4] as $trace) {
                $traces[] = "in {$trace['file']}:{$trace['line']}";
				if(++$trace_count >= YII_TRACE_LEVEL)
					break;
            }
        }
        if (isset($_SERVER['REQUEST_URI'])){
            $traces[] = $_SERVER['REQUEST_URI'];
        }
		$traces = json_encode($traces);

		return "[{$datetime}] [{$level}] [{$category}] [{$text}] [{$ip}] [{$traces}]\n";
	}

}

?>