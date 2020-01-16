<?php
namespace Common\Model;


/**
 * 抢购模型
 */
class GoodsRestrictionsModel extends BaseModel
{

    private static $obj;

	public static $id_d;	//

	public static $goodsId_d;	//商品编号

	public static $restrictionsStatus_d;	//1 已经开启，0未开启

	public static $restrictionsStart_d;	//限购开始时间

	public static $restrictionsOver_d;	//限购结束时间

    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
    }


    /**
     * 得到所有 的抢购商品包括首页广告图  商品图片 商品标题 活动抢购状态  抢购价格 原价 购买人数   设置提醒人数
     *
     */
    public function getShopsInfo(){
        $where['restrictions_status'] = '1';
        $field = 'goods_id,restrictions_start,restrictions_over';
        $goodList = $this->where($where)->field($field)->select();
        return $goodList;
    }
    /**
     *  商品活动限购表 - 获取店铺的限时秒杀商品
     *
     */
    public function getStoreActivityGoods($id){
        $where = [
            'store_id' => $id,
            'restrictions_status' => 1
        ];
        $field = 'goods_id,restrictions_start,restrictions_over';
        $data = $this->where($where)->field($field)->select();
        return $data;
    }



}