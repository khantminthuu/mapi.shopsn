<?php
declare(strict_types = 1);
namespace Common\Logic;
use Common\Model\GoodsClassModel;
use Common\Model\GoodsModel;
use Common\Model\UserModel;
use Think\Cache;


/**
 * 商品分类模型
 */
class GoodsClassLogic extends AbstractGetDataLogic
{
    /**
     * 构造方法
     * @param array $data
     */
    public function __construct(array $data = [], $split = '')
    {
        $this->data = $data;
        $this->splitKey = $split;
        $this->modelObj = new GoodsClassModel();
        $this->goodModel = new GoodsModel();
    }
    /**
     * 返回验证数据
     */
    public function getValidateByLogin()
    {
        $message = [
            'fid' => [
                'required' => '一级Id必传',
            ],
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
     * 获取所有的商品分类  获取所有的商品分类
     *
     */
    public function getAllClassees(){
        // 先获取所有的一级分类
        $firstClass = $this->modelObj->getAllClass($this->data['fid']);
        if (empty($firstClass)){
            return false;
        }
        return $firstClass;
    }
    /**
     * 获取所有的一级分类ID
     *
     */
    public function getFirstClassId(){
        // 先获取所有的一级分类
        $firstClassId = $this->modelObj->getAllClassId();
        if (false  === $firstClassId){
            return array("status"=>0,"message"=>"获取失败","data"=>"");
        }
        return array("status"=>1,"message"=>"获取成功","data"=>$firstClassId);
    }
    //获取下级分类
    public function getNextClassId(){
        $where['fid'] = $this->data['fid'];
        $where['hide_status'] = 1;
        $data = $this->modelObj->field("id,class_name")->where($where)->select();
        if (empty($data)) {
            return array("status"=>0,"message"=>"获取失败","data"=>"");
        }
        return array("status"=>1,"message"=>"获取成功","data"=>$data);
    }
    /**
     * 获取店铺的所有商品分类
     *
     */
    public function getShopGoodsClass(){

        //根据商铺Id获取所有的一级分类
        $firstClassId = $this->modelObj->getAllClassId($this->data['store_id']);
        $all = $this->modelObj->getStoreAllClass($firstClassId,$this->data['store_id']);
        return $all;
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
        return BrandModel::class;
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
     * 店铺热门分类
     *
     */
    public function getHotCalssGoods(){
        $store_id = $this->data['store_id'];
        $catch_name = $store_id . "Hot_class".$store_id;
        // 检查缓存中时候有商品信息 如果没有则进行查询如果有的话则进行提取
        
        $cache = Cache::getInstance('', ['expire' => 60]);
        
        $data = $cache->get($catch_name);
        
        if (!empty($data)) {
            return array("status"=>1,"message"=>"获取成功","data"=>$data);
        }
        
        $where['store_id']= $store_id;
        $where['class_three']= array("NEQ",0);
        $where['sales_sum']= array("NEQ",0);
        $reData = $this->goodModel->field("class_three")->where($where)->group("class_three")->order("sales_sum DESC")->select();
        if(empty($reData)){
            return array("status"=>0,"message"=>"暂无分类","data"=>$data);
        }

        foreach ($reData as $key => $value) {
            $class = $this->modelObj->field("id,class_name")->where(['id'=>$value['class_three']])->find();
            $reData[$key]['id'] = $class['id'];
            $reData[$key]['class_name'] = $class['class_name'];
        }
        $cache->set($catch_name, $reData);
        return  array("status"=>1,"message"=>"获取成功","data"=>$reData);;
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
     * 分类必须是一推荐的
     */
    public function getClassByPage()
    {
    	$cache = Cache::getInstance('', ['expire' => 90]);
    	
    	$key = 'goods_class_page'.'_'.$this->data['page'];
    	
    	$data = $cache->get($key);
    	
    	if (!empty($data)) {
    		return $data;
    	}
    	
    	$field = GoodsClassModel::$id_d.','.GoodsClassModel::$className_d.','.GoodsClassModel::$picUrl_d;
    	
    	$data = $this->modelObj
	    	->field($field)
	    	->where(GoodsClassModel::$fid_d.' = 0 and '.GoodsClassModel::$hideStatus_d.' = 1 and '. GoodsClassModel::$shoutui_d.' = 1')
	    	->order(GoodsClassModel::$sortNum_d.' DESC ')
	    	->page($this->data['page'], 1)
	    	->find();
    	if (empty($data)) {
    		return [];
    	}
    	
    	$cache->set($key, $data);
    	
    	return $data;
    }
    
    /**
     * 根据绑定的分类获取分类id字符串
     */
    public function getGoodsClassIdStringByBindClass() :string
    {
    	$idString = [];
    	
    	foreach ($this->data as $key => $value) {
    		$idString[] = $value['class_one'];
    		$idString[] = $value['class_two'];
    		$idString[] = $value['class_three'];
    	}
    	
    	$id = implode(',', array_unique($idString));
    	
    	return $id;
    }
    /**
     * 根据绑定的分类获取分类数据
     */
    public function getGoodsClassByBindClass() :array
    {
    	$id = $this->getGoodsClassIdStringByBindClass();
    	
    	$data = $this->modelObj->where(GoodsClassModel::$id_d .' in (%s)', $id)->getField(GoodsClassModel::$id_d.','.GoodsClassModel::$className_d);
    	
    	if (empty($data)) {
    		return [];
    	}
    	
    	$bindClassData = $this->data;
    	
    	foreach ($bindClassData as $key => & $value) {
    		
    		if (isset($data[$value['class_one']])) {
    			$value['one_name'] = $data[$value['class_one']];
    		}
    		
    		if (isset($data[$value['class_two']])) {
    			$value['two_name'] = $data[$value['class_two']];
    		}
    		
    		if (isset($data[$value['class_three']])) {
    			$value['three_name'] = $data[$value['class_three']];
    		}
    	}
    	
    	return $bindClassData;
    }
}
