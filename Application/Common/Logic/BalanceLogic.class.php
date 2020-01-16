<?php
namespace Common\Logic;

use Common\Model\BalanceModel;
use Think\Cache;
use Think\SessionGet;

/**
 * 余额支付
 * @author 王强
 *
 */
class BalanceLogic extends AbstractGetDataLogic
{
	/**
	 * 当前余额记录
	 * @var array
	 */
	private $source = [];
	
	/**
	 * 描述
	 * @var string
	 */
	private $description = '';
	
	/**
	 * 构造方法
	 * @param array $data
	 */
	public function __construct(array $data = [], $split = '', $description = '')
	{
		$this->data = $data;
		$this->splitKey = $split;
		
		$this->description = $description;
		
		$this->modelObj = new BalanceModel();
	}
	
	/**
	 * 计算总价
	 */
	private function totaMoney()
	{
		$info = $this->data ;
		
		$totalMoney = 0;
		
		foreach ($info as $value) {
			$totalMoney += $value['total_money'];
		}
		
		return $totalMoney;
	}
	
	/**
	 * 余额购买处理
	 * 获取结果
	 */
	public function getResult()
	{
		
		$totalMoney = $this->totaMoney();
		
		$data = $this->parseBalance($totalMoney);
		
		return $data;
		
	}
	
	/**
	 * 处理金额
	 * @return array
	 */
	private function parseBalance($totalMoney)
	{
	
		$moery = $this->getBalanceMoney();
		if (floatval($moery) < 0 || bccomp($moery, $totalMoney, 2) === -1) {
			return [
				'status' => 0,
				'message' => '余额不足',
				'data'=> 0,
			];
		}
		$result = [
			BalanceModel::$userId_d => SessionGet::getInstance('user_id')->get(),
			BalanceModel::$accountBalance_d => floatval($moery - $totalMoney),
			BalanceModel::$description_d    => $this->description,
			BalanceModel::$type_d=> 0,
			BalanceModel::$changesBalance_d=> $totalMoney,
			BalanceModel::$lockBalance_d=> 0,
			BalanceModel::$status_d => 1,
			BalanceModel::$modifyTime_d=> time(),
		];
		
		try {
			$status = $this->modelObj->add($result);
			
			return [
				'status' => 1,
				'message' => '余额充足',
				'data'=> $this->data,
				'balance_id' => $status
			];
		}catch (\Exception $e) {
			return [
				'status' => 0,
				'message' => $e->getMessage(),
				'data'=> '',
			];
		}
	}
	
	/**
	 * 开店
	 * @return array
	 */
	public function openShopParse()
	{
		$data = $this->parseBalance($this->data['money']);
		
		return $data;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
	 */
	public function getModelClassName() :string
	{
		return BalanceModel::class;
	}
	
	/**
	 * 获取余额
	 * @return float
	 */
	public function getBalanceMoney ()
	{
		$data = $this->modelObj
			->field(BalanceModel::$accountBalance_d.','.BalanceModel::$lockBalance_d)
			->where(BalanceModel::$userId_d.'=%d and '.BalanceModel::$status_d.'= 1', SessionGet::getInstance('user_id')->get())
			->order(BalanceModel::$id_d.' DESC ')
			->find();
		if (empty($data)) {
			return 0;
		}
		
		$money = floatval($data[BalanceModel::$accountBalance_d] - $data[BalanceModel::$lockBalance_d]);
		
		return $money;
	}
	/**
	 * 获取余额
	 * @return float
	 */
	public function getBalance()
	{
		$data = $this->modelObj
			->field("account_balance")
			->where(['user_id'=>SessionGet::getInstance('user_id')->get(),"status"=>1])
			->order('id DESC')
			->find();
		if (empty($data)) {
			return array("status"=>1,"meaasge"=>"获取成功","data"=>0);
		}
		
		return array("status"=>1,"message"=>"获取成功","data"=>$data['account_balance']);
	}
	
	/**
	 * 余额充值
	 * @param array $recharge
	 * @param string $className
	 */
	public function rechargeMoney()
	{
		$recharge = $this->data;
		
		$userId = SessionGet::getInstance('user_id')->get();
		
		$isHas = $this->modelObj
			->field(BalanceModel::$id_d.','.BalanceModel::$accountBalance_d.','.BalanceModel::$lockBalance_d)
			->where(BalanceModel::$userId_d.'= %d', $userId)
			->order(BalanceModel::$id_d.' DESC ')
			->find();
		
		$this->source = $isHas;
		
		$status=$this->addData();
		
		if (!$this->traceStation($status)) {
			$this->rollback();
			return false;
		}
		$key = $userId.'_'.$recharge['trade_no'];
		Cache::getInstance('', ['expire' => 1440])->set($key, $recharge['trade_no']);
		
		$this->modelObj->commit();
		return true;
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getParseResultByAdd()
	 */
	protected function getParseResultByAdd() :array
	{
		$balance = $this->source;

		if (!empty($balance)) {
			$money = floatval($balance[BalanceModel::$accountBalance_d] + $this->data['total_amount'] - $balance[BalanceModel::$lockBalance_d]);
		} else {
			$money = $this->data['total_amount'];
		}
		
		$data = [
			BalanceModel::$userId_d => SessionGet::getInstance('user_id')->get(),
			BalanceModel::$description_d => '余额充值',
			BalanceModel::$changesBalance_d => $this->data['total_amount'],
			BalanceModel::$accountBalance_d => $money,
			BalanceModel::$type_d => 1,
			BalanceModel::$lockBalance_d => 0,
			BalanceModel::$rechargeTime_d => time(),
			BalanceModel::$status_d => 1,
			BalanceModel::$modifyTime_d => 0
		];
		return $data;
	}
}