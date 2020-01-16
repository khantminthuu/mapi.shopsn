<?php
namespace Common\Model;


/**
 * 模型
 */
class StoreEvaluateModel extends BaseModel
{

    private static $obj;

	public static $id_d;	//评价ID

	public static $orderId_d;	//订单ID

	public static $createTime_d;	//评价时间

	public static $storeId_d;	//店铺编号

	public static $memberId_d;	//买家编号

	public static $desccredit_d;	//描述相符评分

	public static $servicecredit_d;	//服务态度评分

	public static $deliverycredit_d;	//发货速度评分

    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
    }

    /**
     * 得到店铺的评分数据
     *
     */
    public function getDesccredit($id,$str){
        $sums = $this->getSum($id,$str);
        $counts= $this->getCount($id);
        if ($counts == 0) {
            return 0;
        }
        return round($sums/$counts,2);
    }
    /**
     * 得到店铺的评分总分数
     *
     */
    public function getSum($id,$str){
        $where['store_id'] = $id;
        $sums =  $this->where($where)->sum($str);
        return $sums;
    }
    /**
     * 得到店铺的评分总条数
     *
     */
    public function getCount($id){
        $where['store_id'] = $id;
        return $this->where($where)->count();
    }
    /**
     * 得到店铺的评分总条数
     *
     */
    public function getCountAll(){
        
        return $this->count();
    }
    /**
     * 得到店铺的评分总条数
     *
     */
    public function getDesccreditAll($str){
        $sums = $this->sum($str);
        $counts= $this->getCountAll();
        if ($counts == 0) {
            return 0;
        }
        return round($sums/$counts,2);
    }

}