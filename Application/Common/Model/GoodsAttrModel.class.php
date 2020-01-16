<?php
namespace Common\Model;


/**
 * 模型
 */
class GoodsAttrModel extends BaseModel
{

    private static $obj;

	public static $id_d;	//id

	public static $attributeId_d;	//商品属性编号

	public static $goodsId_d;	//商品id

	public static $attrValue_d;	//属性值

	public static $attrPrice_d;	//属性价格

	public static $createTime_d;	//创建时间

	public static $updateTime_d;	//更新时间

    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
    }



}