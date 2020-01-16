<?php
namespace Common\Model;


/**
 * 导航表逻辑处理
 */
class AnnouncementModel extends BaseModel
{


	public static $id_d;	//公告id

	public static $title_d;	//公告标题

	public static $adminAccount_d;	//作者

	public static $intro_d;	//公告简介

	public static $content_d;	//公告内容

	public static $createTime_d;	//公告创建时间

	public static $updateTime_d;	//公告最后一次编辑时间

	public static $type_d;	//公告类型 默认：0不选 1新

	public static $status_d;	//显示状态 0不显示  默认：1显示

	public static $sort_d;	//排序 默认:50

	public static $page_d;	//排序 默认:50

    private static $obj;
    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
    }
}