<?php
namespace Common\Model;


/**
 * 模型
 */
class BrandModel extends BaseModel
{

    private static $obj;

	public static $id_d;	//id

	public static $brandName_d;	//品牌名称

	public static $goodsClass_id_d;	//所属商品分类编号

	public static $brandLogo_d;	//品牌图片

	public static $brandDescription_d;	//品牌描述

	public static $recommend_d;	//1推荐0不推荐

	public static $createTime_d;	//创建时间

	public static $updateTime_d;	//更新时间

	public static $letter_d;	//品牌 字母

	public static $brandBanner_d;	//品牌banner

	public static $status_d;	//状态【0审核中， 1已通过， 2不通过】

	public static $storeId_d;	//商家编号


    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
    }
    /**
     * 获取品牌列表
     *
     */
    public function get_brand_list($brandName){
        $field_letter  = 'letter';
        $where = [];
        if (!empty($brandName)){
            $where[ 'brand_name' ] = array( 'like','%' . $brandName . '%' );
        }

        $brand = $this->field($field_letter)->where($where)->group('letter')->select();
        $arr = [];
        foreach ($brand as $key => $value) {
            $arr[$key]['letter'] = $value['letter'];
            $letteBrand = $this->brand_lists_by_letter($value['letter']);
            $arr[$key]['value'] = $letteBrand;
        }
        return $arr;
    }
    /**
     * 获取品牌列表
     *
     */
    public function brand_lists_by_letter($letter){
        $where = [
            'letter' => $letter,
        ];
        $field = 'id,brand_name,brand_logo,brand_description,letter,brand_banner';
        return $this->where($where)->field($field)->select();
    }
}