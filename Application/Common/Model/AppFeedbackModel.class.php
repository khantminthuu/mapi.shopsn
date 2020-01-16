<?php
namespace Common\Model;
/**
 * @name 意见反馈模型层
 * 
 * @des 意见反馈模型层
 * @updated 2017-12-22 19:42
 */
class AppFeedbackModel extends BaseModel
{
	public static $feedbackId_d;	//id

	public static $type_d;	//反馈类型(1:功能意见  2:页面意见  3:你的新需求  4:操作意见  5:流量问题   6:其他)

	public static $tel_d;	//联系方式

	public static $content_d;	//反馈内容

	public static $userId_d;	//用户id

	public static $createTime_d;	//创建时间

    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
    }
	
}