<?php
namespace Common\Model;


/**
 * 商品类型名称模型
 */
class GoodsSpecModel extends BaseModel
{
    private static $obj;

	public static $id_d;	//主键编号

	public static $name_d;	//规格名称

	public static $classOne_d;	//一级分类【id】

	public static $classTwo_d;	//二级分类

	public static $classThree_d;	//三级分类

	public static $sort_d;	//排序

	public static $status_d;	//状态显示【1显示 0 不显示  默认显示】

	public static $createTime_d;	//创建时间

	public static $updateTime_d;	//更新时间


	public static $storeId_d;	//商家编号

   
    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
    }

    /**
     * 获取商品属性
     * @param  int $ids 商品规格数组
     * @param  int $class 商品规格数组
     * @return array|Boolean   商品信息
     *
     */
    public function goodsSpec($goods_type) {
        // TODO 修改 

        // 获取属性
        $spce = array();
        $specification = $this
            ->alias('s')
            ->join('db_goods_spec_item as i ON s.id=i.`spec_id`')
            ->where('type_id='.$goods_type)
            ->field('s.id as type_id,name,i.id as item_id,item')
            ->select();
        foreach ($specification as $item) {
            $type_id = $item['type_id'];
            if (!empty($spce[$type_id])) {
                unset($item['name']);
                unset($item['type_id']);
                $spce[$type_id]['items'][] = $item;
            } else {
                $spce[$type_id]['type_id'] = $type_id;
                $spce[$type_id]['name']    = $item['name'];
                $spce[$type_id]['items'][] = ['item_id'=>$item['item_id'], 'item'=>$item['item']];
            }
        }
        unset($specification);
        return array_values($spce);
    }
    /**
     * 获取商品属性
     *
     */
    public function getSpe($speId){
        $where['id'] = $speId;
        $speName = $this->where($where)->field("name")->find()['name'];
        return $speName;

    }



}