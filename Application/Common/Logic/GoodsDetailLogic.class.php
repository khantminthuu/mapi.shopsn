<?php
namespace Common\Logic;
use Common\Model\UserModel;
use Common\Model\GoodsDetailModel;
use Common\Model\GoodsModel;
use Think\Cache;

/**
 * 逻辑处理层
 *
 */
class GoodsDetailLogic extends AbstractGetDataLogic
{
    /**
     * 构造方法
     * @param array $data
     */
    public function __construct(array $data = [], $split = '')
    {
        $this->data = $data;
        $this->splitKey = $split;
        $this->modelObj = new GoodsDetailModel();
      
    }
    /**
     * 返回验证数据
     */
    public function getValidateByLogin()
    {
        $message = [
            'goods_id' => [
                'required' => '必须输入商品Id',
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
        return GoodsDetailModel::class;
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
     * 验证字段
     */
    public function getMessageByDetail()
    {
    	return [
    		GoodsDetailModel::$goodsId_d => [
    			'number' => '必须是数字'
    		]
    	];
    }
    
    /**
     * 获取商品详情
     */
    public function getGoodDetail(){
    	
    	$cache = Cache::getInstance('', ['expire' => 60]);
    	
    	$key = $this->data[GoodsDetailModel::$goodsId_d].'_goods_images';
    	
    	$data = $cache->get($key);
    	
    	if (!empty($data)) {
    		return $data;
    	}

    	$p_id = M('goods')->where(['id'=>$this->data['goods_id']])->getField('p_id');
    	if($p_id == 0){
            $data = $this->modelObj->where(['goods_id'=> $this->data[GoodsDetailModel::$goodsId_d]])->getField(GoodsDetailModel::$detail_d);
        }else{
            $data = $this->modelObj->where(['goods_id'=> $p_id])->getField(GoodsDetailModel::$detail_d);
        }

        
        if (empty($data)) {
            return "";
        }
        $data = htmlspecialchars_decode($data);
	
        $cache->set($key, $data);
        
        return $data;
    }

   
}
