<?php
namespace Common\TraitClass;


use Think\SessionGet;

/**
 * 是否登录
 * @author Administrator
 */
trait IsLoginTrait 
{
    /**
     * 是否登录
     */
    private function isLogin()
    {
    	$userId = SessionGet::getInstance('user_id')->get();
    	
    	if (empty($userId)) {
        	$this->objController->ajaxReturnData([], 10001, '用户未登录!');//返回数据
        }
    }
    

}