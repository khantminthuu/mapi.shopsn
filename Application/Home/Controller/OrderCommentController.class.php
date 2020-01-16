<?php
namespace Home\Controller;

use Common\TraitClass\InitControllerTrait;
use Validate\CheckParam;
use Common\Logic\OrderCommentLogic;


class OrderCommentController
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
        
        $this->logic = new OrderCommentLogic($args);
    }
    /**
     * 验证参数和得到商品所有的评论的数量
     *
     */
    public function getAllCommentsCount()
    {
        //检测传值                  //检测方法
        $checkObj = new CheckParam($this->logic->getValidateByLogin(), $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());

        $ret = $this->logic->getGoodsAllComments();

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret);
    }

    /**
     * 验证参数和得到商品所有的评论的内容
     *
     */
    public function getAllCommentContent(){
        $checkObj = new CheckParam($this->logic->getValidatesByLogin(), $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());

        $ret = $this->logic->getCommentsList(); 

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret);
    }
    /**
     * 评论商品
     * @author 王波
     */
    public function commentOrder(){ 
    
        $this->checkIsLogin();
        
        $ret = $this->logic->getCommentsOrder();

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
}