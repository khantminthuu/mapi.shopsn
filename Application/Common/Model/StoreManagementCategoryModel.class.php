<?php
namespace Common\Model;


/**
 * 模型
 */
class StoreManagementCategoryModel extends BaseModel
{

    private static $obj;

	public static $id_d;	//编号

	public static $storeId_d;	//入驻表编号

	public static $oneClass_d;	//一级类目

	public static $twoClass_d;	//二级类目

	public static $threeClass_d;	//三级类目

	public static $status_d;	//入驻类型 0公司入驻  1 企业入驻

    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
    }

    public function add_management_category($store_id,$data,$status){
        $date = array();
        foreach ($data as $key => $value) {
            $data[$key]['store_id'] = $store_id;
            $data[$key]['status'] = $status;
        }
        $res = $this->addAll($data);
        return $res;
       // $data['store_id'] = $store_id;
       // $data['one_class'] = $oneClass;
       // $data['two_class'] = $twoClass;
       // $data['three_class'] = $threeClass;
       // $data['status'] = 1;
       // $result = $this->add($data);
       // if ($result){
       //     return true;
       // }
       // return false;
    }



}