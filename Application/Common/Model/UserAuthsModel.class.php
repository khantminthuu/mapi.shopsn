<?php
namespace Common\Model;

/**
 * 
 * @author Administrator
 *
 */
class UserAuthsModel extends BaseModel
{
	private static $obj;
	

	public static $id_d;	//id

	public static $userId_d;	//用户id

	public static $identityType_d;	//登录类型:0.支付宝登陆,1.qq登录2.微信登录,3.微博登录

	public static $identifier_d;	//账户:如果是第三方登陆就是第三方用户唯一标识

	public static $credential_d;	//密码:如果是第三方登录就是第三方的access_tooken

	public static $expiresIn_d;	//第三方登录时的超期时间,本网站注册用户即为0

	public static $updateAt_d;	//更新密码的时间

	public static $createAt_d;	//创建时间

	public static $local_d;	//账户登录类型:1.本地登录 0.三方登陆

	public static $refreshToken_d;	//授权更新

	public static $unionid_d;	//微商城【打通一个企业对应多个公众号】
	
	public static function getInitnation()
	{
		$class = __CLASS__;
		return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
	}
}