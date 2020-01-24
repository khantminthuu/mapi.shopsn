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

    public static function getUserDetail($arr)
    {
    	foreach ($arr as $key => $value) {
    		$user = M('user')->where(['id'=>$value['user_id']])->field('user_name')->find();
    		$userDeatil = M('user_header')->where(['user_id'=>$value['user_id']])->field('user_header')->find();
    		$img = M('review_images')->where(['user_id'=>$value['user_id']])->field('img_url')->select();
    		$arr[$key]['username'] = $user['user_name'];
    		$arr[$key]['userheader'] = $userDeatil['user_header'];
    		$arr[$key]['img'] = $img;
    	}
    	return $arr;
    	
    }
    
}
