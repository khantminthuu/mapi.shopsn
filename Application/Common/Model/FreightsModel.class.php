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

/**
 * 运费模板 
 */
class FreightsModel extends BaseModel
{
    
    private static  $obj;


	public static $id_d;	//id

	public static $expressTitle_d;	//运费模板名称

	public static $sendTime_d;	//发货时间【几个小时内发货】

	public static $isFree_shipping_d;	//运费类型【0自定义运费1卖家包邮】

	public static $valuationMethod_d;	//计价方式【0:按件 1:按重量 2:按体积】

	public static $isSelect_condition_d;	//是否指定条件包邮 【0=>false 1=>true】

	public static $stockId_d;	//关联仓库

	public static $updateTime_d;	//更新时间

	public static $createTime_d;	//创建时间

	public static $storeId_d;	//商户编号

    
    public static function getInitnation()
    {
        $name = __CLASS__;
        return static::$obj = !(static::$obj instanceof $name) ? new static() : static::$obj;
    }
    
    protected function _before_insert(& $data, $options)
    {
        $data[static::$createTime_d] = time();
        $data[static::$updateTime_d] = time();
        return $data;
    }
    
    protected function _before_update(& $data, $options)
    {
        $data[static::$updateTime_d] = time();
        return $data;
    }
    //获取模板列表
    public function getFreightListByWhere($where,$field,$order="create_time DESC"){
        $list = $this->field($field)->where($where)->select();
        if (empty($list)) {
            return array("status"=>"","message"=>"暂无数据","data"=>"");
        }
        return array("status"=>1,"message"=>"获取成功","data"=>$list);
    }
    //获取模板列表
    public function getFreightListById($where,$field,$order="create_time DESC"){
        if (empty($where)) {
            return array("status"=>"","message"=>"参数错误","data"=>"");
        }
        $list = $this->field($field)->where($where)->find();
        if (empty($list)) {
            return array("status"=>"","message"=>"暂无数据","data"=>"");
        }
        return array("status"=>1,"message"=>"获取成功","data"=>$list);
    }
    //添加运费 模板
    public function addFreight($data){
        if (empty($data)) {
            return array("status"=>"","message"=>"参数错误","data"=>"");
        }
        $res = $this->add($data);
        if (!$res) {
            return array("status"=>"","message"=>"添加失败","data"=>"");
        }
        return array("status"=>1,"message"=>"添加成功","data"=>$res);
    }
    //修改数据
    public function saveFreight($where,$data){
        if (empty($where)||empty($data)) {
            return array("status"=>"","message"=>"参数错误","data"=>"");
        }
        $res = $this->where($where)->save($data);
        if (!$res) {
            return array("status"=>"","message"=>"修改失败","data"=>"");
        }
        return array("status"=>1,"message"=>"修改成功","data"=>"");
    }
    //删除数据
    public function delFreight($where){
        if (empty($where)) {
            return array("status"=>"","message"=>"参数错误","data"=>"");
        }
        M()->startTrans();
        $res = $this->where($where)->delete();
        if (!$res) {
            M()->rollback();
            return array("status"=>"","message"=>"删除失败","data"=>"");
        }
        $res = D('FreightCondition')->condition_delete($where['id']);
        M()->commit();
        return array("status"=>1,"message"=>"删除成功","data"=>"");        
    }
    //获取模板名称
    public function getFreightTitle(array $data){
        if (empty($data)) {
            return "";
        }
        foreach ($data as $key => $value) {
            $where['id'] = $value['freight_id']; 
            $data[$key]['freight_name'] = $this->where($where)->getField('express_title');
        }
        return $data;
    }
     //获取模板名称
    public function getFreightTitleOne($data){
        if (empty($data)) {
            return "";
        }
        $where['id'] = $data['freight_id']; 
        $data['freight_name'] = $this->where($where)->getField('express_title');
    
        return $data;
    }
}