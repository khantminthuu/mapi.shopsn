<?php
namespace Common\Logic;

use Common\Model\AdModel;
use Think\Cache;

class AdLogic extends AbstractGetDataLogic
{
	/**
	 * 构造方法
	 * @param unknown $data
	 */
	public function __construct(array $data = [], $split = '')
	{
		$this->data = $data;
		
		$this->splitKey = $split;
		
		$this->modelObj = new AdModel();
	}
	
	/**
	 * 获取店品牌数据
	 */
	public function getResult()
	{
		$cache = Cache::getInstance('', ['expire' => 90]);
		
		$key = 'ad_page'.'_'.$this->data['page'];
		
		$data = $cache->get($key);
		
		if (!empty($data)) {
			return $data;
		}
		
		//楼层底部广告
		$data = $this->modelObj
			->field("id,pic_url,ad_link")
			->where(['ad_space_id'=>50,'enabled'=>1])
			->order("sort_num desc")
			->page($this->data['page'], 1)
			->find(); 
		if (empty($data)) {
			return [];
		}
		
		$cache->set($key, $data);
		
		return $data;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
	 */
	public function getModelClassName() :string
	{
		return AdModel::class;
	}
	
	/**
	 * 切换状态验证
	 * @return string[][]
	 */
	public function getMessageByChangeStatus()
	{
		return [
			AdModel::$id_d => [
				'number' => 'id 必须是数字'
			],
			AdModel::$enabled_d => [
				'number' => '状态必须是数字,且介于${0-1}'
			],
		];
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getMessageNotice()
	 */
	public function getMessageNotice() :array
	{
	}
	
	public function getSplitKeyByAdSpace()
	{
		return AdModel::$adSpace_id_d;
	}
	
	/**
	 * 获取验证规则
	 */
	public function getValidateRule()
	{
	}
	
	/**
	 * 验证page
	 */
	public function getValidateByClassPage()
	{
		return [
			'page' => [
				'number' => '商品分类编号必须是数字'
			]
		];
	}
    /**
     * 获取分类楼层广告图
     */
    public function getFloorAd()
    {
        $post = $this->data;
        //楼层底部广告
        $data = $this->modelObj
            ->field("id,pic_url,ad_link")
            ->where(['ad_space_id'=>50,'enabled'=>1,'class_id'=>$post['id']])
            ->find();
        return $data;
    }
}