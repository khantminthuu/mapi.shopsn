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

use Common\Model\FreightConditionModel;
use Think\Cache;
use Common\Tool\Extend\ArrayChildren;

/**
 * 运费条件逻辑处理
 * 
 * @author 王强
 * @version 1.0.0
 */
class FreightConditionLogic extends AbstractGetDataLogic
{

    /**
     * 构造方法
     * @param array $data            
     * @param string $split            
     */
    public function __construct(array $data, $split = "")
    {
        $this->data = $data;
        
        $this->splitKey = $split;
        
        $this->modelObj = new FreightConditionModel();
    }

    /**
     * {@inheritdoc}
     * @see \Common\Logic\AbstractGetDataLogic::getResult()
     */
    public function getResult()
    {}

    /**
     * {@inheritdoc}
     * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
     */
    public function getModelClassName() :string
    {
        return FreightConditionModel::class;
    }

    /**
     * 获取 运费配置信息
     */
    public function getFreightOneData()
    {
    	$cache = Cache::getInstance('', ['expire' => 100]);
    	
    	$idString = implode(',', array_keys($this->data));
    	
    	$data = $cache->get($idString.'freight');
    	
    	if (empty($data)) {
    		$notField = [
    			FreightConditionModel::$createTime_d,
    			FreightConditionModel::$updateTime_d
    		];
    		$data = $this->modelObj->field($notField, true)
    			->where(FreightConditionModel::$freightId_d . ' in(%s)', $idString)
    			->select();
    	} else {
    		return $data;
    	}
    	
        if (empty($data)) {
       		$this->errorMessage = '未设置包邮设置';
       		return [];
        }
        
        $data = (new ArrayChildren($data))->convertIdByData(FreightConditionModel::$id_d);
        
        
        $cache->set($idString.'freight', $data);
        return $data;
    }


    /**
     * {@inheritdoc}
     * @see \Common\Logic\AbstractGetDataLogic::getMessageNotice()
     */
    public function getMessageNotice() :array
    {
    	
    }
    /**
     * 获取包邮地区关联字段
     * @return string
     */
    public function getIdBySendSplitKey()
    {
        return FreightConditionModel::$id_d;
    }
    
}