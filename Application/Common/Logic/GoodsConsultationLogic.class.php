<?php
namespace Common\Logic;

use Think\AjaxPage;
use Common\Model\GoodsConsultationModel;
use Think\Cache;
use Think\SessionGet;

/**
 * 咨询回复
 * @author Administrator
 *
 */
class GoodsConsultationLogic extends AbstractGetDataLogic
{
	/**
	 * 构造方法
	 * @param array $data
	 */
	public function __construct(array $data = [], $split = null)
	{
		$this->data = $data;
		
		$this->modelObj = new GoodsConsultationModel();
		
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
	 * 获取一个商品的咨询
	 */
	public function getGoodsConsultationByGoods()
	{
		
		$cache = Cache::getInstance('', ['expire' => 80]);
		
		$key = $this->data['page'].'goodsconsultation';
		
		$data = $cache->get($key);
		
		if (!empty($data)) {
			return $data;
		}
		
		$this->searchTemporary = [GoodsConsultationModel::$goodsId_d => $this->data['goods_id']];
		
		$data = parent::getDataList();
		
		if (empty($data['records'])) {
			return $data;
		}
		
		$cache->set($key, $data);
		
		return $data;
	}
	
	/**
	 * 验证商品编号
	 */
	public function getMessageValidateByGoods()
	{
		$message = [
			'goods_id' => [
				'number' => '商品ID必须是数字',
			]
		];
		
		return $message;
	}
	/**
	 * 验证提问
	 */
	public function getMessageValidate(){
		$message = [
			'goods_id' => [
				'number' => '商品ID必须是数字',
			 ],
			 'content'  => [
				'required' => '必须输入问题内容',
			 ],
		];
		return $message;
	}
	
	/**
	 * 获取模型类名
	 * @return string
	 */
	public function getModelClassName() :string
	{
		return GoodsConsultationModel::class;
	}
	
	/**
	 * 管理员添加回复
	 */
	public function addContent (array $post)
	{
		if (!$this->isEmpty($post)) {
			return false;
		}
		
		$post[self::$userId_d] = SessionGet::getInstance('aid')->get();
		
		return $this->add($post);
		
	}
	
	/**
	 * 获取当前商品的咨询
	 * @param numeric $id 商品编号
	 * @return array 咨询数据数组
	 */
	public function getConsulation ($id, $number = 15)
	{
		if ( ($id = intval($id)) === 0) {
			return array();
		}
		
		$tableName = $this->getTableName();
		
		$count = S('count');
		
		if (empty($count)) {
			$count = $this
			->alias('con')
			->join($tableName.self::DBAS.' gc  ON con.'.self::$id_d.'= gc.'.self::$parentId_d)
			->where('con.'.self::$goodsId_d .'='.$id.' and con.'.self::$isShow_d .'= 1')->count();
			
			if ($count <= 0) {
				return array();
			}
			S('count', $count, 5);
		}
		
		
		$pageObj = new AjaxPage($count, $number);
		
		//内联自己的表
		$consulation = $this
		->field(array(
						'con.'.self::$goodsId_d,
						'con.'.self::$id_d,
						'con.'.self::$content_d,
						'con.'.self::$addTime_d,
						'gc.'.self::$content_d .self::DBAS.' reply_content',
						'gc.'.self::$addTime_d.self::DBAS .' reply_time'
		))->alias('con')
		->join($tableName.self::DBAS.' gc  ON con.'.self::$id_d.'= gc.'.self::$parentId_d)
		->where('con.'.self::$goodsId_d .'='.$id .' and con.'.self::$isShow_d .'= 1')
		->limit($pageObj->firstRow.','.$pageObj->listRows)
		->select();
		$data = array();
		$data['data'] = $consulation;
		$data['page'] = $pageObj->show();
		return $data;
	}
	
	/**
	 * 提交咨询
	 */
	public function addConsulation (array $post)
	{
		if (!$this->isEmpty($post)) {
			return false;
		}
		
		$userId = SessionGet::getInstance('user_id')->get();
		if (!empty($userId)) {
			$post[self::$userId_d] = $userId;
		}
		return $this->add($post);
	}
	

	/**
	 * 删除问题及其回答
	 */
	public function deleteAllConsulationById ($id)
	{
		if ( ($id = intval($id)) === 0) {
			return false;
		}
		$tableName = $this->getTableName();
		//链表删除
		return $this->execute('DELETE  dg, gc
                FROM
                '.$tableName.' as  dg , '.$tableName.' as gc WHERE dg.'.self::$id_d.' = gc.'.self::$parentId_d.'
                AND (dg.'.self::$id_d.' ='.$id.' OR gc.'.self::$parentId_d.'='.$id.')'
				);
	}
	
	/**
	 * 获取商品关联字段
	 */
	public function getSplitKeyByGoods()
	{
		return GoodsConsultationModel::$goodsId_d;
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getParseResultByAdd()
	 */
	protected function getParseResultByAdd() :array
	{
		$data = [];
		
		$data[GoodsConsultationModel::$userId_d] = SessionGet::getInstance('user_id')->get();
		
		$data[GoodsConsultationModel::$commentType_d] = 0;
		$data[GoodsConsultationModel::$goodsId_d] = $this->data['goods_id'];
		
		$data[GoodsConsultationModel::$content_d] = $this->data['content'];
		
		$data[GoodsConsultationModel::$ip_d] = get_client_ip();
		
		$data[GoodsConsultationModel::$createTime_d] = time();
		return $data;
	}
}