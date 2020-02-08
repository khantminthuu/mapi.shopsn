<?php

public  function getVideoUpload()
{
    $join = "left join db_user_header as b on u.id = b.user_id";
    $field = "u.id , u.user_name , u.nick_name , b.user_header";
    $getUser = $this->userModelObj->alias(u)->join($join)->field($field)->select();
    foreach ($getUser as $key =>$value){
        $getUploadVideo = $this->modelObj->getUploadVideo($value['id']);
        var_dump($getUploadVideo);
        $getUser[$key]['UploadVideo'] = $getUploadVideo;
    }
    return $getUser;
}
