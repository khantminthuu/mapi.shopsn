<?php
declare(strict_types=1);
namespace Common\Logic;
use Common\Model\GoodsImagesModel;
use Think\Cache;
use Common\Tool\Extend\ArrayChildren;
/**
 * 逻辑处理层
 *
 */
class GoodsImagesLogic extends AbstractGetDataLogic
{
	/**
	 * 客户端参数
	 * @var array
	 */
	private $clientValue = [];
    /**
     * 构造方法
     * @param array $data
     */
    public function __construct(array $data = [], $split = '', array $clientValue = [])
    {
        $this->data = $data;
        $this->splitKey = $split;
        
        $this->clientValue = $clientValue;
        
        $this->modelObj = new GoodsImagesModel();
        
        
    }
    /**
     * 获取结果
     */
    public function getResult() :array
    {
		$data = $this->data;
		
		if (empty($data)) {
			return [];
		}
		
		
		$cache = Cache::getInstance('', ['expire' => 90]);
		
		$key = 'goods_page_gtx1060'.'_'.$this->clientValue['page'];
		$data = $cache->get($key);
		
		if (!empty($data)) {
			return $data;
		}
		
		$images = $this->getSlaveDataByMaster();
		
		if (empty($images)) {
			return [];
		}
		
		$cache->set($key, $images);
		
		return $images;
    }
	
    /**
     * 获取图片（根据商品）
     */
    public function getImageByArrayGoods() :array
    {
    	$data = $this->data;
    	
    	
    	$cache = Cache::getInstance('', ['expire' => 60]);
    	
    	$key = base64_encode(implode(',', array_keys($data)).'what_images_she');
    	$data = $cache->get($key);
    	
    	if (!empty($data)) {
    		return $data;
    	}
    	
    	$images = $this->getSlaveDataByMaster();
    	
    	if (empty($images)) {
    		$this->errorMessage = '没有图片';
    		return [];
    	}
    	
    	$cache->set($key, $images);
    	
    	return $images;
    }
    
    /**
     * 数据处理组合
     * @param array $slaveData
     * @param string $slaveColumnWhere
     * @return array
     */
    protected function parseSlaveData(array $slaveData, $slaveColumnWhere) :array
    {
    	$data = $this->data;
    	
    	$slaveData = (new ArrayChildren($slaveData))->convertIdByData(GoodsImagesModel::$goodsId_d);
    	
    	foreach( $data as $key => &$value ){
    		
    		if( empty( $slaveData[ $value[$this->splitKey] ] ) ){
    			continue;
    		}
    		$value = array_merge( $slaveData[ $value[$this->splitKey] ], $value);
    	}
    	return $data;
    }
    
    /**
     * 获取从表字段（根据主表数据查从表数据的附属方法）
     * @return array
     */
    protected function getSlaveField () :array{
    	return [
    		GoodsImagesModel::$goodsId_d,
    		GoodsImagesModel::$picUrl_d
    	];
    }
    
    /**
     * 回调方法s
     * @param $where
     */
    protected function parseSlaveWhereAgain( $where) :string
    {
    	return $where .' and '.GoodsImagesModel::$isThumb_d.' = 1';
    }
    
    /**
     * 获取从表生成where条件的字段（根据主表数据查从表数据的附属方法）
     */
    protected function getSlaveColumnByWhere() :string
    {
    	return GoodsImagesModel::$goodsId_d;
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
     */
    public function getModelClassName() :string
    {
        return GoodsImagesModel::class;
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
        ];
    }

     /**
     * 验证
     */
    public function getAttrValidateByLogin()
    {
        $message = [
            'id' => [
                'required' => '商品ID必传',
                'number'   => '商品id必须是数字'
            ],
        ];
        return $message;
    }
    public function getValidateByDetail() {
        $message = [ 
            'goods_id' => [ 
                    'required' => '商品Id参数必须' 
            ],
            'goods_num' => [ 
                    'required' => '商品数量参数必须' 
            ],
        ];
        return $message;
    }
    /**
     * 返回验证数据
     * author 刘嘉强
     */
    public function getValidateByLogin()
    {
        $message = [
            'class_three' => [
                'required' => '必须传入商品分类ID',
            ],
        ];
        return $message;
    }
    public function getValidateByGoods()
    {
        $message = [
            'id' => [
                'required' => '必须传入商品分类ID',
            ]
        ];

        return $message;
    }

    public function getValidateByShop()
    {
        $message = [
            'store_id' => [
                'required' => '必须传入店铺ID',
            ]
        ];

        return $message;
    }

    /**
     * 验证字段
     */
    public function getMessageByImage()
    {
    	return [
    		GoodsImagesModel::$goodsId_d => [
    			'number' => '必须是数字'
    		]
    	];
    }
    
    /**
     * 获取商品图片
     */
    public function getImagesByGoodsId()
    {
    	if (empty($this->data[$this->splitKey])) {
    		$this->errorMessage = '商品图片错误';
    		return [];
    	}
    	
    	$cache = Cache::getInstance('', ['expire' => 60]);
    	
    	$key = $this->data[$this->splitKey].'_goods_images';
    	
    	$data = $cache->get($key);
    	
    	if (!empty($data)) {
    		return $data;
    	}
    	
    	$data = $this->modelObj
    		->field(GoodsImagesModel::$picUrl_d)
    		->where(GoodsImagesModel::$goodsId_d.' = :g_id and '.GoodsImagesModel::$isThumb_d.' = 0 and '.GoodsImagesModel::$status_d.' = 1')
    		->bind([':g_id' => $this->data[$this->splitKey]])
    		->select();
    	if (empty($data)) {
    		return [];
    	}
    	
    	$cache->set($key, $data);
    	
    	return $data;
    }
    
    /**
     * 获取压缩的图片
     */
    public function getThumbImagesByGoodsId() :array
    {
    	if (empty($this->data[$this->splitKey])) {
    		$this->errorMessage = '商品图片错误';
    		return [];
    	}
    	
    	$cache = Cache::getInstance('', ['expire' => 60]);
    	
    	$key = $this->data[$this->splitKey].'_goods_thumb_images';
    	
    	$data = $cache->get($key);
    	
    	if (!empty($data)) {
    		return $data;
    	}
    	
    	$data = $this->modelObj
    		->field(GoodsImagesModel::$picUrl_d)
	    	->where(GoodsImagesModel::$goodsId_d.' = :g_id and '.GoodsImagesModel::$isThumb_d.' = 1 and '.GoodsImagesModel::$status_d.' = 1')
	    	->bind([':g_id' => $this->data[$this->splitKey]])
	    	->find();
    	if (empty($data)) {
    		return [];
    	}
    	
    	$cache->set($key, $data);
    	
    	return $data;
    }
    
    /**
     * 获取商品详情图片
     */
    public function getGoodImage()
    {
        $where['goods_id'] = empty($this->data['p_id'])?$this->data[$this->splitKey]:$this->data['p_id'];
    	$where['is_thumb'] = '0';
    	$field = 'pic_url';
    	$images = $this->modelObj->where($where)->field($field)->limit(4)->select();
    	return $images;
    }
    
    /**
     * 获取图片并缓存
     * @return array
     */
    public function getGoodImageCache() :array
    {
    	$cache = Cache::getInstance('', ['expire' => 60]);
    	
    	$key = $this->data[$this->splitKey].'goods_image_cb';
    	
    	$data = $cache->get($key);
    	
    	if (!empty($data)) {
    		return $data;
    	}
    	
    	$data = $this->getGoodImage();
    	
    	if (empty($data)) {
    		return [];
    	}
    	
    	$cache->set($key, $data);
    	
    	return $data;
    }
}
