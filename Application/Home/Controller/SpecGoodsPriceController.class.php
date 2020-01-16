<?php

namespace Home\Controller;
use Common\Logic\SpecGoodsPriceLogic;
use Common\TraitClass\InitControllerTrait;
use Validate\CheckParam;
use Common\Tool\Tool;

/**
 * 商品规格控制器
 */
class SpecGoodsPriceController 
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

        $this->logic = new SpecGoodsPriceLogic($args);

    }

   
}