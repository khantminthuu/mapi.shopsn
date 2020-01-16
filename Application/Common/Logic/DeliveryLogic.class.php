<?php
namespace Common\Logic;

use Common\Model\DeliveryAuthorModel;
use Common\Model\DeliveryMoneyConfigModel;
use Common\Model\UserAddressModel;
use Common\Model\StoreAddressModel;
use Common\Model\RegionModel;
use Think\Cache;

class DeliveryLogic extends AbstractGetDataLogic
{
	/**
	 * 构造方法
	 * @param unknown $data
	 */
	public function __construct(array $data = [], $split = '')
	{
		$this->data = $data;
		
		$this->splitKey = $split;
		
		$this->modelObj = new DeliveryAuthorModel();
	}
	
	/**
	 * 获取店品牌数据
	 */
	public function getResult()
	{
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
	 */
	public function getModelClassName() :string
	{
		return DeliveryAuthorModel::class;
	}
	
	/**
	 * 切换状态验证
	 * @return string[][]
	 */
	public function getValidateByDelivery()
	{
		return [
			'store_id' => [
				'number' => '店铺id 必须是数字'
			],
			'address_id' => [
				'number' => '收货地址ID必须是数字'
			],
		];
	}

	/**
	 * 获取验证规则
	 */
	public function getValidateRule()
	{
	}
	//获取配送费
    public function getDeliveryMoney(){
	    $post = $this->data;
	    $configModel = new DeliveryMoneyConfigModel();
	    $author = $this->modelObj->find();
	    if(empty($author['is_open'])){
            return array('status'=>0,'message'=>'未开启配送','data'=>'');
        }
	    if($author['settings'] == 1){//设置方式1店铺设置
            $data['transport'] = "商家配送";
            $data['delivery'] = 2;
            $config = $configModel->where(['store_id'=>$post['store_id']])->find();
            if(empty($config)||$config['is_open']==0){
                return array('status'=>0,'message'=>'店铺未开启配送','data'=>'');
            }
            if($config['freight_mode'] == 1){
                $data['money'] = $config['freight_money'];
            }elseif($config['freight_mode'] == 2){
                $data['money'] = 0.00;
            }else{
                $money = $this->getDistance($post['address_id'],$post['store_id'],$config['distance']);
                $data['money'] = $money;
            }
        }else{//设置方式0平台设置
            if($author['transport_mode'] == 1){
                $data['transport'] = "商家配送";
                $data['delivery'] = 2;
            }else{
                $data['transport'] = "平台配送";
                $data['delivery'] = 1;
            }
            if($author['freight_mode'] == 1){
                $data['money'] = $author['freight_money'];
            }elseif($author['freight_mode'] == 2){
                $data['money'] = 0.00;
            }else{
                $money = $this->getDistance($post['address_id'],$post['store_id'],$author['distance']);
                $data['money'] = $money;
            }
        }
//	    showData($data);exit;
        return array('status'=>1,'message'=>'获取成功','data'=>$data);
    }
    public function getDistance($address_id,$store_id,$distance){
        $addressModel = new UserAddressModel();
        $storeAddressModel = new StoreAddressModel();
        $regionModel = new RegionModel();
        $address = $addressModel->where(['id'=>$address_id])->find();
        $a_prov  = $regionModel->where(['id'=>$address['prov']])->getFIeld('name');
        $a_city  = $regionModel->where(['id'=>$address['city']])->getFIeld('name');
        $a_dist  = $regionModel->where(['id'=>$address['dist']])->getFIeld('name');
        $storeAddress = $storeAddressModel->where(['store_id'=>$store_id])->find();
        $s_prov  = $regionModel->where(['id'=>$storeAddress['prov_id']])->getFIeld('name');
        $s_city  = $regionModel->where(['id'=>$storeAddress['city']])->getFIeld('name');
        $s_dist  = $regionModel->where(['id'=>$storeAddress['dist']])->getFIeld('name');
        $a_address = $a_prov.$a_city.$a_dist.$address['address'];

        $a_latitude = getLatByAddress($a_address);
        $s_address = $s_prov.$s_city.$s_dist.$storeAddress['address'];

        $s_latitude = getLatByAddress($s_address);

        $space = getdistance($a_latitude['lng'], $a_latitude['lat'], $s_latitude['lng'],$s_latitude['lat']);
        $price =  $space*$distance/1000;
        $price =  sprintf("%.2f",$price);
        return $price;
    }
}