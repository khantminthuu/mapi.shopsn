<?php
namespace User\Controller;

use Common\TraitClass\InitControllerTrait;
use Common\TraitClass\IsLoginTrait;
use Common\Logic\OrderLogic;
use Common\TraitClass\GETConfigTrait;
use Validate\CheckParam;
use Extend\Express\KdApiSearch;
use Common\Logic\OrderGoodsLogic;

/**
 * 物流查询
 * @author Administrator
 *
 */
class LogisticsQueryController
{
	use InitControllerTrait;
	use IsLoginTrait;
	use GETConfigTrait;
	/**
	 * 架构方法
	 * @param array
	 * $args   传入的参数数组
	 */
	public function __construct(array $args = [])
	{
		$this->args = $args;
		
		$this->_initUser();
		
		$this->logic = new OrderLogic($args);
	}
	
	/**
	 * 查看物流
	 * @author 王强
	 */
	public function lookGoodsExpress()
	{
		
	    $checkObj = new CheckParam($this->logic->getValidateByLogist(), $this->args);
		
		$status = $checkObj->checkParam();
		
		$this->objController->promptPjax($status, $checkObj->getErrorMessage());
		
		$ret = $this->logic->get_good_express();
		
		$this->objController->promptPjax($ret, $this->logic->getErrorMessage());
		
		$this->key = 'Logistics_query';
		
		$logicConf = $this->getGroupConfig();
		
		$logist = new KdApiSearch($logicConf['business_id'], $logicConf['app_key'], $ret);
		
		$result = $logist->getOrderTracesByJson();
		
		//是否已签收
		$logist = json_decode($result);
		
		if ($this->args['order_status'] == 3 &&$logist->State == 3) {
		    
		    $orderLogic = new OrderLogic($this->args);
		    
		    $status = $orderLogic->getOverTime();
		    
		    $this->objController->promptPjax($status, $orderLogic->getErrorMessage());
		    
		    $orderGoodsLogic = new OrderGoodsLogic($this->args);
		    
		    $status = $orderGoodsLogic->setOrderOverTime();
		    
		    $this->objController->promptPjax($status, $orderGoodsLogic->getErrorMessage());
		}
		
		$this->objController->ajaxReturnData($result);
	}
}