<?php
// +----------------------------------------------------------------------
// | OnlineRetailers [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2003-2023 www.shopsn.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed 亿速网络（http://www.shopsn.net）
// +----------------------------------------------------------------------
// | Author: 王强 <opjklu@126.com>
// +----------------------------------------------------------------------
namespace Common\Strategy\SpecificStrategy;


use Common\Strategy\Strategy;
use Common\Strategy\Attribute\Attribute;

/**
 * 买就送代金券 类
 * @author 王强
 * @version 1.0.0
 */
class WeightMoney implements Strategy
{
	use Attribute;
    
	/**
	 * @param array $receive
	 */
	public function __construct( array $receive, $freightData)
	{
		$this->receive = $receive;
		
		$this->freightData = $freightData;
	}
    
    /**
     * {@inheritDoc}
     * @see \Home\Strategy\Strategy::acceptCash()
     */
    public function acceptCash()
    {
        // TODO Auto-generated method stub
        
        
        if (empty($this->freightData)) {
        	throw new \Exception("重量出了问题");
        }
       
        $data = $this->receive;
        
        
//         if ( !empty($data[FreightConditionModel::$mailArea_monery_d]) && $goodsMoney >= $data[FreightConditionModel::$mailArea_monery_d]) { //江浙沪大于1000包邮
            
//             return 0;
//         }
      
        $money = $this->algMoney();
        
        return $money;
        
    }
    
    /**
     * @param array $data
     */
    private function algMoney ()
    {
      
    	$data = $this->receive;
    	
        // 总重（单个商户）
        $heavy = $this->freightData;
        
        $money = 0;
        
        //首重
        $fristHeavy = 0;
        
        //续重
        $continuedHeavy = 0;
        
        //首费
        $fristMoney = 0;
        
        //续费
        $continuedMonery = 0;
        
        //计算钱的重量
        $unitWeight = 0;
        
        	
        $fristHeavy = floatval($data['first_weight']);
        	
        $continuedHeavy = floatval($data['continued_heavy']);
        	
        $fristMoney = floatval($data['frist_money'] - $data['mail_area_monery']);
        	
        $continuedMonery = (float)$data['continued_money'];
        	
        $unitWeight = ($heavy - $fristHeavy)/1000 ;
        	
        $unitWeight = $unitWeight <= 0 ? 0 : $unitWeight;
        
        $fristMoney = $fristMoney < 0 ? 0 : $fristMoney;
        	 
        	//         showData($heavy);// 12.5 20
        	
        	//         showData($fristHeavy);// 1 2
        	
        	//         showData($continuedHeavy); //1 5
        	
        	//         showData($continuedMonery);// 6 7
        	
        	//         showData($fristMoney); // 8   8+ (11.5/1)*6 4
        	
        	//         showData($unitWeight); // 11.5 (18/5)*
        	
        $money = bcadd($fristMoney , ($unitWeight/$continuedHeavy)*$continuedMonery, 2);
        return $money;
    }
}