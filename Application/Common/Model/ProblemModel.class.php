<?php
namespace Common\Model;


/**
 * 商品咨询模型模型
 */
class ProblemModel extends BaseModel
{

    private static $obj;

	public static $id_d;	//主键id

	public static $userId_d;	//用户id

	public static $problem_d;	//问题

	public static $addtime_d;	//提问时间

	public static $status_d;	//

	public static $goodsId_d;	//商品id

    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
    }

    /**
     * 提交客户问题
     *
     */
    public function addProblem($data,$userId){

        $probleminfo['user_id'] = empty($userId)? 0:$userId;
        $probleminfo['problem'] = $data['problem'];
        $probleminfo['addtime'] = time();
        $probleminfo['status']  = 1;
        $probleminfo['goods_id'] = $data['goods_id'];
        $result = $this->add($probleminfo);
        if ($result){
            return true;
        }
        return false;
    }



}