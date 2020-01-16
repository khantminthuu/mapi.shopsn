<?php
namespace User\Controller;

use Common\TraitClass\InitControllerTrait;
use Validate\CheckParam;
use Common\Logic\CollectionLogic;

class CollectionController
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
		
        $this->_initUser();
        
        $this->logic = new CollectionLogic($args);

     

    }
    /**
     * 我的收藏  收藏的宝贝
     *
     */
    public function myCollection()
    {
        //检测传值                  //检测方法
        $ret = $this->logic->get_my_collection();

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
    /**
     * 取消收藏  宝贝
     *
     */
    public function cancelCollectionGood(){
        //检测传值                  //检测方法
        $checkObj = new CheckParam($this->logic->getValidateByCancel(), $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());

        $ret = $this->logic->cancel_user_collection();

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret);
    }
    
  /**
     * 我收藏的店铺
     *
     */
    public function collectShops()
    {
        $ret = $this->logic->my_collection_shops();

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);

    }
    
    /**
     * 数据
     *
     */
    public function addCollection()
    {
    	//检测传值                  //检测方法
    	$checkObj = new CheckParam($this->logic->getValidateByLogin(), $this->args);
    	
    	$status = $checkObj->checkParam();
    	
    	$this->objController->promptPjax($status, $checkObj->getErrorMessage());
    	
    	$ret = $this->logic->collectGoods();
    	
    	$this->objController->promptPjax($ret, $this->logic->getErrorMessage());
    	
    	$this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
}