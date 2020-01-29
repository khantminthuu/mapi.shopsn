<?php
namespace Common\Logic;
//use Common\Model\FollowImageModel;

class FollowImageLogic extends AbstractGetDataLogic
{
    public function __construct(array $data = [] , $split ='')
    {
        $this -> splitKey = $split;
        $this -> data  = $data;
//        $this -> modelObj = new FollowImageModel();

    }


    ## 	abstract method
    public function getResult()
    {

    }

    ## 	abstract method
    public function getModelClassName(): string
    {

    }

    public function getAllCategory()
    {
        $field = 'id,user_name,nick_name';
        $getdata = M('user')->field($field)->select();
        foreach ($getdata as $key => $value) {
            $img = M('user_header')->where(['user_id' => $value['id']])->field('user_header')->find();
            $getdata[$key]['img'] = $img['user_header'];
        }
        return $getdata;

    }

    public function getAllFollow()
    {
     $where['id'] = $this->data['id'];
     $save = M('goods_spec')->where($where)->field('status')->select();
     $save = $save[1];
     if($save['status']==1){
        $data = ['status'=>0];
        $res = M('goods_spec')->where($where)->save($data);
    }else{
        $data = ['status'=>1];
        $res = M('goods_spec')->where($where)->save($data);
    }
    if($res){
        echo "follow";
    }else{
        echo "unfollow";
    }

}






}