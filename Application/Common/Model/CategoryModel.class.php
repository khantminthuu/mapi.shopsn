<?php
namespace Common\Model;

class CategoryModel extends BaseModel
{
	private static $obj;

	public static $id_d;	//id

	public static $detail_d;	//Deatil

	public static $type_d;	//0 is category , 1 is recommend

	public static $hide_d;	//0 is hide , 1 is show


	public static function getInitnation()
	{
		$class = __CLASS__;
		return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
	}
	public function jasmine()
	{
		return [];
	}
}