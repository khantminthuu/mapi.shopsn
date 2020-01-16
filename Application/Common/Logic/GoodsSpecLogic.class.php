<?php
namespace Common\Logic;
use Common\Model\GoodsSpecModel;
use Common\Model\GoodsSpecItemModel;
use Common\Model\UserModel;
use Common\Tool\Tool;
use Common\Tool\Extend\ArrayChildren;
/**
 * 逻辑处理层
 *
 */
class GoodsSpecLogic extends AbstractGetDataLogic
{
    /**
     * 构造方法
     * @param array $data
     */
    public function __construct(array $data = [], $split = '')
    {
        $this->data = $data;
        $this->splitKey = $split;
        $this->modelObj = new GoodsSpecModel();
        $this->goodsSpecItemModel = new GoodsSpecItemModel();

    }
    /**
     * 返回验证数据
     */
    public function getValidateByLogin()
    {
        $message = [
            'type_id' => [
                'required' => '商品类型必传',
            ],
            'store_id' => [
                'required' => '店铺Id必传',
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
        return GoodsSpecLogic::class;
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
     * 获取商品规格
     *
     */
    public function getGoodSpec(){

        $goodsSpe = $this->modelObj->goodsSpec($this->data[GoodsSpecModel::$typeId_d]);
        return $goodsSpe;
    }
    /**
     * 获取商品规格组名称
     * @author 王强
     */
    public function getGoodSpecial()
    {
        $idString = Tool::characterJoin($this->data, $this->splitKey);
        $field = [GoodsSpecModel::$id_d, GoodsSpecModel::$name_d];

        $data = $this->modelObj->field($field)->where(GoodsSpecModel::$id_d.' in (%s)', $idString)->select();
        if (empty($data)) {
            return [];
        }

        $data = (new ArrayChildren($data))->convertIdByData(GoodsSpecModel::$id_d);

        $slaveData = $this->data; 
        
//         foreach( $slaveData as $key => &$value ){
 
//             if( empty( $data[ $value[$this->splitKey] ] ) ){
//                 continue;
//             }
//             unset($data[$value[$this->splitKey]][GoodsSpecModel::$id_d]);
//             $value = array_merge( $value, $data[ $value[$this->splitKey] ]);
//         }
      
        return ['spec_group' => $data, 'spec_children' => $slaveData];
    }

   
}
