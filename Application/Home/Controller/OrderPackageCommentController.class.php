<?php
namespace Home\Controller;

use Common\TraitClass\InitControllerTrait;
use Validate\CheckParam;
use Common\Logic\OrderPackageCommentLogic;


class OrderPackageCommentController
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
        $this->logic = new OrderPackageCommentLogic($args);
    }
    /**
     * 评论商品
     * @author 王波
     */
    public function commentOrder(){ 
        //检测传值                  //检测方法
        $checkObj = new CheckParam($this->logic->getValidatesByLogin(), $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());
        $ret = $this->logic->getCommentsOrder();

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
}