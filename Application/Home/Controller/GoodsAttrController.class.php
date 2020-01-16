<?php
namespace Home\Controller;

use Common\TraitClass\InitControllerTrait;
use Validate\CheckParam;
use Common\Logic\GoodsAttrLogic;

class GoodsAttrController
{
    use InitControllerTrait;

    /**
     * 架构方法
     * @param array
     * $args   传入的参数数组
     */
    public function __construct(array $args = [])
    {
        $this->init();

        $this->args = $args;

        $this->goodsAttrLogic = new GoodsAttrLogic($args);

     

    }
    /**
     * 得到商品的规格参数
     *
     */
    public function getGoodsAttr()
    {
        //检测传值                  //检测方法
        $checkObj = new CheckParam($this->goodsAttrLogic->getValidateByLogin(), $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());

        $ret = $this->goodsAttrLogic->getGoodsAttrs();

        $this->objController->promptPjax($ret, $this->goodsAttrLogic->getErrorMessage());

        $this->objController->ajaxReturnData($ret);
    }

}