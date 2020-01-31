<?php
namespace Common\Model;

class IntegralDailyModel extends BaseModel
{
	private static $obj;

	public static $id_d;	//Id

	public static $userId_d;	//user parent id

	public static $checkIn_d;	//CheckIn

	public static $Integral_d;	//Integral


	public static $time_d;	//time


	public static function getInitnation()
	{
		$class = __CLASS__;
		return self::$obj = empty(self::$obj instanceOf $class)? new self() :self::$obj;
	}

	public function getDailyInte($id)
	{
		return $getDailyUser = $this->where(['user_id'=>$id])->find();

	}

	public function addDaily($arr)
	{
		$this->add($arr);
	}

	public function saveArr(array $arr = [],$id='')
	{
 		$this->where(['user_id'=>$id])->save($arr);
	}

	public function getUser($str)
	{
		$where['user_id'] = $str;
		$field = 'time';
		$ret = $this->where($where)->field($field)->find();
		$ret = $ret['time'];
		return $ret;
	}

}
