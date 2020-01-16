<?php
namespace Common\Model;
use Common\Model\CommonModel;

/**
 * 企业入驻地址信息模型
 */
class StoreAddressModel extends BaseModel
{

    private static $obj;


	public static $id_d;	//编号

	public static $storeId_d;	//店铺编号

	public static $provId_d;	//省

	public static $city_d;	//市

	public static $dist_d;	//区

	public static $storeZip_d;	//邮政编码

	public static $address_d;	//详细地址

    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
    }

    public function get_address($id){
        $where['id'] = $id;
        $field = 'prov_id,city,dist';
        $cityCode = $this->where($where)->field($field)->find();
        $address_name = CommonModel::getRegion()->getcityInfo($cityCode['prov_id'],$cityCode['city'],$cityCode['dist']);
        return $address_name['prov_name'].$address_name['city_name'].$address_name['dist_name'];

    }
    public function getAddress($id){
        if (empty($id)) {
            return "";
        }
        $where['id'] = $id;
        $field = 'prov_id,city,dist,address';
        $cityCode = $this->where($where)->field($field)->find();
        $address_name = D('Region')->getcityInfo($cityCode['prov_id'],$cityCode['city'],$cityCode['dist']);
        return $address_name['prov_name'].$address_name['city_name'].$address_name['dist_name'].$cityCode['address'];

    }




}