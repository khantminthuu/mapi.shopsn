<?php
namespace Home\Controller;

use Common\TraitClass\InitControllerTrait;
use Validate\CheckParam;
use Common\Logic\GoodsConsultationLogic;
use Common\Logic\GoodsAdvisoryReplyLogic;

/**
 * 商品咨询
 * @author Administrator
 *
 */
class GoodsConsultationController
{
    use InitControllerTrait;

    /**
     * 架构方法
     * @param array
     * $args   传入的参数数组
     */
    public function __construct(array $args = [])
    {
    	$this->args = $args;
		
    	$this->init();
    	
        $this->logic = new GoodsConsultationLogic($args);
    }
    
    /**
     * 商品咨询数据
     */
    public function consultationData()
    {
        //检测传值                  //检测方法
    	$checkObj = new CheckParam($this->logic->getMessageValidateByGoods(), $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());

        $ret = $this->logic->getGoodsConsultationByGoods();
		
        $this->objController->promptPjax($ret['records'], $this->logic->getErrorMessage());
		
        $goodsAdvisory = new GoodsAdvisoryReplyLogic($ret['records'], $this->logic->getPrimaryKey());
        
        $advisoryData = $goodsAdvisory->getGoodsAdvisoryReplyCache();
        
        $ret['records'] = $advisoryData;
        
        $this->objController->ajaxReturnData($ret);
    }

    /**
     * 用户提交问题
     */
    public function userCommitProblem(){
    	
    	$this->checkIsLogin();
        //检测传值                  //检测方法
    	$checkObj = new CheckParam($this->logic->getMessageValidate(), $this->args);

        $status = $checkObj->checkParam();
 
        $this->objController->promptPjax($status, $checkObj->getErrorMessage());

        $ret = $this->logic->addData();

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret);
    }
    
    //用户在线留言
    public function onlineMessage(){
    	
    	$this->checkIsLogin();
    	
        //检测传值                  //检测方法
    	$checkObj = new CheckParam($this->logic->getMessageValidate(), $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());
		
        $ret = $this->logic->onlineMessageAdd();
		
        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
}