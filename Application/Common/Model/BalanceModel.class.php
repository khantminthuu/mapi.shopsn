<?php

// +----------------------------------------------------------------------
// | OnlineRetailers [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2003-2023 www.shopsn.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed 亿速网络（http://www.shopsn.net）
// +----------------------------------------------------------------------
// | Author: 王强 <opjklu@126.com>\n
// +----------------------------------------------------------------------

namespace Common\Model;

use Common\Model\BaseModel;
use Think\SessionGet;

/**
 * 余额模型 
 */
class BalanceModel extends BaseModel
{
    private static  $obj;


	public static $id_d;	//主键id

	public static $userId_d;	//用户id

	public static $accountBalance_d;	//账户余额

	public static $lockBalance_d;	//锁定余额

	public static $status_d;	//1有效2过期

	public static $modifyTime_d;	//修改时间

	public static $rechargeTime_d;	//添加时间

	public static $description_d;	//描述

	public static $type_d;	//类型 0消费1充值2提现

	public static $changesBalance_d;	//变动余额

    
    public static function getInitnation()
    {
        $name = __CLASS__;
        return static::$obj = !(static::$obj instanceof $name) ? new static() : static::$obj;
    }
    
    
    /**
     * 添加余额记录
     */
    public function addBalanceLogs ($monery)
    {
        if (!is_numeric($monery)) {
            return $this->traceStation(false, '添加余额记录失败');
        }
        
        $array = [
            self::$userId_d => SessionGet::getInstance('user_id')->get(),
            self::$accountBalance_d => $monery,
            self::$description_d    => '购买商品',
        ];
        
        $status = $this->add($array);
        
        if (!$this->traceStation($status, '添加余额记录失败')) {
            return false;
        }
        $this->commit();
        return $status;
    }
    
   
}