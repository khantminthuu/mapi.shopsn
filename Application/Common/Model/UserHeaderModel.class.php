<?php
namespace Common\Model;


/**
 * 用户头像模型
 */
class UserHeaderModel extends BaseModel
{
    private static $obj;


	public static $id_d;	//id

	public static $userId_d;	//User Id

	public static $userHeader_d;	//Avatar


    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
    }
}
