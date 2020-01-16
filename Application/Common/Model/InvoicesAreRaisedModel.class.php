<?php
namespace Common\Model;


/**
 * 发票抬头模型
 */
class InvoicesAreRaisedModel extends BaseModel
{

    private static $obj;


	public static $id_d;	//

	public static $name_d;	//单位名称

	public static $def_d;	//是否默认 0否 1 是

	public static $status_d;	//抬头类型【0 个人 1单位】

	public static $createTime_d;	//

	public static $updateTime_d;	//

	public static $userId_d;	//用户


    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
    }
    public function getInvoiceAreRaised($where,$field,$method){
        $data = $this->field($field)->where($where)->$method();
        return $data;
    }
}