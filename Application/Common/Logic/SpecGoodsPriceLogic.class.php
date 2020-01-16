<?php
namespace Common\Logic;
use Common\Model\SpecGoodsPriceModel;
use Common\Model\UserModel;
/**
 * 逻辑处理层
 *
 */
class SpecGoodsPriceLogic extends AbstractGetDataLogic
{
    /**
     * 构造方法
     * @param array $data
     */
    public function __construct(array $data = [], $split = '')
    {
        $this->data = $data;
        
        $this->splitKey = $split;
        
        $this->modelObj = new SpecGoodsPriceModel();
    }

    
     /**
     * 获取商品规格数据 
     * @return array
     */
    public function getSpecByGoods ()
    { 
        if (empty($this->data)) {
            return array();
        }
        
        $field = array(
            SpecGoodsPriceModel::$id_d,
            SpecGoodsPriceModel::$goodsId_d,
            SpecGoodsPriceModel::$key_d,
            SpecGoodsPriceModel::$sku_d
        );
        $data = $this->getDataByOtherModel($field, SpecGoodsPriceModel::$goodsId_d);
        return $data;
    }
    /**
     * 获取 规格相关字段
     */
    public function getSplitKeyByGoods()
    {
        return SpecGoodsPriceModel::$key_d;
    }


    /**
     * 
     */
    public function getResult()
    {

    }

    public function getModelClassName() :string
    {
        return SpecGoodsPriceModel::class;
    }

}