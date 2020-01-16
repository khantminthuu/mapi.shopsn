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

use Common\Tool\Extend\ArrayChildren;
use Common\Tool\Tool;
use Common\Model\UserLevelModel;

/**
 * 用户逻辑处理
 * 
 * @author Administrator
 */
class UserLevelLogic extends AbstractGetDataLogic {
	/**
	 * 构造方法
	 * 
	 * @param array $data        	
	 */
	public function __construct(array $data, $split = null) {
		$this->data = $data;
		
		$this->splitKey = $split;
		
		$this->modelObj = new UserLevelModel();
		
		$this->covertKey = UserLevelModel::$levelName_d;
	}
	
	/**
	 * 获取用户等级数据
	 * 
	 * {@inheritdoc}
	 *
	 * @see \Common\Logic\AbstractGetDataLogic::getResult()
	 */
	public function getResult() {
		$key = 'LEVEL_I_USER_WHY_YOU';
		
		$data = S ( $key );
		
		if (empty ( $data )) {
			
			$data = $this->modelObj->where ( UserLevelModel::$status_d . ' = 1' )->select ();
		} else {
			return $data;
		}
		
		if (empty ( $data )) {
			return [ ];
		}
		
		$data = (new ArrayChildren ( $data ))->convertIdByData ( UserLevelModel::$id_d );
		
		S ( $key, $data, 600 );
		
		return $data;
	}
	
	/**
	 * 获取具体等级的上下限
	 * 
	 * @return []
	 */
	public function getLevelByUpperAndLowerLimitsId() {
		$result = $this->getResult ();
		
		if (empty ( $result [$this->data ['id']] )) {
			return [ ];
		}
		
		return [ 
						$result [$this->data ['id']] [UserLevelModel::$integralSmall_d],
						$result [$this->data ['id']] [UserLevelModel::$integralBig_d] 
		];
	}
	
	/**
	 * 返回模型类名
	 * 
	 * @return string
	 */
	public function getModelClassName() :string {
		return UserLevelModel::class;
	}
	
	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \Common\Logic\AbstractGetDataLogic::likeSerachArray()
	 */
	protected function likeSerachArray() :array {
		return [ 
						UserLevelModel::$levelName_d 
		];
	}
	
	/**
	 * 确认等级
	 */
	public function getLevelByUser() {
		$data = $this->getResult ();
		
		if (empty ( $data )) {
			return [ ];
		}
		
		$userData = $this->data;
		
		$currentIntergal = ( int ) $this->data[$this->splitKey];
		
		$next = [];
		
		foreach ( $data as $key => $level ) {
			
			// 积分下限
			$intergalSmall = ( int ) $level [UserLevelModel::$integralSmall_d];
			// 积分上限
			$intergalBig = ( int ) $level [UserLevelModel::$integralBig_d];
			
			if ($intergalSmall <= $currentIntergal && $currentIntergal < $intergalBig) {
				$next ['level_name'] = $level [UserLevelModel::$levelName_d];
				break;
			}
		}
		
		if (! isset ( $next ['level_name'] )) {
			$next ['level_name'] = '';
		}
		
		return $next;
	}
	
	/**
	 * 根据会员等级字符串 查询数据
	 * 
	 * @return array
	 */
	public function getDataByGroup() {
		$data = $this->data;
		
		$parkey = $this->splitKey;
		
		if (empty ( $data ) || ! is_string ( $parkey )) {
			return array ();
		}
		
		$idString = Tool::characterJoin ( $data, $parkey );
		
		if (empty ( $idString )) {
			return array ();
		}
		
		$field = UserLevelModel::$id_d . ',' . UserLevelModel::$levelName_d;
		
		$userData = $this->modelObj->where ( UserLevelModel::$id_d . ' in (' . $idString . ')' )->getField ( $field );
		
		if (empty ( $userData )) {
			return array ();
		}
		
		$flag = [ ];
		foreach ( $data as $key => & $value ) {
			
			if (empty ( $value [$parkey] )) {
				continue;
			}
			$flag = explode ( ',', $value [$parkey] );
			if (empty ( $flag )) {
				continue;
			}
			
			foreach ( $flag as $k => $v ) {
				if (! empty ( $userData [$v] )) {
					$value [$parkey] .= ',' . $userData [$v];
					$value [$parkey] = substr ( $value [$parkey], 2 );
				}
			}
		}
		return $data;
	}
}