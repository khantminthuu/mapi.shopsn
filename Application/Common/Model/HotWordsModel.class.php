<?php
namespace Common\Model;


/**
 * 模型
 */
class HotWordsModel extends BaseModel
{

    private static $obj;

	public static $id_d;	//id

	public static $hotWords_d;	//关键词

	public static $createTime_d;	//创建时间

	public static $updateTime_d;	//更新时间

	public static $goodsClass_id_d;	//商品分类id

	public static $isHide_d;	//1为隐藏，0为显示

    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
    }

    /**
     * 热门搜索
     *
     */
    public function getHotWordsList(){
        $where['is_hide'] = '0';
        $field = 'id,hot_words';
        return $this->where($where)->field($field)->order('update_time desc')->limit(5)->select();
    }

}