<?php
namespace Common\Model;


/**
 * 模型
 */
class OrderInvoiceModel extends BaseModel
{

    private static $obj;

	public static $id_d;	//发票id

	public static $orderId_d;	//订单编号

	public static $raisedId_d;	//发票抬头【编号】

	public static $contentId_d;	//发票内容

	public static $createTime_d;	//创建时间

	public static $updateTime_d;	// 修改日期

	public static $userId_d;	//用户id

	public static $remarks_d;	//备注

	public static $typeId_d;	//发票类型


    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
    }

    /**
     * 增加发票
     *
     */
    public function addInvoce($data,$uid,$order_big){
        foreach($data as $k1=>$v1){
            $bill['content']=$v1['content'];
            $bill['invoice_title']=$v1['invoice_title'];
            $bill['invoice_type']=$v1['invoice_type'];
            $bill['order_id']=$order_big;
            $bill['create_time']=time();
            $bill['user_id']=$uid;
            $result = M('invoice')->add($bill);
            if ($result){
                return true;
            }else{
                return false;
            }
        }
    }

    //获取发票内容(不分页)
    public function getInvoiceInfo($where,$field,$method){
        $data = $this->field($field)->where($where)->$method();
        return $data;
    }

}