<?php
namespace Common\Model;


/**
 * 发票内容模型
 */
class InvoiceContentModel extends BaseModel
{

    private static $obj;

	public static $id_d;	//发票内容编号

	public static $name_d;	//内容名称

	public static $status_d;	//是否启用【0不，1是】

	public static $updateTime_d;	//更新时间

	public static $createTime_d;	//

    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
    }

    public function getInvoiceContent(){
        $where['status'] = 1;
        $field = "id,name";
        return  $this->where($where)->field($field)->select();
    }



}