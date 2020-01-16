<?php
// +----------------------------------------------------------------------
// | OnlineRetailers [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2003-2023 www.shopsn.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed 亿速网络（http://www.shopsn.net）
// +----------------------------------------------------------------------
// | Author: 王强 <opjklu@126.com>
// +----------------------------------------------------------------------

namespace Common\Tool\Extend;


use Common\Tool\Tool;

/**
 * curl 操作
 * @author Administrator
 * @version 1.0.1
 */
class CURL
{
    /**
     * 文件数组
     * @var array
     */
    private $data = [];
    
    /**
     * url
     * @var string
     */
    private $url = '';
    
    /**
     * @param array $data
     * @param string $url
     */
    public function __construct(array $data, $url)
    {
        $this->data = $data;
        
        $this->url  = $url;
        
    }
    
    /**
     * 请求接口
     */
    public function requestQuery()
    {
    	$data = $this->data;
    	
    	$url = $this->url;
    	
    	
    	$returnData = $this->curlConfig();
    	
    	return $returnData;
    }
    
    /**
     * @param array  $data 文件信息
     * @param string $url  上传的URL
     */
    public function uploadFile()
    {
        $data = $this->data;
        
        $url = $this->url;
        
        if (empty($data) || empty($url))
        {
            throw new \Exception('文件错误');
        }
        //php 5.5以上的用法
        if (class_exists('\CURLFile')) {
            $data = [
                'data' => new \CURLFile(realpath($data['tmp_name']),$data['type'],$data['name'])
            ];
        } else {
            $data =[ 
                'data'  =>'@'.realpath($data['tmp_name']).";type=".$data['type'].";dataname=".$data['name']
            ];
        }
        
        $this->data = $data;
        
        $returnData = $this->curlConfig();
        return $returnData;
    }
    
    /**
     * 生成缩略图
     */
    public function sendImageToCreateThumb()
    {
        return $this->curlConfig();
    }
    
    
    private function curlConfig()
    {
        $url = $this->url;
        
        $data = $this->data;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        $returnData = curl_exec($ch);
        // showData(curl_error($ch));
        curl_close($ch);
        return $returnData;
    }
    
    /**
     * 服务器获取信息
     * @return []
     */
    public function curlByGet()
    {
    	$ch     = curl_init();
    	
    	$url .= $this->url.'?'.http_build_query($this->data);
    	curl_setopt($ch, CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    	$output = curl_exec($ch);
    	
    	file_put_contents('./Log/wx/qq_refresh.txt', print_r($url, true)."\r\n", FILE_APPEND);
    	
    	file_put_contents('./Log/wx/qq_refresh.txt', print_r(curl_error($ch), true)."\r\n", FILE_APPEND);
    	
    	curl_close($ch);
    	
    	return $output;
    }
    
    
    /**
     * 删除文件 
     */
    public function deleteFile()
    {
        return $this->curlConfig();
    }
    
    /**
     * 异步执行
     */
    public function asynchronousExecution()
    {
        $urlinfo = parse_url($this->url);
        
        $host = $urlinfo['host'];
        $path = $urlinfo['path'];
        $query = http_build_query($this->data);
        
        $port = 80;
        $errno = 0;
        $errstr = '';
        $timeout = 10;
        
        $fp = fsockopen($host, $port, $errno, $errstr, $timeout);
        
        $out = "POST ".$path." HTTP/1.1\r\n";
        $out .= "host:".$host."\r\n";
        $out .= "content-length:".strlen($query)."\r\n";
        $out .= "content-type:application/x-www-form-urlencoded\r\n";
        $out .= "connection:close\r\n\r\n";
        $out .= $query;
        
        fputs($fp, $out);
        fclose($fp); 
    }
}