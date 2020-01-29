<?php
namespace Home\Controller;

use Common\TraitClass\InitControllerTrait;
use Validate\CheckParam;
use Common\Logic\StoreGradeLogic;


class StoreGradeController
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

        $this->logic = new StoreGradeLogic($args);
    }
    /**
     * 数据
     *
     */
    public function getShopLevel()
    {
        $ret = $this->logic->shop_level_list();

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret);
    }

}