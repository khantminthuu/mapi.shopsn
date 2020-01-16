<?php
namespace Common\Model;


/**
 * 模型
 */
class StoreInformationModel extends BaseModel
{

    private static $obj;

	public static $id_d;	//编号

	public static $storeId_d;	//公司入驻表编号

	public static $shopAccount_d;	//商家账号

	public static $shopName_d;	//店铺名称

	public static $levelId_d;	//店铺等级

	public static $shopLong_d;	//开店时长

	public static $shopClass_d;	//店铺分类

	public static $scBail_d;	//店铺分类保证金

	public static $payingCertificate_d;	//付款凭证

	public static $payingCertif_d;	//付款凭证说明

	public static $status_d;	//入驻类型 0公司入驻  1 企业入驻

    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
    }



}