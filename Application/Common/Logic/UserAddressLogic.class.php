<?php

namespace Common\Logic;

use Common\Model\UserAddressModel;
use Common\Model\RegionModel;
use Think\SessionGet;
/**
 * 用户收货逻辑处理层
 * @author 薛松
 */
class UserAddressLogic extends AbstractGetDataLogic
{
	/**
	 * 构造方法
	 *
	 * @param array $data
	 */
	public function __construct(array $data = [], $split = '')
	{
		$this->data = $data;
		$this->splitKey = $split;
		
		$this->modelObj = new UserAddressModel();
		$this->region = new RegionModel();
	}
	
	public function getResult()
	{
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
	 */
	public function getModelClassName() :string
	{
		return UserAddressModel::class;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::hideenComment()
	 */
	public function hideenComment() :array
	{
		return [
			UserAddressModel::$realname_d,
		];
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::likeSerachArray()
	 */
	public function likeSerachArray() :array
	{
		return [
			UserAddressModel::$realname_d,
		];
	}
	
	/**
	 * @name 新增或者编辑收货地址验证规则
	 * 
	 * @des 新增或者编辑收货地址验证规则
	 * @updated 2017-12-21
	 */
	public function getRuleByAddEditAddress()
	{
		$message = [
			'realname'          => [
				'required'          => '请输入收货人姓名',
				'specialCharFilter' => '收货人姓名不正确',
			],
			'mobile'          => [
				'required'          => '请输入手机号',
				'specialCharFilter' => '手机号不正确',
			],
			'prov'          => [
				'required'          => '请选择省份',
				'specialCharFilter' => '省份选择不正确',
			],
			'city'          => [
				'required'          => '请选择城市',
				'specialCharFilter' => '城市选择不正确',
			],
			'dist'          => [
				'required'          => '请选择街道',
			],
			'address'          => [
				'required'          => '请输入详细地址',
			],
			'status'          => [
				'required'          => '请选择是否为默认',
				'specialCharFilter' => '参数不正确',
			],
		];
		return $message;
	}
	/**
	 * @name 新增收货地址逻辑
	 * 
	 * @des 新增收货地址逻辑
	 * @updated 2017-12-22
	 */
	public function addAddress()
	{   $post= $this->data;
		$post['user_id'] = SessionGet::getInstance('user_id')->get();
		$ret = $this->modelObj->addAddress($post);
		return $ret;
	}
	/**
	 * @name 编辑收货地址逻辑
	 * 
	 * @des 编辑收货地址逻辑
	 * @updated 2017-12-22
	 */
	public function editAddress()
	{
		$post= $this->data;
		$post['user_id'] = SessionGet::getInstance('user_id')->get();
		$where['id'] = $post['id'];
		$ret = $this->modelObj->saveAddress($where,$post); 
		return $ret;
	}
	/**
	 * @name 收货地址列表验证规则
	 * 
	 * @des 收货地址列表验证规则
	 * @updated 2017-12-21
	 */
	public function getRuleByAddressLists()
	{
		$message = [
			'page'          => [
				'required'          => '参数不正确',
				'specialCharFilter' => '参数不正确',
			],
		];
		return $message;
	}
	/**
	 * @name 收货地址列表逻辑
	 * 
	 * @des 收货地址列表逻辑
	 * @updated 2017-12-21
	 */
	public function addressLists()
	{
		$userId = session('user_id');
		
		//#TODO 这里是查询条件
		$this->searchTemporary = [
			UserAddressModel::$userId_d => $userId,
		];
		
		//#TODO 这里是要查询的字段如果不传的话默认为表中的所有字段
		$this->searchField = 'id, realname, mobile, create_time, update_time, prov, city, dist, address, status';
		
		//#TODO 这里是按照什么排序查询，如果不传默认为status DESC排序
		$this->searchOrder = 'create_time DESC, status DESC';

		//#TODO 调用通用的获取列表的接口并返回数据  data=>['countTotal'=>2, 'records'=>[.....]]
		$retData = parent::getDataList();
		//#TODO 处理返回的省份等翻译成中文
		if(!empty($retData['records'])){
			$regionDb = M('region');
			foreach ($retData['records'] as $k => $vo) {
				$retData['records'][$k]['create_time'] = date('Y-m-d', $vo['create_time']);//格式化创建时间
				$retData['records'][$k]['update_time'] = !empty($vo['update_time']) ? date('Y-m-d', $vo['update_time']) : '';//格式化更新时间
				$arr = array($vo['prov'], $vo['city'], $vo['area'], $vo['dist']);//查询条件
				$conditon['id'] = array('in', $arr);//查询条件
				$citys = $regionDb->where($conditon)->select();//查询在这些ID内的城市列表
				$newKey = array_column($citys, 'id');//将城市们按照ID排序
				$retData['records'][$k]['prov_name'] = $citys[ array_search($vo['prov'], $newKey)]['name'];//省  在数组中搜索为此建值的省份
				$retData['records'][$k]['city_name'] = $citys[ array_search($vo['city'], $newKey)]['name'];//市  在数组中搜索为此建值的市
				$retData['records'][$k]['area_name'] = $citys[ array_search($vo['area'], $newKey)]['name'];//区  在数组中搜索为此建值的区
				$retData['records'][$k]['dist_name'] = $citys[ array_search($vo['dist'], $newKey)]['name'];//街道 在数组中搜索为此建值的街道
			}
		}
		return $retData;
	}
	//收货地址列表
	public function getAddressLists(){
		$where['user_id'] = SessionGet::getInstance('user_id')->get();
		$field  = 'id,realname,mobile,prov,city,dist,address,status';
	    $list = $this->modelObj->getAddressByWhere($where,$field);
	    if (empty($list)) {
	    	return array("status"=>1,"message"=>"获取成功","data"=>$list);
	    }else{
            $address = $this->region->getRegionByAddress($list);
            return array("status"=>1,"message"=>"获取成功","data"=>$address);
	    }
	}
	/**
	 * @name 查看收货地址详情验证规则
	 * 
	 * @des 查看收货地址详情验证规则
	 * @updated 2017-12-21
	 */
	public function getRuleByAddressLook()
	{
		$message = [
			'id'          => [
				'required'          => '参数不正确',
				'specialCharFilter' => '参数不正确',
				'number'            => '参数不正确',
			],
		];
		return $message;
	}
	/**
	 * @name 查看收货地址详情逻辑
	 * 
	 * @des 查看收货地址详情逻辑
	 * @updated 2017-12-21
	 */
	public function addressLook()
	{	
		//#TODO 调用基类查询单条数据
		$post = $this->data;
		$where['id'] = $post['id'];
		$field  = 'id,realname,mobile,prov,city,dist,address,status';
		$retData = $this->modelObj->getFindOneByWhere($where,$field);
		if (empty($retData)) {
	    	return array("status"=>1,"message"=>"获取成功","data"=>"");
	    }else{
            $address = $this->region->getRegionByOne($retData); 
            return array("status"=>1,"message"=>"获取成功","data"=>$address);
	    }
	}
	/**
	 * @name 删除用户的收货地址
	 * 
	 * @des 删除用户的收货地址
	 * @updated 2017-12-21
	 */
	public function addressDel()
	{    
		$post = $this->data;
		$where['id'] = $post['id'];
		$retData = $this->modelObj->delAddressByWhere($where);
		return $retData;
	}

    /***
     * @return mixed|string
     * 获取默认用户默认地址
     */
	public function getUserDefaultAddress(){
        $userId = session('user_id');
        //获取用户的默认地址
        $user_defalt_Info = $this->modelObj->get_user_default_address($userId);
        return $user_defalt_Info;
    }
    /***
     * @return mixed|string
     * 获取默认用户默认地址
     */
	public function getUserDefault(){
        $userId = session('user_id');
        //获取用户的默认地址
        $user_defalt_Info = $this->modelObj->get_default_address($userId);
        return $user_defalt_Info;
    }
}
