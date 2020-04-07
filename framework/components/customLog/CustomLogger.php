<?php

namespace BaseComponents\customLog;

use yii\log\Logger;

class CustomLogger extends Logger {
	const LEVEL_APP_ERR = 'app_err';
	
	/**
	 * 格式化写入信息 
	 * @param string $purpose	请求的目的或记录的目的
	 * @param string $api	请求的url
	 * @param array|string $params	请求的参数
	 * @param array|string $return	返回结果
	 * @return string
	 */
	public static function formatMessage($purpose, $api = '',$params=array(), $return = array())
	{
		$str = "@purpose:{$purpose}";
		if (!empty($api)){
			$str .= " @api:{$api}"; 
		}
		if (!empty($params)){
			if (!is_string($params)){
				$params = json_encode($params, JSON_UNESCAPED_UNICODE);
			}
			$str .= " @param:{$params}";
		}
		if (!empty($return)){
			if (!is_string($return)){
				$return = json_encode($return, JSON_UNESCAPED_UNICODE);
			}
			$str .= " @return:{$return}";
		}

		$data = func_get_args();
		unset($data[0], $data[1], $data[2], $data[3]);
		if ( ! empty($data)) {
			$other = json_encode($data, JSON_UNESCAPED_UNICODE);
			$str .= " @other:{$other}";
		}

		return $str;
	}
}

?>