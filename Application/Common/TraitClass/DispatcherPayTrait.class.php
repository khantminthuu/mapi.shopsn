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
namespace Common\TraitClass;


use Validate\CheckParam;
use Common\Logic\GoodsLogic;
use Think\SessionGet;

/**
 * 
 * @author Administrator
 *
 */
trait DispatcherPayTrait
{
	private $errorMessage = '';
	
	public function setErrorMessage($errorMessage) 
	{
		$this->errorMessage = $errorMessage;
	}
	
    /**
     * 分发支付
     * @param array $data
     */
    protected function dispatcherPay (array $data, $orderData)
    {

        try {
            $data['pay_name'] = str_replace('/', '\\', $data['pay_name']);
            $obj = new \ReflectionClass($data['pay_name']);
            $instance = $obj->newInstanceArgs([$data, $orderData]);

            $rusult = $obj->getMethod('pay')->invoke($instance);//发起支付
			
            return $rusult;
            
        }catch (\Exception $e) {
          	$this->errorMessage = $e->getMessage();
          	return [];
        }
    }
    
    /**
     * 发起支付处理S h o p s N
     */
    private function initiate()
    {
    	
    	$checkObj = new CheckParam($this->logic->getValidateByPay(), $this->args);
    	
    	if ($checkObj->checkParam() === false) {
    		$this->errorMessage = $checkObj->getErrorMessage();
    		
    		return [];
    	}
    	
    	$goodsData = SessionGet::getInstance('goods_id_by_user')->get();
    	
    	//判断库存是否足够
    	$goodsLogic = new GoodsLogic($goodsData);
    	
    	if ($goodsLogic->checkStock() === false) {
    		
    		$this->errorMessage = $goodsLogic->getErrorMessage();
    		
    		return [];
    	}
    	
    	$payConfig = $this->logic->getResult();
    	
    	if (empty($payConfig)) {
    		
    		$this->errorMessage = '无法获取支付配置';
    		
    		return [];
    	}
    	
    	$orderData = SessionGet::getInstance('order_data')->get();
    	
    	$result = $this->dispatcherPay($payConfig, $orderData);
    	
    	if (empty($result)) {
    		
    		$this->errorMessage = '无法三方支付配置';
    		
    		return [];
    	}
    	return $result;
    }
    
}