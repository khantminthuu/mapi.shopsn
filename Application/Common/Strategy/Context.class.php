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
namespace Common\Strategy;


class Context
{
	/**
	 * 静态对象
	 * @var Context
	 */
    private static $obj;   
    
    private static $intaceObj = [];
    
    private static $newInstance = [];
    
    /**
     * 运费总价
     * @var []
     */
    private $totalMoney = 0.0;
    
    //构造方法参数
    private $freightConf; 
    
    //运费相关重量数量数据
    private $freightData = [];
    
    private static $freightType = [
        'Common\Strategy\SpecificStrategy\NumberMoney', //按件收费
        'Common\Strategy\SpecificStrategy\WeightMoney',  // 按件收费
        'Common\Strategy\SpecificStrategy\VolumeMoney'// 按体积
    ];
    
    /**
     * 构造方法
     * @param array $freightConf
     * @param array $freightData
     */
    private function __construct(array $freightConf, array $freightData)
    {
        $this->freightConf = $freightConf;
        
        $this->freightData = $freightData;
    }
    
    /**
     * 获取活动处理对象
     * @param AbstractActivity $activity 具体活动
     * @param mixed $param
     * @return Context
     */
    public static function getInstance (array $freightConf, array $freightData)
    {
        $class = __CLASS__;
        if (!(self::$obj instanceof $class)) {
        	self::$obj = new static($freightConf, $freightData);
        }
    
        return self::$obj;
    }
    
    public function getTotalMoney()
    {
    	return $this->totalMoney;
    }
    /**
     * 获取价格
     */
    public function getPriceByFreight ()
    {
       $freightConf = $this->freightConf;
       
       if (empty($this->freightConf)) {
       		throw new \Exception("支付配置有误"); 
       }
       
       $class = '';
       
       $obj = null;
       
       $money = [];
       
       $freightSource = 0;
       
       $reflection = null;
       
       $totalMoney = 0.0;
       
       try {
	       foreach ($freightConf as $key => $value) {
	       		$class = self::$freightType[$value['valuation_method']];
	       		
	       		$freightSource = $this->freightData[$value['valuation_method']][$value['store_id']];
	       		if (!isset(static::$intaceObj[$class])) {
	       			
	       			$obj = new \ReflectionClass($class);
	       			
	       			static::$newInstance[$class] = $obj->newInstanceArgs([$value, $freightSource]);
	       			
	       			$money[$value['store_id']] = $obj->getMethod('acceptCash')->invoke(static::$newInstance[$class]);
	       			
	       			static::$intaceObj[$class] = $obj;
	       			
	       		} else {
	       			
	       			$reflection = static::$intaceObj[$class];
	       			
	       			$reflection->getMethod('setReceive')->invoke(static::$newInstance[$class], $value);
	       			
	       			$reflection->getMethod('setFreightData')->invoke(static::$newInstance[$class], $freightSource);
	       			
	       			$money[$value['store_id']] = $reflection->getMethod('acceptCash')->invoke(static::$newInstance[$class]);
	       		}
	       		
	       		$totalMoney = bcadd($money[$value['store_id']], $totalMoney, 2);
	       }
	       
	       $this->totalMoney = $totalMoney;
       } catch (\Exception $e) {
       		throw $e;
       }
       return $money;
    }
}