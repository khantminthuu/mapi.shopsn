<?php

namespace Home\Controller;
use Common\Logic\ArticleLogic;
use Common\TraitClass\InitControllerTrait;

/**
 * @name 文章控制器
 * 
 * @des 文章控制器
 * @updated 2018-01-05 12:08
 */
class ArticleController
{
	use InitControllerTrait;
	/**
	 * 架构方法
	 * @param array $args
	 */
	public function __construct(array $args = [])
	{
		$this->init();
		$this->args = $args;
		$this->logic = new ArticleLogic($args);
	}
	/**
	
	
    /**
     * @name 文章列表
     * 
     * @des 文章列表
     * @updated 2018-01-05 12:08
     */
    public function lists()
    {
        if (IS_GET) {
            $ret = $this->logic->lists();//逻辑处理

            $this->objController->promptPjax($ret, $this->logic->getErrorMessage());//获取失败提示并返回

            $this->objController->ajaxReturnData($ret);//返回数据
        }
    }
    /**
     * @name 文章详情
     * 
     * @des 文章详情
     * @updated 2018-01-05 12:08
     */
    public function info()
    {
        if (IS_GET) {
            $ret = $this->logic->info();//逻辑处理

            $this->objController->promptPjax($ret, $this->logic->getErrorMessage());//获取失败提示并返回

            $this->objController->ajaxReturnData($ret);//返回数据
        }
    }
}