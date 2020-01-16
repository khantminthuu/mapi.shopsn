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
namespace Common\Logic;

use Common\Model\FreightSendModel;
use Think\Cache;

/**
 * 发货逻辑
 * @author 王强
 */
class FreightSendLogic extends AbstractGetDataLogic
{
	/**
	 * 具体的运费计算方式(剔除包邮的)
	 * @var array
	 */
	private $modeDetail = [];
	
	/**
	 * @return array
	 */
	public function getModeDetail()
	{
		return $this->modeDetail;
	}
	
    /**
     * 构造方法
     * @param array  $data
     * @param string $split
     */
    public function __construct(array $data, $split = "")
    {
        $this->data = $data;
    
        $this->splitKey = $split;
    
        $this->modelObj = new FreightSendModel();
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
        return FreightSendModel::class;
    }
    
    /**
     * 获取配送地址 
     * @return array
     */
    public function getSendAddress()
    {
        if (empty($this->data['f_mode'])) {
            return [];
        }
        
        $modeConf = $this->data['f_mode'];
        
        $cache = Cache::getInstance('', ['expire' => 106]);
        
        $key = implode(',', array_keys($modeConf));
        
        $temp = $cache->get($key.'_edfg');
        
        if (empty($temp)) {
        	$data = $this->modelObj
        		->where(FreightSendModel::$freightId_d .' in (%s)', $key)
        		->select();
        } else {
        	return $temp;
        }
        
        if (empty($data)) {
        	return [];
        }
        
        $temp = [];
        
        foreach ($data  as $key => $value) {
        	$temp[$value[FreightSendModel::$freightId_d]][] = $value[FreightSendModel::$mailArea_d];
        }
        
        
        $cache->set($key.'_edfg', $temp);        
        
        return $temp;
    }
    
    /**
     * 验证收货地址是否在配送区域内
     * @return bool
     */
     public function userAddressIndexOfSendArea()
     {
     	$data = $this->getSendAddress();
     	
     	if (empty($data)) {
     		$this->errorMessage = '商家运费设置错误,请联系对应商品的商家';
     		return false;
     	}
     	
     	$area = [
     		$this->data['area_conf']['prov_id'],
     		$this->data['area_conf']['city_id'],
     	];
     	
     	
     	foreach ($data as $key => $value) {//没有指出具体商家
     		if (empty(array_intersect($area, $value))) {
     			$this->errorMessage = '配送区域不包含用户的收货地址';
     			return false;
     		}
     	}
     	
     	$freightConf = $this->data['f_mode'];
     	
     	$filterFreight = [];
     	
     	//筛选包邮->指定条件包邮和自定义运费
     	foreach ($freightConf as $key => $value) {
     		if (($value['is_free_shipping'] == 0 && $value['is_select_condition'] == 0)) {
     			continue;
     		}
     		$filterFreight[$key] = $value;
     	}
     	$this->modeDetail = $filterFreight;
     	
     	return true;
     }
    
    /**
     * 获取地区关联字段
     */
    public function getRegionAddress()
    {
        return FreightSendModel::$mailArea_d;
    }
}