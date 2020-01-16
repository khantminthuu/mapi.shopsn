<?php

namespace Common\Logic;

use Common\Model\NewsModel;

/**
 * @name 消息逻辑层
 * 
 * @des 消息逻辑层
 * @updated 2017-12-22 19:42
 */
class NewsLogic extends AbstractGetDataLogic
{
	protected $userModelObj = '';
	/**
	 * 构造方法
	 * @param array $data
	 */
	public function __construct(array $data = [], $split = '')
	{
		$this->data = $data;
		
		$this->splitKey = $split;
		$this->modelObj = new NewsModel();
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
		return NewsModel::class;
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
			NewsModel::$theme_d,
			NewsModel::$newsInfo_d,
		];
	}
	
	/**
	 * @name 消息验证规则
	 * 
	 * @des 消息验证规则
	 * @updated 2017-12-22
	 */
	public function getRuleByLists()
	{
		$message = [
			'page'          => [
				'required'          => '参数不正确',
				'specialCharFilter' => '参数不正确',
			],
		];
		
		return $message;
	}
	
	/**
	 * @name 消息列表逻辑
	 * 
	 * @des 消息列表逻辑
	 * @updated 2017-12-22
	 */
	public function lists()
	{
		$userId = session('user_id');
		
		//#TODO 这里是查询条件
		$this->searchTemporary = [
			NewsModel::$userId_d => $userId,
		];
		
		//#TODO 这里是要查询的字段如果不传的话默认为表中的所有字段
		$this->searchField = 'id,news_info,create_time,theme';
		
		//#TODO 这里是按照什么排序查询，如果不传默认为ID DESC排序
		$this->searchOrder = 'create_time DESC';
		
		//#TODO 调用通用的获取列表的接口并返回数据  data=>['countTotal'=>2, 'records'=>[.....]]
		$data = parent::getDataList();
		if(!empty($data['records'])){
			foreach ($data['records'] as $key => $value){
				$data['records'][$key]['create_time'] = mdate($value['create_time']);//#TODO 将时间格式化为几分钟前
			}
		}
		return $data;
	}
	
	/**
	 * @name 查询单条消息验证规则
	 * 
	 * @des 查询单条消息验证规则
	 * @updated 2017-12-22
	 */
	public function getRuleByInfo()
	{
		$message = [
			'id'          => [
				'required'          => '参数不正确',
				'specialCharFilter' => '参数不正确',
				'number'            => '参数不正确',
			],
		];
		return $message;
	}
	
	/**
	 * @name 查询单条消息逻辑
	 * 
	 * @des 查询单条消息逻辑
	 * @updated 2017-12-22
	 */
	public function info()
	{
		$userId = session('user_id');
		//#TODO 这里是查询条件
		$this->searchTemporary = [
			NewsModel::$userId_d => $userId,
			NewsModel::$id_d => $this->data['id'],
		];
		
		//#TODO 这里是要查询的字段如果不传的话默认为表中的所有字段
		$this->searchField = 'id,news_info,create_time,theme';
		$retData = $this->getFindOne();
		if(empty($retData)){
			$this->errorMessage = '查询数据为空!';
			return [];
		}
		//#TODO 处理时间
		$retData['create_time'] = date('Y-m-d H:i', $retData['create_time']);
		return $retData;
	}
}
