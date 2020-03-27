<?php
namespace Common\Logic;
use Common\Model\UserModel;
use Common\Model\GoodsAttrModel;
use Common\Model\GoodsAttributeModel;
/**
 * 逻辑处理层
 *
 */
class GoodsAttrLogic extends AbstractGetDataLogic
{
    /**
     * 构造方法
     * @param array $data
     */
    public function __construct(array $data = [], $split = '')
    {
        $this->data = $data;
        $this->splitKey = $split;
        $this->modelObj = new GoodsAttrModel();
        $this->goodsAttributeModel = new GoodsAttributeModel();

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
        return GoodsAttrModel::class;
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

    public function getValidateByLogin(){
        $message = [
            'goods_id' => [
                'required' => '商品ID必传',
            ],
        ];
        return $message;
    }

    public function getGoodsAttrs()
    {
        $this->searchTemporary = [
            GoodsAttrModel::$goodsId_d => $this->data[GoodsAttrModel::$goodsId_d],
        ];
        $this->searchField = 'id,attribute_id,attr_value';
        $reData = parent::getDataList();
        foreach ($reData['records'] as $key=>$value){
            $name = $this->goodsAttributeModel->getAttrName($value['attribute_id']);
            $reData['records'][$key]['attrName'] = $name;
        }
        return $reData;
    }
    
   
}
