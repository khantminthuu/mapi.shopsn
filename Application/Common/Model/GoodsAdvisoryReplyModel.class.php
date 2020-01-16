<?php
namespace Common\Model;

/**
 * 咨询模型
 * @author Administrator
 */
class GoodsAdvisoryReplyModel extends BaseModel
{
	private static $obj;

	public static $id_d;	//主键id

	public static $userId_d;	//回复人id

	public static $content_d;	//回复内容

	public static $createTime_d;	//回复时间

	public static $status_d;	//状态 0 隐藏 1 显示

	public static $consultationId_d;	//咨询编号d

	public static $type_d;	//回答类型 【0买家回答 1商户回答】

	
	public static function getInitnation()
	{
		$class = __CLASS__;
		return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
	}
}