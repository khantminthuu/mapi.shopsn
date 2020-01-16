<?php
declare(strict_types=1);
namespace Common\Controller;

use Think\Controller;
use Common\TraitClass\NoticeTrait;

class CommonController {
	use NoticeTrait;
	
	/**
	 * Ajax方式返回数据到客户端
	 * 
	 * @access protected
	 * @param mixed $data
	 */
	public function ajaxReturn($data) :void
	{
		// 返回JSON数据格式到客户端 包含状态信息
		header ( 'Content-Type:application/json; charset=utf-8' );
		exit ( json_encode ( $data, JSON_UNESCAPED_UNICODE ) );
	}
}