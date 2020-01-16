<?php
namespace Common\Model;

/**
 * 
 * @author Administrator
 *
 */
class GoodsConsultationModel extends BaseModel
{
	private static $obj;

	public static $id_d;	//商品咨询id

	public static $goodsId_d;	//商品名称编号

	public static $createTime_d;	//咨询时间

	public static $commentType_d;	//0 商品咨询 1 支付咨询 2 配送 3 售后

	public static $content_d;	//咨询内容

	public static $isShow_d;	//是否显示

	public static $userId_d;	//用户名编号

	public static $ip_d;	//ip地址

	
	public static function getInitnation()
	{
		$class = __CLASS__;
		return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
	}
}