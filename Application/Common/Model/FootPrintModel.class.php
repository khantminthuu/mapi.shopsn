<?php
namespace Common\Model;


/**
 * 模型
 */
class FootPrintModel extends BaseModel
{

    private static $obj;

	public static $id_d;	//足迹表

	public static $uid_d;	//用户id

	public static $gid_d;	//商品id


	public static $createTime_d;	//时间

	public static $goodsPic_d;	//商品图片

	public static $goodsName_d;	//商品名字

	public static $isType_d;	//1: 商品   2：旅游  3：合伙人   4：会员

	public static $classId_d;	//分类id


    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
    }

    public function getLoveGoods($uid){
        $where['uid'] = (int)$uid;//用户id
        $field = 'gid as id,goods_name as title,goods_pic as pic_url,goods_price as price_market';
        $loveGoods = $this
                    ->where($where)
                    ->field($field)
                    ->order('rand()')
                    ->limit(3)
                    ->select();
        if (!empty($loveGoods)) {
            foreach ($loveGoods as $key => $value) {
                $p_id = M("Goods")->where(['id'=>$value['id']])->getField("p_id");
                $loveGoods[$key]['pic_url']  = M("GoodsImages")->where(['goods_id'=>$p_id,"is_thumb"=>1])->getField("pic_url");
            }
        }
        return $loveGoods;
    }
//获取用户足迹
    public function getFootPrint($user_id){
        $where['uid'] = $user_id;//用户id
        $field = 'gid as Goods_id,create_time';
        $loveGoods = $this->where($where)->field($field)->order("create_time DESC")->select();
        return $loveGoods;
    }
   //清除用户足迹
    public function delFootPrint($user_id){
        $where['uid'] = $user_id;//用户i
        $res = $this->where($where)->delete();
        if (!$res) {
            return array("status"=>0,"message"=>"清除失败","data"=>"");
        }
        return array("status"=>1,"message"=>"清除成功","data"=>"");
    }
}