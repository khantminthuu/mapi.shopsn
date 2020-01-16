<?php
namespace Common\Model;


/**
 * 发票类型模型
 */
class InvoiceTypeModel extends BaseModel
{

    private static $obj;

	public static $id_d;	//发票类型编号

	public static $name_d;	//发票名称

	public static $status_d;	//是否启用0否1是

	public static $createTime_d;	//

	public static $updateTime_d;	//


    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
    }

    public function getInvoiceType(){
        $where['status'] = 1;
        $feild = 'id,name,status';
        $data =  $this->where($where)->field($feild)->select();
        return $data;
    }



}