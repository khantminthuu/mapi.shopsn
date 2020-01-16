<?php
namespace Common\Model;


/**
 * 模型
 */
class StoreCompanyBankInformationModel extends BaseModel
{

    private static $obj;

	public static $id_d;	//编号

	public static $storeId_d;	//店铺【编号】

	public static $accountName_d;	//开户名

	public static $companyAccount_d;	//公司银行账号

	public static $branchBank_d;	//开户银行支行名称

	public static $branchNumber_d;	//支行联行号

	public static $bankElectronic_d;	//开户银行许可证电子版

	public static $isSettle_d;	//是否以开户行作为结算账号 0-否 1-是

	public static $settleName_d;	//结算账户开户名

	public static $settleAccount_d;	//结算公司银行账号

	public static $settleBank_d;	//结算开户银行支行名称

	public static $settleNumber_d;	//结算支行联行号

	public static $certificateNumber_d;	//税务登记证号

	public static $identificationNumber_d;	//纳税人识别号

	public static $registrationElectronic_d;	//税务登记证号电子版
	
	public static $alipayAccount_d;	//支付宝支付账号
	
	public static $wxAccount_d;	//微信支付账号

    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
    }



}