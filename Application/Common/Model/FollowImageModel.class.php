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
  /*
   * khantminthu
   * */
  
  #PcenterController
   public function getFollow($id)
   {
        $where['user_id'] = $id;
        
        $getFollow = $this->where($where)->field('f_id')->find();
        
        $count1 = M('user')->where(['id'=>['IN',$getFollow['f_id']]])->count();
        
        $count2 = $this->where(['f_id'=>['like','%'.$id.'%']])->count();
        
        return $arr = array(
            'follow'=>$count1,
            'follower'=>$count2
        );
   }
   
   public function isUserFollow($id)
   {
       $where['user_id'] = $id;
       $isUser = $this->where($where)->find();
       if(empty($isUser)){
           return false;
       }
       return true;
   }
   
   public function isFollower($id , $f_id)
   {
       $where['f_id'] = ['like','%'.$f_id.'%'];
       $where['user_id'] = $id;
       $isFollower = $this->where($where)->find();
       if(empty($isFollower)){
           return false;
       }
       return true;
       
   }
}
