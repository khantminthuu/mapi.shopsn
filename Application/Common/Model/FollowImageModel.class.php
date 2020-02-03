<?php
namespace Common\Model;
class FollowImageModel extends BaseModel
{
    private static  $obj;

	public static $id_d;	//

	public static $userId_d;	//

	public static $fId_d;	//

	public static $status_d;	//
   
   public static function getInitnation()
   {
       $class = __CLASS__;
       return self::$obj = !( self::$obj instanceof $class) ? new self() : self::$obj;
   }
   public function getFollow($id)
   {
       $where['user_id'] = $id;
       $where['status'] = 1;
       $getFollow = $this->where($where)->field('user_id,f_id')->select();
       foreach ($getFollow as $value)
       {
           $arr[] = $value['user_id'];
       }
       $follow = count($arr);
       $follower = $this->where(['f_id'=>$id,'status'=>1])->count();
       return  $arr = array(
           'follow' => $follow,
           'follower' => $follower
       );
   }
   
}
