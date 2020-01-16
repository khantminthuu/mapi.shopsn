<?php
namespace Common\Model;


/**
 * 模型
 */
class StoreFollowModel extends BaseModel
{

    private static $obj;

	public static $id_d;	//id

	public static $userId_d;	//用户【编号】

	public static $storeId_d;	//商家【编号】

	public static $createTime_d;	//创建时间

	public static $updateTime_d;	//更新时间

    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
    }
    /**
     * 得到店铺的粉丝数量 - 店铺的收藏数量
     *
     */
    public function getFansNumber($id){
        $where['store_id'] = $id;
        $count = $this->where($where)->count();
        return $count;
    }
    /**
     * 验证用户是否关注过店铺
     *
     */
    public function ifAttenStore($uid,$storeId){
        if (empty($uid)) {
            return false;
        }
        $where['user_id'] = $uid;
        $where['store_id'] = $storeId;
        $result = $this->where($where)->field("id")->find();
        if (!empty($result)){
            return true;
        }
        return false;
    }
    /**
     * 获取用户收藏的店铺信息
     *
     */
    public function get_collection_shops($user_id){
        $page = empty($this->data['page'])?0:$this->data['page'];
        $end_time = time();
        $start_time = date("Y-m-d H:i:s",strtotime("-1 month"));
        $where = [
            'update_time' => ['between',[$start_time,$end_time]],
            'user_id'  => $user_id,
        ];
        $field = "store_id";
        $data = $this->where($where)->field($field)->page($page.",10")->order("create_time DESC")->select();
        $count =  $this->where($where)->count();
        $Page = new \Think\Page($count,10);
        $show = $Page->show();
        $totalPages = $Page->totalPages;
        if (empty($data)) {
           return array("status"=>1,"message"=>"暂物数据","data"=>"");
        }
        // 获取店铺的logo和名字
        foreach ($data as $key => $value){
            $store_info = $this->get_store_info($value['store_id']);
            $data[$key]['store_logo'] = $store_info['store_logo'];
            $data[$key]['shop_name'] = $store_info['shop_name'];
        }
        $reData['count'] = $count;
        $reData['totalPages'] = $totalPages;
        $reData['page_size'] = $page_size;
        $reData['goods'] = $data;
        return array("status"=>1,"message"=>"获取成功","data"=>$reData);
    }

    public function get_store_info($store_id){
        $where = [
            'id' => $store_id,
        ];
        $field = 'store_logo,shop_name';
        $data = CommonModel::get_modle('Store')->where($where)->field($field)->find();
        return $data;

    }



}