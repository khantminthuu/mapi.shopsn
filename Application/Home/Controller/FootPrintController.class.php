<?php
namespace Home\Controller;

use Common\TraitClass\InitControllerTrait;
use Common\Logic\FootPrintLogic;


class FootPrintController
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
        
        $this->logic = new FootPrintLogic($args);

    }
    /**
     * 猜你喜欢
     *
     */
    public function guessLove()
    {
        $ret = $this->logic->guessLove();

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret);
    }
	
    /**
     * 添加足迹
     */
    public function addPrint()
    {
    	$this->_initUser();
    	
    }
}