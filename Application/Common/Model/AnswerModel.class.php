<?php
namespace Common\Model;


/**
 * 问题答案模型
 */
class AnswerModel extends BaseModel
{

    private static $obj;

	public static $id_d;	//主键id

	public static $problemId_d;	//问题id

	public static $addtime_d;	//回答时间

	public static $answerCode_d;	//回答人编码

	public static $answer_d;	//答案

	public static $status_d;	//状态

    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
    }
    public function getAnswers($problemId){
        $where['problem_id'] = $problemId;
        $field = 'addtime,answer,answer_code';
        return $this->where($where)->field($field)->find();
    }



}