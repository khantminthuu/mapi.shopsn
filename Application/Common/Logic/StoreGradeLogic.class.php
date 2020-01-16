<?php
namespace Common\Logic;
use Common\Model\UserModel;
use Common\Model\StoreGradeModel;
use Think\SessionGet;
/**
 * 逻辑处理层
 *
 */
class StoreGradeLogic extends AbstractGetDataLogic
{
    /**
     * 构造方法
     * @param array $data
     */
    public function __construct(array $data = [], $split = '')
    {
        $this->data = $data;
        $this->splitKey = $split;
        $this->modelObj = new StoreGradeModel();
      
    }
    /**
     * 返回验证数据
     */
    public function getValidateByLogin()
    {
//        $message = [
//            'page' => [
//                'required' => '必须输入分页信息',
//            ],
//        ];
//        return $message;
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
//            UserModel::$userName_d,
        ];
    }
    /***
     * 获取所有的店铺等级
     */
    public function shop_level_list(){
        $store_level_list = $this->modelObj->get_all_shop_level_list();
        return $store_level_list;
    }
   	
    /**
     * 获取店铺等级
     */
    public function getStoreGrade()
    {
    	$data = $this->modelObj->field(array_values($this->getStaticProperties()))->where(StoreGradeModel::$id_d.'=:id')
    		->bind([':id' => $this->data['level_id']])
    		->find();
    	if (empty($data)) {
    		return [];
    	}
    	SessionGet::getInstance('money', floatval($data[StoreGradeModel::$price_d]))->set();
    	return $data;
    }
}
