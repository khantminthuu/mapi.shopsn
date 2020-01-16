<?php
namespace Common\Model;
use Common\Model\CommonModel;
use Think\SessionGet;

/**
 * 用户地址模型 
 */
class UserAddressModel extends BaseModel
{
    private static $obj ;

	public static $id_d;	//id

	public static $realname_d;	//名字

	public static $mobile_d;	//手机号

	public static $userId_d;	//user_id

	public static $createTime_d;	//创建时间

	public static $updateTime_d;	//更新时间

	public static $prov_d;	//省

	public static $city_d;	//城市编号

	public static $dist_d;	//区域编号

	public static $address_d;	//地址说

	public static $status_d;	//是否默认地址    默认 1   不默认 0

	public static $zipcode_d;	//邮编

	public static $alias_d;	//地址别名

	public static $email_d;	//电子邮件

	public static $telphone_d;	//座机

	public static $type_d;	//地址类型【0 -收货地址，1-公司地址（店铺地址），2-开户行地址，3-结算账号开户行地址，4- 实体店地址

    
    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !(self::$obj instanceof $class) ? new self() : self::$obj;
    }
	/**
	 * @name 新增收货地址
	 * 
	 * @des 新增收货地址
	 * @updated 2017-12-16 15:11
	 */
	public function addAndEdit($params, $userId)
	{
		M()->startTrans(); //#TODO 启动事务
		try {
			$data['realname'] = $params['realname'];
			$data['mobile'] = $params['mobile'];
			$data['prov'] = $params['prov'];
			$data['city'] = $params['city'];
			$data['dist'] = $params['dist'];
			$data['address'] = $params['address'];
			$default = $params['default'];
			$data['user_id'] = $userId;
			//#TODO 新增
			if (empty($params['id']) || !is_numeric($params['id'])) {
				$data['create_time'] = time();
				$ret = $this->add($data);
				if (false === $ret) {
					E("新增失败!");
				}
				$addressId = $ret;
			} else {//#TODO 编辑
				$addressId = $params['id'];
				//#TODO 查询该收货地址是否为该用户下的
				$ret = $this->getAddressDetails($data['user_id'], $addressId);
				if(false == $ret){
					E("编辑失败!");
				}
				$data['update_time'] = time();
				$ret = $this->where(['id'=>$addressId])->save($data);
				if (false === $ret) {
					E("编辑失败!");
				}
			}
			//#TODO 设置为默认地址--将原默认地址设置为非默认地址
			if ($default == 1) {
				$ret = $this->where(['user_id' => $data['user_id'], 'status' => 1])->save(['status' => 0]);
				if (false === $ret) {
					E("操作失败!");
				}
				$ret = $this->where(['id' => $addressId])->save(['status' => 1]);
				if (false === $ret) {
					E("操作失败!");
				}
			}
			//#TODO 提交事务
			M()->commit();
		} catch (\Exception $e) {
			//#TODO 回滚事务
			M()->rollback();
			return false;
		}
		return true;
	}
	/**
	 * @name 查看收货地址详情
	 * 
	 * @des 查看收货地址详情
	 * @updated 2017-12-16 16:30
	 */
	public function getAddressDetails($userId, $addressId){
		//#TODO 格式化时间不管用,我也很无奈 DATE_FORMAT(create_time,"%Y-%m-%d %H:%i")
		$ret = $this
			->field('realname, mobile, create_time, update_time, prov, city, area, dist, address, status')
			->where(['user_id'=>$userId, 'id'=>['IN', $addressId]])
			->find();
		if(empty($ret) || false === $ret){
			return false;
		}
		return $ret;
	}

	public function checkAddress($uid){
        $ret = $this
            ->field('id,realname,mobile,prov,city,dist,address')
            ->where(['user_id'=>$uid])
            ->find();
        if(empty($ret) || false === $ret){
            return false;
        }
        return $ret;
    }
    public function getUserAddress($aid,$uid =0){
	    $where['id'] = $aid;
	    $where['user_id'] = $uid;
	    $field = 'realname,mobile,prov,city,dist,address';
	    $ainfo = $this->where($where)->field($field)->find();
	    if (empty($ainfo)) {
	    	return "";
	    }
	    //获取具体的省市区
        $region = CommonModel::getRegion();
        $cityInfo = $region->getcityInfo($ainfo['prov'],$ainfo['city'],$ainfo['dist']);
        $ainfo['prov_name'] = $cityInfo['prov_name'];
        $ainfo['city_name'] = $cityInfo['city_name'];
        $ainfo['dist_name'] = $cityInfo['dist_name'];
        $ainfo['addressInfo'] = $cityInfo['prov_name'].$cityInfo['city_name'].$cityInfo['dist_name'].'  '.$ainfo['address'];
        unset($ainfo['prov']);
        unset($ainfo['city']);
        unset($ainfo['dist']);
        unset($ainfo['address']);
        return $ainfo;
    }
	//根据用户id获取收货地址列表
	public function addresslist(){

			$id=session('user_id');
			$region_model=M('region');
			$list=M('user_address')
				->field('id,realname,mobile,prov,city,dist,address')
				->where(array('user_id'=>$id))
				->select();

				foreach($list as $k=>$vo){
				$arr=array($vo['prov'],$vo['city'],$vo['dist']);
				$conditon['id']=array('in',$arr);
				$add=$region_model->where($conditon)->select();
				$newKey=array_column($add,'id');
				$a=array_search($vo['prov'],$newKey);
				$b=array_search($vo['city'],$newKey);
				$list[$k]['prov']=$add[$a]['name'];
				$list[$k]['city']=$add[$b]['name'];
				if($vo['dist']!=-1) {
					$c=array_search($vo['dist'],$newKey);
					$list[$k]['dist']=$add[$c]['name'];
				}
			}
			if(empty($list)){
				return null;
			}else{
				return $list;
			}
	}
	//根据用户id获取默认收货地址
	public  function get_user_default_address($userId){
		$where['user_id'] = $userId;
		$where['status']  = 1;
		$field = 'id,realname,mobile,prov,city,dist,address,status';
		$region_model = M('region');
		$list  = $this->field($field)->where($where)->find();
        if (empty($list)) {
        	$list = $this->field($field)->where(['user_id'=>SessionGet::getInstance('user_id')->get()])->find();
        	if (empty($list)) {
        		return "";
        	}
        }
        $list['prov_id']=$list['prov'];
		$list['city_id']=$list['city'];
		$list['dist_id']=$list['dist'];			
		$list['prov']=$region_model->where(['id'=>$list['prov']])->getField('name');
		$list['city']=$region_model->where(['id'=>$list['city']])->getField('name');
		$list['dist']=$region_model->where(['id'=>$list['dist']])->getField('name');
		return $list;		
	}
	//根据用户id获取默认收货地址
    public  function get_default_address($userId){
        $where['user_id'] = $userId;
        $where['status']  = 1;
        $field = 'id,realname,mobile,prov,city,dist,address,status';
        $region_model = M('region');
        $list  = $this->field($field)->where($where)->find();
        if (empty($list)) { 
            return array('status'=>0,"message"=>"暂无数据","data"=>'');;           
        }
        $list['prov_id']=$list['prov'];
        $list['city_id']=$list['city'];
        $list['dist_id']=$list['dist'];         
        $list['prov']=$region_model->where(['id'=>$list['prov']])->getField('name');
        $list['city']=$region_model->where(['id'=>$list['city']])->getField('name');
        $list['dist']=$region_model->where(['id'=>$list['dist']])->getField('name');
        return array('status'=>1,"message"=>"获取成功","data"=>$list);      
    }
    //获取用户收货地址
    public function getAddressByWhere($where,$field){
    	$data = $this->field($field)->where($where)->select();
    	return $data;
    }
    /**
	 * @name 新增收货地址
	 * 
	 * @des 新增收货地址
	 * @updated 2017-12-16 15:11
	 */
	public function addAddress($params)
	{
		M()->startTrans(); //#TODO 启动事务
		if ($params['status'] == 1) {
			$where['user_id'] = $params['user_id'];
			$where['status'] = 1;
			$date['status'] = 0;
			$date['update_time'] = time();
			$ret = $this->where($where)->save($date);			
			if ($ret === false) {
				M()->rollback();
				return array('status'=>0,"message"=>"添加失败","data"=>'');
			}
		}	
		$params['create_time'] = time();
		$params['type'] = 0;
		$ret = $this->add($params);
		if (!$ret) {
			M()->rollback();
			return array('status'=>0,"message"=>"添加失败","data"=>'');
		}
		M()->commit();
		return array('status'=>1,"message"=>"添加成功","data"=>'');	
	}
	/**
	 * @name 新增收货地址
	 * 
	 * @des 新增收货地址
	 * @updated 2017-12-16 15:11
	 */
	public function saveAddress($where,$params)
	{
		M()->startTrans(); //#TODO 启动事务
		if ($params['status'] == 1) {
			$a_where['user_id'] = $params['user_id'];
			$a_where['status'] = 1;
			$date['status'] = 0;
			$date['update_time'] = time();
			$ret = $this->where($a_where)->save($date);			
			if ($ret === false) {
				M()->rollback();
				return array('status'=>0,"message"=>"修改失败","data"=>'');
			}
		}	
		$params['update_time'] = time();
		$ret = $this->where($where)->save($params);
		if ($ret === false) {
			M()->rollback();
			return array('status'=>0,"message"=>"修改失败","data"=>'');
		}
		M()->commit();
		return array('status'=>1,"message"=>"修改成功","data"=>'');	
	}
	//获取单条数据
	public function getFindOneByWhere($where,$field){
		$data = $this->field($field)->where($where)->find();
    	return $data;
	}
	//删除
	public function delAddressByWhere($where){
		$res = $this->where($where)->delete();
		if (!$res) {
			return array('status'=>0,"message"=>"删除失败","data"=>'');
		}
    	return array('status'=>1,"message"=>"删除成功","data"=>'');
	}
}