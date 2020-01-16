<?php
declare(strict_types = 1);
namespace Common\Model;

/**
 * 用户模型
 * @author Administrator
 *
 */
class UserDataModel extends BaseModel
{
	private static $obj;

	public static $id_d;	//编号

	public static $userId_d;	//用户编号

	public static $currentIntegral_d;	//当前积分

	public static $alreadyIntegral_d;	//已使用积分

	public static $createTime_d;	//创建时间

	public static $updateTime_d;	//更新时间


	public static $beOverdue_d;	//过期积分

	
	public static function getInitnation()
	{
		$class = __CLASS__;
		return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
	}
}