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
// |让情绪简单一点，人生就会更丰富一点。
// +----------------------------------------------------------------------
// |让环境简单一点，空间就会更丰富一点。
// +----------------------------------------------------------------------
// |让爱情简单一点，幸福就会更丰富一点。
// +----------------------------------------------------------------------
namespace User\Controller;

use Common\TraitClass\InitControllerTrait;
use Common\TraitClass\DispatcherPayTrait;
use Common\Logic\PayLogic;
use Validate\CheckParam;
use Common\Logic\OpenShopOrderLogic;

/**
 * 开店支付
 * @author Administrator
 */
class OpenShopPayController
{
	use InitControllerTrait;
	use DispatcherPayTrait;
	
	public function __construct(array $args)
	{
		$this->args = $args;
		
		$this->_initUser();
		
		//余额充值
		$args['platform'] = 1;
		
		$args['special_status'] = 3;
		
		$this->logic = new PayLogic($args);
	}
	
	/**
	 * 余额充值处理
	 */
	public function openShop()
	{
		$this->objController->promptPjax(IS_POST , '不允许请求');
		
		$checkObj = new CheckParam($this->logic->getValidateByOpenShop(), $this->args);
		
		$this->objController->promptPjax($checkObj->checkParam(), $checkObj->getErrorMessage());
		
		//获取支付信息
		$payConfig = $this->logic->getResult();
		
		$this->objController->promptPjax($payConfig, '无法获取支付配置');
		
		$this->objController->promptPjax($_SESSION['store_data_by_person'], '参数异常');
		
		$openShopLogic = new OpenShopOrderLogic($_SESSION['store_data_by_person']);
		
		$insertId = $openShopLogic->addData();
		
		$this->objController->promptPjax($insertId, $openShopLogic->getErrorMessage());
		
		$args = [];
		
		$args['order_id'] = $insertId;
		
		$args['order_sn'] = $openShopLogic->getOrderSn();
		$args['store_type'] = $_SESSION['store_data_by_person']['type'];
		$args['money'] = $_SESSION['money'];
		$result = $this->dispatcherPay($payConfig, $args);
		$this->objController->ajaxReturn($result);
	}
}