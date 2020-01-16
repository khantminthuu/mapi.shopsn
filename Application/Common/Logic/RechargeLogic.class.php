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
// |简单与丰富！让外表简单一点，内涵就会更丰富一点。
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
namespace Common\Logic;

use Common\Model\RechargeModel;
use Common\Tool\Tool;
use Think\SessionGet;

class RechargeLogic extends AbstractGetDataLogic
{
	
	/**
	 * 订单号
	 * @var string
	 */
	private $orderSn = '';
	
	/**
	 * 
	 * @return string
	 */
	public function getOrderSn()
	{
		return $this->orderSn;
	}
	
    public function __construct( $data )
    {
        $this->data = $data;
        
        $this->modelObj = RechargeModel::getInitnation();
    }
    /**
     * 
     */
    public function getResult() {}
    
    /**
     *
     * {@inheritdoc}
     *
     * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
     */
    public function getModelClassName() :string {
    	return RechargeModel::class;
    }
    
    /**
     * 获取当前订单充值的金额
     */
    public function getCurretRecharge()
    {
        $orderId = $this->data;
        
        if (empty($orderId) || !is_numeric($orderId)) {
            return false;
        }
        
        return $this->model->field(RechargeModel::$userId_d.','.RechargeModel::$account_d)->where(RechargeModel::$id_d.'=%d', $orderId)->find();
        
    }
    
    public function getRechargeInfo()
    {
        $orderId = $this->data;
        
        if (!is_numeric($orderId)) {
            return [];
        }
        
        $data = $this->model->field(RechargeModel::$payTime_d.', '.RechargeModel::$ctime_d, true)->where(RechargeModel::$id_d.'=%d', $orderId)->find();
        
        return $data;
    }
    
    /**
     * 添加时处理参数
     * @return array
     */
    protected function getParseResultByAdd() :array
    {
    	$orderSn = Tool::connect('Token')->toGUID();
    	
    	$data = $this->data;
    	
    	$data[RechargeModel::$orderSn_d] = $orderSn;
    	
    	$data[RechargeModel::$payType_d] = 1;
    	
    	$data[RechargeModel::$account_d] = $this->data['money'];
    	
    	$data[RechargeModel::$payStatus_d] = 0;
    	
    	$data[RechargeModel::$ctime_d] = time();
    	
    	$data[RechargeModel::$userId_d] = SessionGet::getInstance('user_id')->get();
    	
    	$this->orderSn = $orderSn;
    	
    	return $data;
    }
    
    /**
     * 修改状态
     * @return boolean
     */
    public function saveStatus()
    {
    	$this->modelObj->startTrans();
    	
    	$status = $this->saveData();
    	
    	if (empty($status)) {
    		
    		$this->modelObj->rollback();
    		
    		$this->errorMessage = '不允许重复请求，或者异常';
    		
    		return false;
    	}
    	
    	return true;
    }
    
    /**
     * 保存时处理参数
     */
    protected function getParseResultBySave() :array
    {
    	
    	$data = $this->data;
    	
    	$save = [
    		RechargeModel::$id_d => $data['id'],
    		RechargeModel::$payTime_d => time(),
    		RechargeModel::$payStatus_d => 1,
    		RechargeModel::$payType_d => $data['pay_id'],
    		RechargeModel::$payType_d => 1
    	];
    	
    	
    	return $save;
    }
    
}