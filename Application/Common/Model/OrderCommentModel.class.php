<?php
namespace Common\Model;


use Think\SessionGet;

/**
 * 模型
 */
class OrderCommentModel extends BaseModel
{

    private static $obj;

	public static $id_d;	//id

	public static $goodsId_d;	//商品【编号】

	public static $orderId_d;	//订单【编号】

	public static $userId_d;	//用户【编号】

	public static $status_d;	//是否可见【1可见, 0.不可见】

	public static $content_d;	//评论内容

	public static $createTime_d;	//评论时间

	public static $anonymous_d;	//是否匿名【 0.是  1.否】

	public static $score_d;	//评分【 1-5】

	public static $level_d;	//评级【 0.差评(1,2分) 1.一般(3,4分) 2.好评(5分)】

	public static $labels_d;	//评论标签【0 手感好, 1 发货快 2 物美价廉 3 性价比高】

	public static $anwser_d;	//评论回复

	public static $havePic_d;	//是否有图片【0没有,1有】

	public static $storeId_d;	//店铺【编号】


    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
    }


   
    /**
     * 得到有图的评论
     *
     */
    public function getInmageCounts($goodId){

        $where = [
            'goods_id' => ['EQ', $goodId],
            'show_pic' => ['NEQ', ''],
        ];
        $count = $this->where($where)->count();
        return $count;
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
    /**
     * 得到所有的评论
     *
     */
    public function getCommont($user_id){

        $where['user_id'] = $user_id;
        $data = $this->field("id,goods_id,content,create_time,score")->where($where)->order("create_time DESC")->select();
        $count = $this->where($where)->count();
        return array("data"=>$data,"countAll"=>$count);
    }

}