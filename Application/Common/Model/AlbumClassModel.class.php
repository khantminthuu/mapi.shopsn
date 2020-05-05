<?php
namespace Common\Model;


/**
 * 模型
 */
class AlbumClassModel extends BaseModel
{

    private static $obj;

	public static $id_d;	//相册id

	public static $albName_d;	//相册名称

	public static $albDes_d;	//相册描述

	public static $albSort_d;	//排序

	public static $albCover_d;	//相册封面

	public static $storeId_d;	//商家编号

	public static $createTime_d;	//创建时间


	public static $isDefault_d;	//是否默认【0否1是】


    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
    }

    public function getStoreBaner($storeId){
        $where['store_id'] = $storeId;
        $field = "alb_cover";
        return $this->field($field)->where($where)->select();
    }
    
}
