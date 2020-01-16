<?php
namespace Common\Logic;
use Common\Model\UserModel;
use Common\Model\IntegralGoodsModel;
use Common\Model\GoodsModel;
use Think\Cache;

/**
 * 逻辑处理层
 */
class IntegralGoodsLogic extends AbstractGetDataLogic
{
	private $integralData = [];
	
	/**
	 * 
	 * @return array
	 */
	public function getIntegralData()
	{
		return $this->integralData;
	}
    /**
     * 构造方法
     * @param array $data
     */
    public function __construct(array $data = [], $split = '')
    {
        $this->data = $data;
        $this->splitKey = $split;
        $this->modelObj = new IntegralGoodsModel();
      
    }
    /**
     * 返回验证数据
     */
    public function getValidateByLogin()
    {
        $message = [
            'good_id' => [
                'required' => '必须传入商品ID',
            ],
        ];
        return $message;
    }
    
   
    
    /**
     * 返回验证数据
     */
    public function getValidateBydetail()
    {
        $message = [
            'id' => [
                'number' => '必须是数字',
            ],
            'goods_num' => [
                'number' => '必须是数字',
            ],
        ];
        return $message;
    }
    /**
     * 获取结果
     */
    public function getResult()
    {

    }

    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
     */
    public function getModelClassName() :string
    {
        return IntegralGoodsModel::class;
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
            UserModel::$userName_d,
        ];
    }
    /**
     * 获取积分商品
     *
     */
    public function getAllIntegralGoods(){
        if (empty($this->data['intergral_upper']) && empty($this->data['intergral_lower'])){
            $where = [
                'status' => '1',
                "is_show" =>'1',
            ];
        }
        if (empty($this->data['intergral_upper']) && !empty($this->data['intergral_lower'])){
            $where = [
                'status' => '1',
                'integral' => ['GT',$this->data['intergral_lower']],
                "is_show" =>'1',
            ];

        }
        if (!empty($this->data['intergral_upper']) && !empty($this->data['intergral_lower'])){

            $where = [
                'status' => '1',
                'integral' => ['between',[$this->data['intergral_lower'],$this->data['intergral_upper']]],
                "is_show" =>'1',
            ];
        }
        $field = 'id,goods_id,integral,create_time,money';
        $page = empty($this->data['page'])?0:$this->data['page'];
        $List = $this->modelObj->field($field)->where($where)->page($page.',10')->order('create_time DESC')->select();
        $count =  $this->modelObj->where($where)->count();
        $Page = new \Think\Page($count,10);
        $show = $Page->show();
        $totalPages = $Page->totalPages;
        $dataList['count'] = $count;
        $dataList['totalPages'] = $totalPages;
        $dataList['page_size'] = 10;
        $dataList['records'] = $List;
        $goodModel = new GoodsModel();
        foreach ($dataList['records'] as $key => $value){
            //得到商品的图片 简介
            $goodInfo = $goodModel->getGoodTitle($value['goods_id']);
            $dataList['records'][$key]['title'] = $goodInfo['title'];
            $dataList['records'][$key]['store_id'] = $goodInfo['store_id'];
            $dataList['records'][$key]['image'] = $goodInfo['image'];
        }
        return $dataList;
    }
    /**
     * 获取积分商品的详细信息
     */
    public function getIntegralGoodInfo(){
    	
    	$cache = Cache::getInstance('', ['expire' => 80]);
    	
    	$key = $this->data['id'].'_integral_what';
    	
    	$data = $cache->get($key);
    	
    	if (!empty($data)) {
    		return $data;
    	}
    	
    	$data = $this->modelObj->field(IntegralGoodsModel::$isShow_d, true)
    		->where(IntegralGoodsModel::$id_d.'=:id and '.IntegralGoodsModel::$isShow_d.'= 1')
    		->bind([':id' => $this->data['id']])
    		->find();
    	
    	if (empty($data)) {
    		$this->errorMessage = '没有数据';
    		return [];
    	}
    	
    	$data[IntegralGoodsModel::$delayed_d] =  $data[IntegralGoodsModel::$delayed_d] * 86400;
    	
    	$cache->set($key, $data);
    	
    	return $data;
    }
    
    /**
     * 验证是否可兑换
     */
    public function checkIsConvertibility()
    {
    	$integral = $this->getIntegralGoodInfo();
    	
    	if (empty($integral)) {
    		$this->errorMessage = '积分商品错误';
    		return false;
    	}
    	
    	if ((time() - $integral[IntegralGoodsModel::$createTime_d] -$integral[IntegralGoodsModel::$delayed_d] * 86400) < 0) {
    		$this->errorMessage = '购买还没开始';
    		return false;
    	}
    	
    	$this->integralData = $integral;
    	
    	return true;
    }
    
    /**
     * 获取商品关联字段
     * @return string
     */
    public function getSplitKeyByGoods()
    {
    	return IntegralGoodsModel::$goodsId_d;
    }
}
