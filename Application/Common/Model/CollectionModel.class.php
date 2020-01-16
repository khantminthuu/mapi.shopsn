<?php
namespace Common\Model;


/**
 * 收藏列表模型
 */
class CollectionModel extends BaseModel
{

    private static $obj;

	public static $id_d;	//主键id

	public static $goodsId_d;	//收藏的商品id

	public static $userId_d;	//收藏者id

	public static $addTime_d;	//收藏时间

	public static $status_d;	//0普通商品1降价商品

    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
    }

    /**
     * 用户收藏商品
     *
     */
    public function addCollection($data,$userId){
        if ($data['type'] == 1){
            $collection['goods_id'] = $data['goods_id'];
            $collection['user_id'] = $userId;
            $collection['add_time'] = time();
            $collection['status']   = 1;
            $result = $this->add($collection);
        }
        if($data['type'] == 2)
        {
            $where['goods_id'] = $data['goods_id'];
            $where['user_id'] = $userId;
            $result = $this->where($where)->delete();
        }
        if (!$result) {
            return array("status"=>0,"message"=>"操作失败","data"=>"");
        }
        return array("status"=>1,"message"=>"操作成功","data"=>$result);
    }
    //删除收藏商品
    public function delete_collect_good($user_id,$good_id){
        $where = [
            'user_id' => $user_id,
            'id' => $good_id,
        ];
        $result = $this->where($where)->delete();
        return $result;
    }
    //获取用户收藏商品
    public function getCollection($where,$field){
        $data = $this->field($field)->where($where)->order("add_time DESC")->select();
        return $data; 
    }


}