<?php
declare(strict_types=1);
namespace Common\Logic;
use Common\Model\StoreAddressModel;
/**
 * 逻辑处理层
 */
class StoreAddressLogic extends AbstractGetDataLogic
{
    /**
     * 构造方法
     * @param array $data
     */
    public function __construct(array $data = [], $split = '')
    {
        $this->data = $data;
        $this->splitKey = $split;
        $this->modelObj = new StoreAddressModel();
      
    }
    /**
     * 返回验证数据
     */
    public function getValidateByLogin() :array
    {
        $message = [
            'page' => [
                'required' => '必须输入分页信息',
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
        return StoreAddressModel::class;
    }

	
    /**
     * 添加店铺地址
     * @return boolean
     */
    public function addAddressStore() :bool{
        $result = $this->addData();
        
        if (!$this->traceStation($result)) {
        	$this->errorMessage .= '、保存店铺地址失败';
        	return false;
        }
        
        $this->modelObj->commit();
        
        return true;
    }
}
