<?php
namespace Home\Controller;

use Common\TraitClass\InitControllerTrait;
use Validate\CheckParam;
use Common\Logic\BrandLogic;


class BrandController
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

        $this->logic = new BrandLogic($args);

     

    }
    /**
     * 数据
     *
     */
    public function brandList()
    {
        //检测传值                  //检测方法
//        $checkObj = new CheckParam($this->logic->getValidateByLogin(), $this->args);
//
//        $status = $checkObj->checkParam();
//
//        $this->objController->promptPjax($status, $checkObj->getErrorMessage());

        $ret = $this->logic->getBrandList();

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret);
    }

    /**
     * 品牌店详情
     *
     */
    public  function brandDetail()
    {
        print_r($this->data);
        //检测传值                  //检测方法
        $checkObj = new CheckParam($this->logic->getValidateByLogin(), $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());

        $ret = $this->logic->get_brand_info();

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret);
    }

}