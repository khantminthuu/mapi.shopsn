<?php
// +----------------------------------------------------------------------
// | OnlineRetailers [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2003-2023 www.shopsn.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed 亿速网络（http://www.shopsn.net）
// +----------------------------------------------------------------------
// | Author: 王强 <13052079525>
// +----------------------------------------------------------------------
// |简单与丰富！
// +----------------------------------------------------------------------
// |让外表简单一点，内涵就会更丰富一点。
// +----------------------------------------------------------------------
// |让需求简单一点，心灵就会更丰富一点。
// +----------------------------------------------------------------------
// |让言语简单一点，沟通就会更丰富一点。
// +----------------------------------------------------------------------
// |让私心简单一点，友情就会更丰富一点。
// +----------------------------------------------------------------------
// |让情绪简单一点，人生就会更丰富一点。
// +----------------------------------------------------------------------
// |让环境简单一点，空间就会更丰富一点。
// +----------------------------------------------------------------------
// |让爱情简单一点，幸福就会更丰富一点。
// +----------------------------------------------------------------------
declare(strict_types=1);
namespace Common\TraitClass;

use Common\Controller\CommonController;
use Think\SessionGet;
/**
 * 控制器初始化
 * @author Administrator
 * @version 1.0.0
 */
trait InitControllerTrait 
{
    /**
     * @param array 属性
     */
    private $args;
    
    /**
     * 逻辑处理层对象
     */
    private $logic;
    
    private $logicClassName;
    
    /**
     * 控制器对象
     * @var CommonController
     */
    private $objController;
    
    /**
     * @return the $args
     */
    public function getArgs()
    {
        return $this->args;
    }

    /**
     * @return the $logic
     */
    public function getLogic()
    {
        return $this->logic;
    }

    /**
     * @return the $objController
     */
    public function getObjController()
    {
        return $this->objController;
    }
    
    /**
     * 初始化
     */
    protected function init() :void
    {
        //基类控制器
        $this->objController = new CommonController();
		
		$this->headerOriginInit();
		
    }
    
    /**
     * header初始化
     */
    protected function headerOriginInit() :void
    {
    	$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
    	
    	$allowOrigin = C('origin');
    	
    	if(in_array($origin, $allowOrigin)){
    		header("Access-Control-Allow-Origin:" . $origin);//跨域解决
    		header('Access-Control-Allow-Methods:POST,GET');
    		header('Access-Control-Allow-Headers:Origin, X-Requested-With, Content-Type, Accept');
    		header('Access-Control-Allow-Credentials: true');
    	}
    	
    }
    /**
     * session 初始化（支付回调初始化）
     */
    protected function sessionInit() :void
    {
    	$this->sessionId = isset($this->args['token']) ? $this->args['token'] : null;
    	
    	if ($this->sessionId) {
    		session_write_close();
    		session_id($this->sessionId);
    		session_start();
    	}
    }
    
	/**
	 * @name 用户登录初始化
	 * 
	 * @des 用户登录初始化
	 * @updated 2017-12-23 10:58
	 */
    protected function _initUser() :void
    {
    	$this->init();//#TODO 控制器初始化
    	
		$this->checkIsLogin();
	}
	
	protected function checkIsLogin() :void
	{
		$userId = SessionGet::getInstance('user_id')->get();

		if(!$userId){
			$this->objController->ajaxReturnData([], 10001, '用户未登录!');//返回数据
		}
	}
	
	/**
	 * 析构方法
	 */
//	public function __destruct()
//	{
//		unset($this->args, $this->objController, $this->logic);
//	}
}