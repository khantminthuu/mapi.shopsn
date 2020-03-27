<?php
namespace Common\Logic;
use Common\Model\FollowImageModel;
use Think\SessionGet;
use Common\Model\UserModel;
class FollowImageLogic extends AbstractGetDataLogic
{
    public function __construct(array $data = [] , $split ='')
    {
        $this -> splitKey = $split;
        $this -> data  = $data;
        $this -> modelObj = new FollowImageModel();
        $this -> userModelObj = new UserModel();
    }


    ## 	abstract method
    public function getResult()
    {

    }

    ## 	abstract method
    public function getModelClassName(): string
    {

    }
    
    /*
     * khantminthu
     * */
    public function getFollowId()
    {
        return [
            'id' => [
                'required' => 'need to put id'
            ]
        ];
    }
    
    public function getAllUser()
    {
        $userId = SessionGet::getInstance('user_id')->get();
        
        $getFollow = $this->modelObj->where(['user_id'=>$userId])->field('f_id')->find();
        
        $join = "right join db_user_header as h on u.id = h.user_id";
        
        $field = "u.id,u.user_name,u.nick_name,h.user_header";
        
        $where['u.id'] = ['not in' , $getFollow['f_id']];
        
        $getUser = $this->userModelObj->alias('u')->join($join)->where($where)->field($field)
                    ->limit('3')->select();
        
        return $getUser;
    }
    
    public function addFollow()
    {
        $userId = SessionGet::getInstance('user_id')->get();
        
        $isUser = $this->modelObj->isUserFollow($userId);
        
        $arr['user_id'] = $userId;
        $arr['status'] =1;
        if(empty($isUser)){
            $arr['f_id'] = $this->data['id'];
            $this->modelObj->add($arr);
        }else{
            $isFollower = $this->modelObj->isFollower($userId , $this->data['id']);
            if(empty($isFollower)){
                return $arr = ['status'=>0,'message'=>'unsuccess','data'=>false];
            }
                $where['user_id'] = $userId;
            
                $getFollow = $this->modelObj->where($where)->field('f_id')->find();
                
                $arr['f_id'] = $getFollow['f_id'].','.$this->data['id'];
                
                $this->modelObj->where(['user_id'=>$userId])->save($arr);
                
                return $arr = ['status'=>1,'message'=>'success','data'=>true];
        }
    }

}
