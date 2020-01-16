<?php
namespace Common\Model;


/**
 * 商品规格模型
 */
class GoodsSpecItemModel extends BaseModel
{

    private static $obj;

	public static $id_d;	//规格项id

	public static $specId_d;	//规格id

	public static $item_d;	//规格项

	public static $storeId_d;	//店铺【编号】


    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
    }
    public function getGoodItem($specId){
        $where['id'] = $specId;
        $field = 'item';
        return $this->where($where)->field($field)->find()['item'];
    }

    public function getspe($specIds){
        $where['id']=array('in',$specIds);
        $spes = $this->where($where)->field('item,spec_id')->select();
        return $spes;

    }
     public function getspec($specIds){
        $where['id'] = $specIds;
        $spes = $this->where($where)->field('item,spec_id')->find();
        return $spes;

    }


}