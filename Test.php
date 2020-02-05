<?php

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
