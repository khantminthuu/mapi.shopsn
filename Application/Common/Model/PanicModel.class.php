<?php
namespace Common\Model;


/**
 * 商品抢购表
 */
class PanicModel extends BaseModel
{

    private static $obj;

	public static $id_d;	//抢购编号

	public static $panicTitle_d;	//抢购标题

	public static $panicPrice_d;	//抢购价格

	public static $goodsId_d;	//商品编号

	public static $panicNum_d;	//参加抢购数量

	public static $quantityLimit_d;	//限购数量

	public static $alreadyNum_d;	//已购买

	public static $startTime_d;	//开始时间

	public static $endTime_d;	//结束时间

	public static $status_d;	//审核状态【0拒绝，1通过，2审核中】

	public static $storeId_d;	//店铺【编号】

	public static $createTime_d;	//创建时间

	public static $updateTime_d;	//更新时间

    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
    }

    public function getStoreActivityGoods($id){

        $where  = [
            'store_id' => $id,
            'status'   => 1,
            'end_time' => ['GT',time()],// 活动的时间必须大于当前时间
        ];
        $field = 'panic_title,panic_price,goods_id,panic_num,already_num,FROM_UNIXTIME(start_time,\'%Y-%m-%d\') as start_time,FROM_UNIXTIME(end_time,\'%Y-%m-%d\') as end_time';
        $data = $this->where($where)->field($field)->select();
        foreach ($data as $key => $value){
            $good_info = CommonModel::good_model()->getGoodTitle($value['goods_id']);
            $data[$key]['title']    = $good_info['title'];
            $data[$key]['store_id'] = $good_info['store_id'];
            $data[$key]['goods_id'] = $good_info['goods_id'];
            $data[$key]['image']    = $good_info['image'];
        }
        return $data;
    }



}