<?php
namespace Common\Model;
/**
 * @name 消息模型层
 * 
 * @des 消息模型层
 * @updated 2017-12-22 20:31
 */
class NewsModel extends BaseModel
{

	public static $id_d;	//消息表

	public static $newsInfo_d;	//消息详情

	public static $createTime_d;	//时间

	public static $theme_d;	//消息主题

	public static $userId_d;	//用户id


	public static $status_d;	//0消息未读，1消息已读

    private static $obj;
    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
    }
}