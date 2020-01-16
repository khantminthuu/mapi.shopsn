<?php
namespace Common\Model;


/**
 * 模型
 */
class GoodsAttributeModel extends BaseModel
{

    private static $obj;

	public static $id_d;	//属性id

	public static $attrName_d;	//属性名称

	public static $typeId_d;	//属性分类id

	public static $attrIndex_d;	//0不需要检索 1关键字检索

	public static $attrType_d;	//0唯一属性 1单选属性 2复选属性

	public static $inputType_d;	// 0 手工录入 1从列表中选择 2多行文本框

	public static $attrValues_d;	//可选值列表

	public static $orderSort_d;	//属性排序

	public static $createTime_d;	//创建时间

	public static $updateTime_d;	//更新时间

    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
    }
    public function getAttrName($id){
        $where['id'] = $id;
        $field = 'attr_name';
        return $this->where($where)->field($field)->find()['attr_name'];
    }
}