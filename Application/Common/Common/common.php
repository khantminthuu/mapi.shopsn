<?php
/**
 * @name 加密接口中返回的用户ID等信息
 * 
 * @des 加密接口中返回的用户ID等信息
 * @updated 2017-12-15
 */
function paramEncrypt($data, $key = '', $expire = 0) {
    $key  = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
    $data = base64_encode($data);
    $x    = 0;
    $len  = strlen($data);
    $l    = strlen($key);
    $char = '';
    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }
    $str = sprintf('%010d', $expire ? $expire + time():0);
    for ($i = 0; $i < $len; $i++) {
        $str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1)))%256);
    }
    return str_replace(array('+','/','='),array('-','_',''),base64_encode($str));
}
/**
 * @name 解密接口中返回的用户ID等信息
 * 
 * @des 解密接口中返回的用户ID等信息
 * @updated 2017-12-15
 */
function paramDecrypt($data, $key = ''){
    $key    = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
    $data   = str_replace(array('-','_'),array('+','/'),$data);
    $mod4   = strlen($data) % 4;

    if ($mod4) {
        $data .= substr('====', $mod4);
    }
    $data   = base64_decode($data);

    $expire = substr($data,0,10);
    $data   = substr($data,10);
    if($expire > 0 && $expire < time()) {
        return '';
    }
    $x      = 0;
    $len    = strlen($data);
    $l      = strlen($key);
    $char   = $str = '';
    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }
    for ($i = 0; $i < $len; $i++) {
        if (ord(substr($data, $i, 1))<ord(substr($char, $i, 1))) {
            $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
        }else{
            $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
        }
    }
    return base64_decode($str);
}
/**
 * @name 将时间戳格式化为几分钟前
 * 
 * @des 将时间戳格式化为几分钟前
 * @updated 2017-12-22
 */
function mdate($time = NULL) {
	$text = '';
	$time = $time === NULL || $time > time() ? time() : intval($time);
	$t = time() - $time; //时间差 （秒）
	$y = date('Y', $time)-date('Y', time());//是否跨年
	switch($t){
		case $t == 0:
			$text = '刚刚';
			break;
		case $t < 60:
			$text = $t . '秒前'; // 一分钟内
			break;
		case $t < 60 * 60:
			$text = floor($t / 60) . '分钟前'; //一小时内
			break;
		case $t < 60 * 60 * 24:
			$text = floor($t / (60 * 60)) . '小时前'; // 一天内
			break;
		case $t < 60 * 60 * 24 * 30:
			$text = date('m-d H:i', $time); //一个月内
			break;
		case $t < 60 * 60 * 24 * 365&&$y==0:
			$text = date('m-d', $time); //一年内
			break;
		default:
			$text = date('Y-m-d', $time); //一年以前
			break;
	}
	return $text;
}
/**
 * @name 检查手机号格式是否正确
 * 
 * @des 检查手机号格式是否正确
 * @updated 2017-12-15
 */
function validateMobileFormat($mobile){
	if (preg_match('/(^(13\d|15[^4\D]|17[013678]|18\d)\d{8})$/', $mobile)) {
		return true;
	}
	return false;
}
function checkorderstatus($ordid){
    $Ord=M('Orderlist');
    $ordstatus=$Ord->where('ordid='.$ordid)->getField('ordstatus');
    if($ordstatus==1){
        return true;
    }else{
        return false;
    }
}

/***
 * 上传图片
 */
function upload_image($mulu){
    $upload = new \Think\Upload();// 实例化上传类
    $upload->maxSize = C('img_size');// 设置附件上传大小
    $upload->exts = C('img_type');// 设置附件上传类型
    $upload->rootPath = './Uploads/';//上传根目录
    $upload->savePath = "/$mulu/"; // 设置附件上传目录    // 上传文件
    $info = $upload->upload();
    if (!$info) {// 上传错误提示错误信息
        $msg = $upload->getError();
        return array('status' => 0, 'msg' => $msg);
    } else {
//        $remote_file = './public/upload/'.$info['info']['file']['savename'];
//        $ftp_server = "59.110.170.202/Uploads";
//        $infos = postimg($ftp_server,$remote_file);
//        return $infos;
        return array('status' => 1 ,"msg" => $info);
      }
}
 function postimg($posturl,$path){

    $obj = new CurlFile($path);
    $obj->setMimeType("application/octet-stream");//必须指定文件类型，否则会默认为application/octet-stream，二进制流文件</span>
    $post['Filedata'] =  $obj;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, false);
    //启用时会发送一个常规的POST请求，类型为：application/x-www-form-urlencoded，就像表单提交的一样。
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch,CURLOPT_BINARYTRANSFER,true);
    curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
    curl_setopt($ch, CURLOPT_URL, $posturl);//上传类
    $info= curl_exec($ch);
    curl_close($ch);
    return $info;

}
//处理订单函数
//更新订单状态，写入订单支付后返回的数据

/*function orderhandle($parameter){
    $ordid=$parameter['out_trade_no'];
    $data['payment_trade_no']      =$parameter['trade_no'];
    $data['payment_trade_status']  =$parameter['trade_status'];
    $data['payment_notify_id']     =$parameter['notify_id'];
    $data['payment_notify_time']   =$parameter['notify_time'];
    $data['payment_buyer_email']   =$parameter['buyer_email'];
    $data['ordstatus']             =1;
    $Ord=M('Orderlist');
    $Ord->where('ordid='.$ordid)->save($data);
}*/
