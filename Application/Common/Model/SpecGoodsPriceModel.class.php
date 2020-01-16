<?php
namespace Common\Model;
use Common\Model\GoodsSpecItemModel;
use Common\Model\GoodsSpecModel;


/**
 * 商品规格属性模型
 */
class SpecGoodsPriceModel extends BaseModel
{

    private static $obj;

	public static $id_d;	//id

	public static $goodsId_d;	//商品id

	public static $key_d;	//规格键名

	public static $barCode_d;	//商品条形码

	public static $sku_d;	//SKU
    

	public static $pId_d;	//商品父级【编号】


    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
    }
    public function getGoodSpe($goodId){
        $goodSpeItem = new GoodsSpecItemModel();
        $goodsSpec = new GoodsSpecModel();
        $where['goods_id'] = $goodId;
        $speItem = $this->where($where)->field("key")->find()['key'];
        $speItemArray = explode('_',$speItem);
        $result = $goodSpeItem->getspe($speItemArray);
        
        foreach ($result as $key =>$value){
           $speName = $goodsSpec->getSpe($value['spec_id']);
           $result[$key]["speName"] = $speName;
           unset($result[$key]['spec_id']);
        }
        return $result;

    }

    public function getGoodSpec($goodId){
        $goodSpeItem = new GoodsSpecItemModel();
        $goodsSpec = new GoodsSpecModel();
        $where['goods_id'] = $goodId;
        $speItem = $this->where($where)->getField('key');
        $result = $goodSpeItem->getspec($speItem);
        $speName = $goodsSpec->getSpe($result['spec_id']);
        $result["speName"] = $speName;
        unset($result['spec_id']);
        
        return $result;
    }

    public function getGoodKey($goodId){
        $where['goods_id'] = $goodId;
        return $this->where($where)->field("key")->find()['key'];
    }
}