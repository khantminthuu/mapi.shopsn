<?php
namespace Common\Model;

/**
 * 企业入驻信息模型
 */
class StoreJoinCompanyModel extends BaseModel
{

    private static $obj;



	public static $id_d;	//主键编号

	public static $userId_d;	//申请人

	public static $storeName_d;	//店铺名称

	public static $companyName_d;	//公司名称

	public static $numberEmployees_d;	//员工数

	public static $registeredCapital_d;	//注册资金数

	public static $licenseNumber_d;	//营业执照号

	public static $validityStart_d;	//营业执照开始日期

	public static $validityEnd_d;	//营业执照结束日期

	public static $electronicVersion_d;	//营业执照电子版

	public static $organizationCode_d;	//组织机构代码

	public static $organizationElectronic_d;	//组织机构代码证电子版

	public static $taxpayerCertificate_d;	//一般纳税人证明

	public static $createTime_d;	//创建时间

	public static $updateTime_d;	//更新时间

	public static $status_d;	//申请状态 【0-已提交申请 1-缴费完成  2-审核成功 3-审核失败 4-缴费审核失败 5-审核通过开店】

	public static $remark_d;	//备注

	public static $mobile_d;	//联系人手机

	public static $companyMobile_d;	//公司电话

	public static $name_d;	//联系人姓名

	public static $scopeOf_operation_d;	//法定经营范围


    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
    }

    public function getSoreJionCompany($id){
        //得到店铺详细的地址信息
        $where = [
            'user_id' => $id,
//            'user_id' => 3,
            'status' => 5,
        ];
        $field = 'mobile';
        $shopInfo = $this->where($where)->field($field)->find();
        return $shopInfo;
    }

    public function get_store_info($id,$field){
        $where['id'] = $id;
        $info =  $this->where($where)->field($field)->find();
        return $info;

    }



}