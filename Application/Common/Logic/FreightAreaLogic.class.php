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
namespace Common\Logic;

use Common\Model\FreightAreaModel;
use Think\Cache;
use Common\Tool\Extend\ArrayChildren;

/**
 * 配送地区表
 * @author Administrator
 * @version 1.0.0
 */
class FreightAreaLogic extends AbstractGetDataLogic
{
    /**
     * 构造方法
     * @param array  $data
     * @param string $split
     */
    public function __construct(array $data, $split = "")
    {
        $this->data = $data;
    
        $this->splitKey = $split;
    
        $this->modelObj = new FreightAreaModel();
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getResult()
     */
    public function getResult(){}
    
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
     */
    public function getModelClassName() :string
    {
        return FreightAreaModel::class;
    }
    
    /**
     * 获取运送地区
     */
    public function getAddressArea() :array
    {
        if (empty($this->data['con'])) {
            return [];
        }
        
        $cache = Cache::getInstance('', ['expire' => 105]);
        
        $idString = implode(',', array_keys($this->data['con']));
        
        $temp = $cache->get($idString.'khlj');
        
        if (empty($temp)) {
        	$data = $this->modelObj->where(FreightAreaModel::$freightId_d .' in (%s)',$idString)->select();
        } else {
        	return $temp;
        }
        
        if (empty($data)) {
        	return [];
        }
        
        $temp = [];
        
        foreach ($data  as $key => $value) {
        	$temp[$value[FreightAreaModel::$freightId_d]][] = $value[FreightAreaModel::$mailArea_d];
        }
        
        $cache->set($idString.'khlj', $temp);
        
        return $temp;
    }
    
    /**
     * 收货地址是否在包邮地区内
     * @return boolean
     */
    public function sendAddressIsInFreeShipping() :array
    {
    	$area = $this->getAddressArea();
    	
    	$freightMode = $this->data['freight_mode'];
    	
    	if (empty($area)) {
    		//没有包邮设置
    		foreach ($freightMode as $key => & $value) {
    			$value['mail_area_num'] = 0;
    			$value['mail_area_wieght'] = 0;
    			$value['mail_area_volume'] = 0;
    			$value['mail_area_monery'] = 0;
    		}
    		
    		return $freightMode;
    	}
    	
    	$provId = $this->data['param']['prov_id'];
    	
    	$cityId = $this->data['param']['city_id'];
    	
    	$sendArea = [
    		$provId => $provId,
    		$cityId => $cityId
    	];
    	
    	$isFreeShipping = [];//判断 哪个商家包邮
    	
    	foreach ($area as $key => $value) {
    		if (!empty(array_intersect($sendArea, $value))) {
    			$isFreeShipping[$key] = $key;
    		}
    	}
    	
    	$arrayObj = new ArrayChildren($this->data['con']);
    	
    	//获取指定条件包邮商家
    	$result = $arrayObj->getArrayAssocByData($isFreeShipping);
    	
    	$arrayObj->setData($result);
    	
    	$result = $arrayObj->convertIdByData('freight_id');
    	
    	
    	//指定條件包郵
    	foreach ($freightMode as $key => & $value) {
    		if (empty($result[$value['freight_id']])) {
    			$value['mail_area_num'] = 0;
    			$value['mail_area_wieght'] = 0;
    			$value['mail_area_volume'] = 0;
    			$value['mail_area_monery'] = 0;
    		} else {
    			$value['mail_area_num'] = $result[$value['freight_id']]['mail_area_num'];
    			$value['mail_area_wieght'] = $result[$value['freight_id']]['mail_area_wieght'];
    			$value['mail_area_volume'] = $result[$value['freight_id']]['mail_area_volume'];
    			$value['mail_area_monery'] = $result[$value['freight_id']]['mail_area_monery'];
    		}
    	}
    	
    	return $freightMode;
    }
    
    /**
     * 获取关联字段
     */
    public function getMialAreaSplitKey() :string
    {
        return FreightAreaModel::$mailArea_d;
    }
}