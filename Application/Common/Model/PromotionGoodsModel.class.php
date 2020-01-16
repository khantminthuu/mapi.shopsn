<?php
namespace Common\Model;


/**
 * 模型
 */
class PromotionGoodsModel extends BaseModel
{

    private static $obj;

	public static $id_d;	//

	public static $promId_d;	//促销编号

	public static $goodsId_d;	//商品编号

	public static $startTime_d;	//促销开始时间

	public static $endTime_d;	//促销结束时间

	public static $activityPrice_d;	//促销价格

    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
    }
    /**
     * 获取商品的促销活动
     * author 刘嘉强
     */
    public function getGoodPromotion($good_id){
        $promotion = M('promotion_goods')
            ->join('db_prom_goods ON db_prom_goods.id=db_promotion_goods.prom_id')
            ->where('status=1 AND db_promotion_goods.goods_id='.$good_id)
            ->select();
        return $promotion;
    }


}