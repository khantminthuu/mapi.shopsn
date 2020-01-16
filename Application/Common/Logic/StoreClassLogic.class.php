<?php
namespace Common\Logic;
use Common\Model\UserModel;
use Common\Model\StoreClassModel;
/**
 * 逻辑处理层
 *
 */
class StoreClassLogic extends AbstractGetDataLogic
{
    /**
     * 构造方法
     * @param array $data
     */
    public function __construct(array $data = [], $split = '')
    {
        $this->data = $data;
        $this->splitKey = $split;
        $this->modelObj = new StoreClassModel();
      
    }
    /**
     * 返回验证数据
     */
    public function getValidateByLogin()
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
//        return BrandModel::class;
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
//        return [
//            UserModel::$userName_d,
//        ];
    }
    public function get_store_class(){
        $data = $this->modelObj->get_store_class();
        return $data;
    }

   
}
