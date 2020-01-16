<?php
namespace Home\Controller;

use Common\TraitClass\InitControllerTrait;
use Validate\CheckParam;
use Common\Logic\ExpressLogic;


class ExpressController
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

        $this->logic = new ExpressLogic($args);

     

    }
    /**
     * 数据
     *
     */
    public function lookGoodsExpress()
    {
        //检测传值                  //检测方法
        $checkObj = new CheckParam($this->logic->getValidateByLogin(), $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());

        $ret = $this->logic->getHomeInfo();

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret);
    }

}