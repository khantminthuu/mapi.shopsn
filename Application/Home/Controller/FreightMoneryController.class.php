<?php
declare(strict_types=1);
namespace Home\Controller;

use Common\TraitClass\InitControllerTrait;
use Common\Logic\FreightsLogic;
use Common\Logic\FreightConditionLogic;
use Common\Logic\FreightAreaLogic;
use Common\Logic\FreightModeLogic;
use Common\Logic\FreightSendLogic;
use Common\Strategy\Context;
use Common\SessionParse\SessionManager;

class FreightMoneryController
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
		
		$this->_initUser();
		
		$this->logic = new FreightsLogic($args);
	}
	
	/**
	 * 计算运费
	 */
	public function getFreightMoneyByEnoughToBuyImmediately()
	{
		$this->objController->promptPjax($this->logic->getValidateSource(), $this->logic->getErrorMessage());
		
		//模板设置
		$templateConf = $this->logic->getFreightTemplate();
		
		$this->objController->promptPjax($templateConf, $this->logic->getErrorMessage(), $this->logic->getStoreId());
		
		//运费模板有没有运送到该地区的
		$freightMode = new FreightModeLogic($templateConf);
		
		$fModeConf = $freightMode->getFreightMoney();
		
		$this->objController->promptPjax($fModeConf, $freightMode->getErrorMessage());
		
		//查看 该模板包不包含该配送地区
		$sendAreaLogic = new FreightSendLogic(['f_mode' =>$fModeConf, 'area_conf' => $this->args]);
		
		$this->objController->promptPjax($sendAreaLogic->userAddressIndexOfSendArea(), $sendAreaLogic->getErrorMessage());
		
		//具体的计算方式
		$modeDetail = $sendAreaLogic->getModeDetail();
		
		$this->objController->promptPjax($modeDetail, '没有模板提供计算运费');
		
		
		if ($this->logic->getIsAllPost()) {//包邮
			$this->objController->ajaxReturnData(0);
		}
		
		$condition = [];
		
		$freightCondition = new FreightConditionLogic($templateConf, $this->logic->getPrimaryKey());
		
		$condition = $freightCondition->getFreightOneData();
		
		$freightArea = new FreightAreaLogic(['con' => $condition,'param' =>$this->args, 'freight_mode' => $modeDetail]);
		
		//筛选指定条件包邮商家
		$freightMode = $freightArea->sendAddressIsInFreeShipping();
		
		$context = Context::getInstance($freightMode, SessionManager::GETFREIGHT_MODE_DATA());
		
		$monery = $context->getPriceByFreight();
		
		SessionManager::SET_FREIGHT_MONRY($monery);
		
		$this->objController->ajaxReturnData($context->getTotalMoney());
	}
	
}