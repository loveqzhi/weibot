<?php
namespace BaseComponents\base;

/**
 * 数据处理引擎基类
 *
 * @author coso
 * @since 2015-09-10
 */
abstract class DataEngine
{

    public $url = '';
    // 请求地址
    public $queryString = '';

    /**
     * 设置请求地址
     *
     * @param string $url            
     */
    public function setUrl($url)
    {}

    /**
     * 设置参数
     *
     * @param array $params            
     * @param array $exraParam            
     */
    public function setQueryString($param, $exraParam = [])
    {}

    /**
     * 获取远程数据
     *
     * @param array $params            
     * @param array $url            
     * @param array $exraParam            
     */
    public function execute($param, $url, $exraParam = [])
    {}
}
