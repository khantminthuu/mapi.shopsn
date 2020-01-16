<?php
namespace Common\Model;


use Think\SessionGet;

/**
 * 模型
 */
class OrderPackageCommentModel extends BaseModel
{

    private static $obj;
    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
    }

 
    //添加数据
    public function commontAdd($data){
        $data['user_id'] = SessionGet::getInstance('user_id')->get();
        $data['create_time'] = time();
        if ($data['score']<3) {
            $data['level']=0;
        }else if($data['score'] == 5){
            $data['level']=2;
        }else{
            $data['level']=1;
        }
        $res = $this->add($data);
        return $res;
    }

}