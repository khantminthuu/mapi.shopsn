<?php

namespace User\Controller;
use Common\TraitClass\InitControllerTrait;
use Validate\CheckParam;
use Common\Logic\OrderCommentLogic;
use Common\Logic\PcenterLogic;

class PcenterController
{
	
	use InitControllerTrait;
	
	/**
	 * 架构方法
	 *
	 * @param array $args
	 */
	public function __construct(array $args = [])
	{   
		$this->args = $args;
		
		$this->_initUser();//#TODO 这里是需要用户必须登录时要初始化这个 否则初始化$this->init();
  
		$this->logic = new PcenterLogic($args);
		
	}
	
	//我的足迹
	public function myFootprint(){
		$result=$this->logic->getFoot();
		
		$this->objController->promptPjax($result, $this->logic->getErrorMessage());
		
		$this->objController->ajaxReturnData($result['data'],$result['status'],$result['message']);
	}
	//清除我的足迹
	public function deleteFootprint(){
		$result=$this->logic->delFoot();
		
		$this->objController->promptPjax($result, $this->logic->getErrorMessage());
		
		$this->objController->ajaxReturnData($result['data'],$result['status'],$result['message']);
	}
	//我的评论
	public function myComment(){
		$commontLogic= new OrderCommentLogic($this->args);
		
		$result=$commontLogic->getMyCommont();
		
		$this->objController->promptPjax($result, $commontLogic->getErrorMessage());
		
		$this->objController->ajaxReturnData($result['data'],$result['status'],$result['message']);
	}
	
	/*
	 * khantminthu UserInfo
	 * */
	public function userDetail()
    {
        $ret = $this->logic->getUserDetail();
        
        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());
        
        $this->objController->ajaxReturnData($ret);
    }
    
    public function test()
    {
        $test = $this->logic->test();
        
        var_dump($test);
    }
	
}
