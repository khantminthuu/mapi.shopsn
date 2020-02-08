<?php
declare(strict_types=1);
namespace Common\Logic;
use Think\SessionGet;
use Think\Cache;
use Think\Log;
use Common\Model\VideoUploadModel;
use Common\Model\UserModel;
class VideoUploadLogic extends AbstractGetDataLogic
{
    public function __construct(array $data=[] , $split='')
    {
        $this->data = $data;
        $this->splitKey = $split;
        $this->modelObj = new VideoUploadModel();
        $this->userModelObj = new UserModel();
    }
    
    /**
     * 获取结果
     */
    public function getResult()
    {
        // TODO: Implement getResult() method.
    }
    
    /**
     * 获取当前模型类名
     */
    public function getModelClassName(): string
    {
        // TODO: Implement getModelClassName() method.
    }
    /*
     * khantminthu program start
     * */
    ##Is User Get['id']
    public function getUserId()
    {
        return array(
            'id' => array(
                'required' => "key must id"
            )
        );
    }
    ##get video upload from controller
    public function getVideoUpload()
    {
        $isUserUpload = $this->modelObj->field('user_id')->select();
        if(empty($isUserUpload)){
            return false;
        }
        $arr = array_map('current',$isUserUpload);
        $join = "left join db_user_header as b on u.id = b.user_id";
        $field = "u.id , u.user_name , u.nick_name , b.user_header";
        $where['u.id'] = ['IN',$arr];
        $getUser = $this->userModelObj->alias(u)->join($join)->where($where)
                ->field($field)->select();
        foreach ($getUser as $key =>$value){
            $getUploadVideo = $this->modelObj->getUploadVideo($value['id']);
            $getUser[$key]['UploadVideo'] = $getUploadVideo;
        }
        return $getUser;
    }
    ##like
    public function addLike()
    {
        $userId = SessionGet::getInstance('user_id')->get();
        $getId = $this->data['id'];
        $where['user_id'] = $this->data['id'];
        $isUser = $this->modelObj->isUser($getId , $userId   );
        if($isUser['a'] == 1) {
            $arr['like'] = $userId;
            $ret = $this->modelObj->where(['user_id' => $getId])->save($arr);
            return ['status'=>1,'message'=>'save','data'=>$ret];
        }
        if($isUser['a']==2){
            $getUser = $this->modelObj->where(['user_id'=>$getId])->field('like')->find();
            $arr['like'] = $getUser['like'].','.$userId;
            $ret = $this->modelObj->where(['user_id'=>$getId])->save($arr);
            return ['status'=>1,'message'=>'save','data'=>$ret];
        }
        if($isUser['a']==3){
            $getUser = $this->modelObj->where(['user_id'=>$getId])->field('like')->find();
            $strNo = strlen(','.$userId);
            $str = substr($getUser['like'],0,-$strNo);
            $arr['like'] = $str;
            $ret = $this->modelObj->where(['user_id'=>$getId])->save($arr);
            return ['status'=>1,'message'=>'remove User','data'=>$ret];
        }
        return ['status'=>0,'message'=>'save is not success','data'=>[]];
    }
    
}
