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

use Common\Model\StoreMemberLevelModel;
use Think\Cache;
use Common\Tool\Tool;
use Common\Tool\Extend\ArrayChildren;
use Think\Log;

/**
 * 会员等级
 * 
 * @author Administrator
 *        
 */
class StoreMemberLevelLogic extends AbstractGetDataLogic {
	
	/**
	 * 是否添加店铺会员
	 * @var string
	 */
	private $isAddMember = false;
	
	public function getIsAddMember()
	{
		return $this->isAddMember;
	}
	
	/**
	 * 构造方法
	 * 
	 * @param array $data        	
	 */
	public function __construct(array $data = [], $split = '') {
		$this->data = $data;
		$this->splitKey = $split;
		$this->modelObj = new StoreMemberLevelModel ();
	}
	
	/**
	 * 获取结果卍卐卍
	 */
	public function getResult() {
		
		$data = $this->getLevelList ();
		
		if (empty ( $data )) {
			//提交事务 无需添加店铺会员
			$this->modelObj->commit();
			return [ ];
		}
		
		$resource = $this->data;
		
		$resourceCovert = [ ];
		
		// 这里 刚生成的订单（商家编号）是不会重复的
		$resourceCovert = (new ArrayChildren ( $resource ))->convertIdByData ( 'store_id' );
		$flag = [];
		
		foreach ( $data as $key => $value ) {
			
			if (! isset ( $resourceCovert [$value ['store_id']] )) {
				continue;
			}
			
			if ($value [StoreMemberLevelModel::$moneySmall_d] <= $resourceCovert [$value ['store_id']] ['total_money'] && $resourceCovert [$value ['store_id']] ['total_money'] <=
			$value [StoreMemberLevelModel::$moneyBig_d]) 
			{
				$flag[$value ['store_id']] = $resourceCovert [$value ['store_id']];
			}
		}
		
		if (empty($flag)) { 
			//提交事务 无需添加店铺会员
			$this->modelObj->commit();
			return [];
		}
		
		$this->isAddMember = true;
		
		return $flag;
	}
/**
* 获取结果卍卐卍
*/
    public function getResults() {

        $data = $this->getLevelLists ();

        if (empty ( $data )) {
            //提交事务 无需添加店铺会员
            $this->modelObj->commit();
            return [ ];
        }

        $resource = $this->data;

        $resourceCovert = [ ];

        // 这里 刚生成的订单（商家编号）是不会重复的
        $resourceCovert = (new ArrayChildren ( $resource ))->convertIdByData ( 'store_id' );
        $flag = [];

        foreach ( $data as $key => $value ) {

            if (! isset ( $resourceCovert [$value ['store_id']] )) {
                continue;
            }

            if ($value [StoreMemberLevelModel::$moneySmall_d] <= $resourceCovert [$value ['store_id']] ['total_money'] && $resourceCovert [$value ['store_id']] ['total_money'] <=
                $value [StoreMemberLevelModel::$moneyBig_d])
            {
                $flag[$value ['store_id']] = $resourceCovert [$value ['store_id']];
            }
        }

        if (empty($flag)) {
            //提交事务 无需添加店铺会员
            $this->modelObj->commit();
            return [];
        }

        $this->isAddMember = true;

        return $flag;
    }
	/**
	 * 获取会员等级列表
	 * 
	 * @return array
	 */
	public function getLevelList() {
		$cache = Cache::getInstance ( '', [ 
			'expire' => 3600 
		] );
		
		$idString = Tool::characterJoin ( $this->data, $this->splitKey );
		
		$key = base64_encode ( $idString );
		
		$data = $cache->get ( $key );
		
		if (! empty ( $data )) {
			return $data;
		}
		
		$data = $this->modelObj->field ( $this->getTableColum () )->where ( StoreMemberLevelModel::$storeId_d . ' in (%s)', $idString )->select ();
		
		if (empty ( $data )) {
			return [ ];
		}
		
		return $data;
	}
    /**
     * 获取会员等级列表
     *
     * @return array
     */
    public function getLevelLists() {
        $cache = Cache::getInstance ( '', [
            'expire' => 3600
        ] );

        $idString = $this->data;

        $key = base64_encode ( $idString );

        $data = $cache->get ( $key );

        if (! empty ( $data )) {
            return $data;
        }

        $data = $this->modelObj->field ( $this->getTableColum () )->where ( StoreMemberLevelModel::$storeId_d . ' in (%s)', $idString )->select ();

        if (empty ( $data )) {
            return [ ];
        }

        return $data;
    }
	/**
	 * {@inheritdoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getTableColum()
	 */
	protected function getTableColum() :array {
		return [ 
			StoreMemberLevelModel::$id_d,
			StoreMemberLevelModel::$discount_d,
			StoreMemberLevelModel::$conditionNum_d,
			StoreMemberLevelModel::$storeId_d,
			StoreMemberLevelModel::$moneySmall_d,
			StoreMemberLevelModel::$moneyBig_d,
			StoreMemberLevelModel::$numBig_d 
		];
	}
	
	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
	 */
	public function getModelClassName() :string {
		return StoreMemberLevelModel::class;
	}
}