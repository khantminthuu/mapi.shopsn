<?php
namespace Common\Model;


/**
 * 模型
 */
class StoreClassModel extends BaseModel
{

    private static $obj;

	public static $id_d;	//id编号

	public static $scName_d;	//分类名称

	public static $scBail_d;	//保证金数额

	public static $scSort_d;	//排序

	public static $status_d;	//是否启用【0关闭 1开启】

	public static $createTime_d;	//创建时间

	public static $updateTime_d;	//更新时间

    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
    }
    public function get_store_class(){
        $where['status'] = 1;
        $field = "id,sc_name,sc_bail";
        $data = $this->where($where)->field($field)->select();
        if (empty($data)) {
            return array("status"=>0,"message"=>"暂无数据","data"=>"");
        }
        return array("status"=>1,"message"=>"获取成功","data"=>$data);
    }



}