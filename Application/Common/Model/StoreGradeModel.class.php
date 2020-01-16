<?php
namespace Common\Model;


/**
 * 模型
 */
class StoreGradeModel extends BaseModel
{

    private static $obj;

	public static $id_d;	//索引ID

	public static $levelName_d;	//等级名称

	public static $goodsLimit_d;	//允许发布的商品数量

	public static $albumList_d;	//允许上传图片数量

	public static $spaceLimit_d;	//上传空间大小【单位MB】

	public static $templateNumber_d;	//选择店铺模板套数

	public static $price_d;	//开店费用(元/年)

	public static $description_d;	//申请说明

	public static $upperLimit_d;	//销售上限

	public static $lowerLimit_d;	//销售下限金额

	public static $status_d;	//是否启用【0否1是】

	public static $createTime_d;	//创建时间

	public static $updateTime_d;	//更新时间

    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
    }

    public function get_all_shop_level_list(){
        $where['status'] = 1;
        $field = 'create_time,update_time,status';
        return  $this->where($where)->field($field,true)->select();

    }





}