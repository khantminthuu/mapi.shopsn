<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/24 0024
 * Time: 14:00
 */

namespace Home\Controller;
use Common\TraitClass\InitControllerTrait;
use Common\Logic\ServiceLogic;
use Validate\CheckParam;

class ServiceController
{
    use InitControllerTrait;
    /**
     * 架构方法
     *
     * @param array $args
     */
    public function __construct(array $args = [])
    {
       
        $this->_initUser();

        $this->args = $args;

        $this->logic = new ServiceLogic($args);

    }
    //获取客服列表s
    public function serviceList(){
        $res = $this->logic->getServiceList();
        $this->objController->promptPjax($res, $this->logic->getErrorMessage());
        $this->objController->ajaxReturnData($res);
    }
}