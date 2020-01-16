<?php
namespace Common\Logic;

use Common\Model\GoodsAdvisoryReplyModel;
use Think\Cache;

/**
 * 咨询回答
 * @author Administrator
 *
 */
class GoodsAdvisoryReplyLogic extends AbstractGetDataLogic
{
	/**
	 * 关联数据
	 * @var string
	 */
	private $idString = '';
	/**
	 * 构造方法
	 * @param array $data
	 */
	public function __construct(array $data = [], $split = null)
	{
		$this->data = $data;
		
		$this->modelObj = new GoodsAdvisoryReplyModel();
		
		$this->splitKey = $split;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getResult()
	 */
	public function getResult()
	{
		//TODO
	}
	
	/**
	 * 获取模型类名
	 * @return string
	 */
	public function getModelClassName() :string
	{
		return GoodsAdvisoryReplyModel::class;
	}
	
	/**
	 * 获取每个回答中的一条
	 * SELECT replay.user_id, replay.id, replay.content, replay.consultation_id, replay.type
		FROM (
			SELECT MAX(id) as id  FROM db_goods_advisory_reply
			WHERE consultation_id in (1, 2)
			GROUP BY consultation_id
		) goods_adv INNER JOIN db_goods_advisory_reply as replay
		ON goods_adv.id = replay.id
	 */
	public function getGoodsAdvisoryReply()
	{
		$idString = implode(',', array_column($this->data, $this->splitKey));
		
		$this->idString = $idString;
		
		$field = 'replay.user_id, replay.id, replay.content, replay.consultation_id, replay.type, d.count_answer';
		
		$table = $this->modelObj->getTableName();
		
		$integral = $this->modelObj
			->field($field)
			->table($table .' as replay')
			->join('(select MAX(`id`) as id, count(consultation_id) as count_answer  From '.$table.' where consultation_id in('.$idString.') group by consultation_id order by id desc ) as d ON  d.id = replay.id')
			->getField($field);
		
		return $integral;
	}
	
	/**
	 * 获取每个回答中的一条并缓存
	 */
	public function getGoodsAdvisoryReplyCache()
	{
		$cache = Cache::getInstance('', ['expire' => 80]);
		
		$key = md5($this->idString.'replay');
		
		$data = $cache->get($key);
		
		if (!empty($data)) {
			return $data;
		}
		
		$data = $this->getGoodsAdvisoryReply();
		
		
		$goodsReply = $this->data;
		
		foreach ($goodsReply as $key => & $value) {
			if (!isset($value[$this->splitKey])) {
				$value['answer'] = '';
				$value['count_answer'] = 0;
				continue;
			}
			$value['answer'] = $data[$value[$this->splitKey]]['content'];
			
			$value['count_answer'] = $data[$value[$this->splitKey]]['count_answer'];
		}
		
		$cache->set($key, $goodsReply);
		return $goodsReply;
	}
}