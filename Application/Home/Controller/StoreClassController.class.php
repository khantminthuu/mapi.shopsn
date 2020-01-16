<?php
namespace Home\Controller;

use Common\TraitClass\InitControllerTrait;
use Validate\CheckParam;
use Common\Logic\StoreClassLogic;


class StoreClassController
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

        $this->logic = new StoreClassLogic($args);

     

    }
    /**
     * 数据
     *
     */
    public function storeClasses()
    {
        //检测传值                  //检测方法

        $ret = $this->logic->get_store_class();

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']); 
    }

}