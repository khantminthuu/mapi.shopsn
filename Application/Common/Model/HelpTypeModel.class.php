<?php
namespace Common\Model;


/**
 * 模型
 */
class HelpTypeModel extends BaseModel
{


	public static $id_d;	//类型ID

	public static $name_d;	//类型名称

	public static $sort_d;	//排序

	public static $helpCode_d;	//调用编号【auto的可删除】

	public static $status_d;	//是否显示【0为否,1为是,默认为1】

	public static $pageShow_d;	//页面类型【0为店铺,1为会员】

	public static $updateTime_d;	//更新时间

	public static $createTime_d;	//创建时间

	public static $pId_d;	//父级编号

    private static $obj;
    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
    }
}