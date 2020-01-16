<?php
namespace Common\Model;


/**
 * 模型
 */
class StoreHelpModel extends BaseModel
{

    private static $obj;

	public static $id_d;	//id

	public static $title_d;	//标题

	public static $sort_d;	//排序

	public static $status_d;	//是否显示【0为否,1为是,默认为1】

	public static $info_d;	//帮助内容

	public static $helpUrl_d;	//跳转链接

	public static $updateTime_d;	//更新时间

	public static $typeId_d;	//帮助类型

	public static $createTime_d;	//创建时间

    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
    }



}