<?php
namespace Common\Model;


/**
 * 模型
 */
class StorePersonModel extends BaseModel
{

    private static $obj;


	public static $id_d;	//主键编号

	public static $userId_d;	//用户编号

	public static $storeName_d;	//店铺名称

	public static $personName_d;	//姓名

	public static $idCard_d;	//身份证号码

	public static $idcardPositive_d;	//身份证正面

	public static $otherSide_d;	//身份证反面

	public static $bankName_d;	//银行名称

	public static $bankAccount_d;	//银行账号

	public static $createTime_d;	//创建时间

	public static $updateTime_d;	//更新时间

	public static $status_d;	//申请状态 【0-已提交申请 1-缴费完成  2-审核成功 3-审核失败 4-缴费审核失败 5-审核通过开店】

	public static $mobile_d;	//联系人电话

	public static $alipayAccount_d;	//支付宝支付账号
	
	public static $wxAccount_d;	//微信支付账号

    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
    }
    public function getSoreJionPerson ($id){
        //得到店铺详细的地址信息
        $where = [
            'user_id' => $id,
            'status' => 5,
        ];
        $field = 'mobile ';
        $shopInfo = $this->where($where)->field($field)->find();
        return $shopInfo;
    }

    public function get_store_info($id,$field){
        $where['id'] = $id;
        $info =  $this->where($where)->field($field)->find();
        return $info;

    }

}