<?php

namespace Common\Logic;

use Common\Model\AppFeedbackModel;

/**
 * @name 意见反馈逻辑层
 * 
 * @des 意见反馈逻辑层
 * @updated 2017-12-22 19:42
 */
class FeedbackLogic extends AbstractGetDataLogic
{
	/**
	 * 构造方法
	 * @param array $data
	 */
	public function __construct(array $data = [], $split = '')
	{
		$this->data = $data;
		
		$this->splitKey = $split;
		$this->modelObj = new AppFeedbackModel();
	}
	
	public function getResult()
	{
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
	 */
	public function getModelClassName() :string
	{
		return AppFeedbackModel::class;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::hideenComment()
	 */
	public function hideenComment() :array
	{
		return [
		
		];
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::likeSerachArray()
	 */
	public function likeSerachArray() :array
	{
		return [
			AppFeedbackModel::$tel_d,
			AppFeedbackModel::$content_d,
		];
	}
	
	/**
	 * @name 用户反馈验证规则
	 * 
	 * @des 用户反馈验证规则
	 * @updated 2017-12-22
	 */
	public function getRuleByFeedback()
	{
		$message = [
			'type'              => [
				'required'          => '请选择反馈类型',
				'specialCharFilter' => '请选择反馈类型',
			],
			'tel'              => [
				'required'          => '请输入联系方式',
				'specialCharFilter' => '请输入联系方式',
			],
			'content'              => [
				'required'          => '请输入反馈内容',
				'specialCharFilter' => '请输入反馈内容',
			],
		];
		
		return $message;
	}
	
	/**
	 * @name 用户登录逻辑
	 * 
	 * @des 用户登录逻辑
	 * @updated 2017-12-20
	 */
	public function feedback()
	{
		//#TODO 查询当天有没有反馈过，如果反馈过就直接返回提示，如果没有反馈过就新增到反馈表
		$userId = session('user_id');
		if(!in_array((int)$this->data['type'], [1,2,3,4,5,6])){
			$this->errorMessage = '提交成功!';//不能返回具体错误
			return [];
		}
		$this->searchTemporary = [
			'user_id' => $userId,
		];
		$this->searchOrder = 'create_time DESC';
		$find = $this->getFindOne();
		if(!empty($find)){
			$date = date("Y-m-d", (int)$find['create_time']);
			$toDay = date("Y-m-d");
			if ($date == $toDay) {
				$this->errorMessage = '您今天已经提交过反馈了';
				return [];
			}
		}
		$this->data['user_id'] = $userId;
		$this->data['create_time'] = time();
		$this->addData();
		return [
			'token' => session_id(),
		];
	}
}
