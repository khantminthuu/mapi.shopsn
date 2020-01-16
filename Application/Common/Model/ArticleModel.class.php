<?php
namespace Common\Model;
/**
 * @name 文章模型层
 * 
 * @des 文章模型层
 * @updated 2018-01-05 12:30
 */
class ArticleModel extends BaseModel
{
	public static $id_d;	//文章ID

	public static $name_d;	//文章标题

	public static $adminName_d;	//管理员名字

	public static $articleCategory_id_d;	//文章分类id

	public static $intro_d;	//文章详情

	public static $status_d;	//显示状态

	public static $sort_d;	//排序

	public static $createTime_d;	//录入时间

	public static $picUrl_d;	//图片

    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
    }

}