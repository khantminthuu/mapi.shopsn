<?php
namespace Common\Logic;
use Common\Model\StoreInformationModel;
use Common\Model\CommonModel;
use Think\SessionGet;
/**
 * 逻辑处理层
 *
 */
class StoreInformationLogic extends AbstractGetDataLogic
{
    /**
     * 构造方法
     * @param array $data
     */
    public function __construct(array $data = [], $split = '')
    {
        $this->data = $data;
        $this->splitKey = $split;
        $this->modelObj = new StoreInformationModel();
      
    }
    /**
     * 返回验证数据
     */
    public function getValidateByLogin() :array
    {
        $message = [
            'shop_account' => [
                'required' => '商家账号必填',
            ],
            'level_id' => [
                'required' => '商家等级必填',
            ],
            'shop_long' => [
                'required' => '开店时长必填',
            ],
            'shop_class' => [
                'required' => '店铺分类ID必填',
            ],
            'sc_bail' => [
                'required' => '店铺分类保证金必填',
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
        return StoreInformationModel::class;
    }


    public function perfect_company_info(){
    	
    	$companyObj = SessionGet::getInstance('add_join_company_id');
    	
    	$companyId = $companyObj->get();
    	
    	if (empty($companyId)) { 
    		$this->errorMessage = '店铺异常';
    		return false;
    	}
    	
    	$storeObj = SessionGet::getInstance('store_name');
    	
        //用户只能完善自己的店铺信息
    	$data[StoreInformationModel::$storeId_d] = $companyId;
        $data[StoreInformationModel::$shopClass_d] = $this->data[StoreInformationModel::$shopClass_d];
        // 获取店铺名称 
        $data[StoreInformationModel::$shopName_d] = $storeObj->get();
        $data[StoreInformationModel::$shopAccount_d] = $this->data[StoreInformationModel::$shopAccount_d];
        $data[StoreInformationModel::$levelId_d] = $this->data[StoreInformationModel::$levelId_d];
        $data[StoreInformationModel::$shopLong_d] = $this->data[StoreInformationModel::$shopLong_d];
        $data[StoreInformationModel::$scBail_d] = $this->data[StoreInformationModel::$scBail_d];
        $data[StoreInformationModel::$status_d] = SessionGet::getInstance('store_type')->get();
        M()->startTrans();
        $result_data = $this->modelObj->add($data);
        if (!$result_data ){
            M()->rollback();
            return array("status"=>0,"message"=>"失败","data"=>0); 
        }
        
        $typeObj = SessionGet::getInstance('store_type');
        
        // 添加店铺经营类目信息
        $category_status = CommonModel::get_modle("StoreManagementCategory")->add_management_category($companyId, $this->data['class'], $typeObj->get());
        if (!$category_status){ 
            M()->rollback();
            return array("status"=>0,"message"=>"失败","data"=>0); 
        }
        M()->commit();
        
        $companyObj->delete();
        $storeObj->delete();
        $typeObj->delete();
        
        return array("status"=>1,"message"=>"成功","data"=>0); 
    }
	
    /**
     * 获取店铺信息
     */
    public function getStoreInfoByStore()
    {
    	$storeDataByPerson = SessionGet::getInstance('store_data_by_person')->get();
    	
    	if (empty($storeDataByPerson['id']) || $storeDataByPerson['id'] != $this->data['id']) {
    		return [];
    	}
    	$data = $this->modelObj->field(array_values($this->getStaticProperties()))->where(StoreInformationModel::$storeId_d.'=:s_id and '.StoreInformationModel::$status_d.' = :status')
    	->bind([':s_id' => $this->data['id'], 'status' => $storeDataByPerson['type']])
    		->find();
    	return $data;
    }
   
}
