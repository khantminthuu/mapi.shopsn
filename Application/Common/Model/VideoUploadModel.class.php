<?php
namespace Common\Model;
class VideoUploadModel extends BaseModel
{
    public static $obj;
    
	public static $id_d;	//Id

	public static $userId_d;	//User ID and get Follower

	public static $detail_d;	//Video Post Detail

	public static $videoLink_d;	//Upload Video Link

	public static $comment_d;	//Which user comment

	public static $like_d;	//Which user like

    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !(self::$obj instanceof $class) ? new self() : self::$obj;
    }
    ##IsUser
    public function isUser($id , $user_id)
    {
        $getUserId = $this->where(['user_id'=>$id])->field('like')->find();
        if($getUserId['like']==''){
            return ['a'=>1];
        }else{
            $isUser = explode(',',$getUserId['like']);
            $isUserExit = in_array($user_id , $isUser);
            if(!empty($isUserExit)){
                return ['a'=>3];
            }
            return ['a'=>2];
        }
        
    }
    ##getUploadvideo from VideoUploadLogic
    public function getUploadVideo($id)
    {
        $where['user_id'] = $id;
        $field = 'user_id,detail';
        $getPostVideo = $this->where($where)->field($field)->find();
        $getPostCount = $this->where($where)->field('comment,like')->find();
        $commentCount =count(explode(',',$getPostCount['comment']));
        $likeCount = count(explode(',',$getPostCount['like']));
        $getPostVideo['like'] = $likeCount;
        $getPostVideo['comment'] = $commentCount;
        return  $getPostVideo;
    }
 
}
