<?php
namespace Common\Model;


/**
 * 积分规则模型
 */
class UserLevelModel extends BaseModel
{
	private static $obj;
	
	public static $id_d;	//id
	
	public static $levelName_d;	//等级名称
	
	public static $integralSmall_d;	//积分下限
	
	public static $integralBig_d;	//积分上限
	
	public static $discountRate_d;	//折扣率
	
	public static $status_d;	//会员等级状态 1 使用 0弃用
	
	public static $description_d;	//等级描述
	
    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
    }
}
