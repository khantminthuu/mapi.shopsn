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

use Common\Model\StoreMemberModel;
use Think\Log;

/**
 * 店铺会员逻辑处理
 * @author Administrator
 *
 */
class StoreMemberLogic extends AbstractGetDataLogic
{
	/**
	 * 要更新的会员
	 * @var array
	 */
	private $already = [];
	
	/**
	 * 要添加的会员
	 * @var array
	 */
	private $thereIsNo = [];
	/**
	 * 用户编号
	 * @var integer
	 */
	private $userId = 0;
	/**
	 * 构造方法
	 * @param array $data
	 */
	public function __construct(array $data = [], $split = '', $userId = 0) {
		$this->data = $data;
		$this->splitKey = $split;
		
		$this->userId = $userId;
		
		$this->modelObj = new StoreMemberModel();
	}
	
	/**
	 * 获取结果
	 */
	public function getResult() {
		
		$data = $this->getDetailMember();
		
		$already = [];
		
		$thereIsNo = [];
		
		foreach ($this->data as $key => $value) {
			if (!empty($data[$value[StoreMemberModel::$storeId_d]])) {
				$already[$key] = $value;
			} else {
				$thereIsNo[$key] = $value;
			}
		}
		
		
		
		if (!empty($already)) {
			//更新
			$this->already = $already;	
			$sql = $this->buildUpdateSql();
			
			try {
				
				$status = $this->modelObj->execute($sql);
				
			} catch (\Exception $e) {
				
				$this->errorMessage = $e->getMessage();
				
				$this->modelObj->rollback();
				
				$time = date('y_m_d');
				
				Log::write('店铺会员更新失败 --'.$sql, Log::ERR, '', './Log/store_member_'.$time.'.txt');
				
				return false;
				
			}
		}
		
		$addStatus = false;
		if (!empty($thereIsNo)) {
			//添加
			$this->thereIsNo = $thereIsNo;
			
			$addStatus = $this->addAll();
		}
		
		if (!$this->traceStation($addStatus)) {
			return false;
		}
		
		$this->modelObj->commit();
		return true;
	}
	
	/**
	 * 获取具体的店铺会员
	 * @return array
	 */
	public function getDetailMember()
	{
		$storeIdString = implode(',', array_keys($this->data));
		
		$data = $this->modelObj->where(StoreMemberModel::$storeId_d.' in(%s) and '.StoreMemberModel::$memberId_d .'=:id', $storeIdString)
			->bind([':id' =>$this->userId])
			->getField(StoreMemberModel::$storeId_d.','.StoreMemberModel::$memberId_d.','.StoreMemberModel::$totalTransaction_d.','.StoreMemberModel::$transactionNumber_d);
		return $data;
	}
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getParseResultByAddAll()
	 */
	protected function getParseResultByAddAll() :array
	{
		$data = $this->thereIsNo;
		
		$result = [];
		
		$index = 0;
		
		foreach ($data as $key => $value) {
			$result[$index] = [
				StoreMemberModel::$memberId_d => $this->userId,
				StoreMemberModel::$storeId_d  => $value['store_id'],
				StoreMemberModel::$totalTransaction_d => $value['total_money'],
				StoreMemberModel::$transactionNumber_d => 1
			];
			$index++;
		}
		return $result;
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getColumToBeUpdated()
	 */
	protected function getColumToBeUpdated() :array
	{
		return [
			StoreMemberModel::$totalTransaction_d,
			StoreMemberModel::$transactionNumber_d,
			StoreMemberModel::$updateTime_d
		];
	}
	
	/**
	 * 要更新的数据【已经解析好的】
	 * @return array
	 */
	protected function getDataToBeUpdated() :array
	{
		$result = [];
		
		$i = 0;
		
		foreach ($this->already as $key => $value) {
			$result[$this->userId][$i] = StoreMemberModel::$totalTransaction_d.' + '.$value['total_money'];
			
			$result[$this->userId][$i] = StoreMemberModel::$transactionNumber_d.' + 1';
			
			$result[$this->userId][$i] = time();
			
			$i++;
		}
		
		return $result;
	}
	
	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
	 */
	public function getModelClassName() :string {
		return StoreMemberModel::class;
	}
}