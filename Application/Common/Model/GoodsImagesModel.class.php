<?php
namespace Common\Model;


/**
 * 商品图片模型
 */
class GoodsImagesModel extends BaseModel
{

    private static $obj;

	public static $id_d;	//id

	public static $goodsId_d;	//商品id

	public static $picUrl_d;	//商品图片

	public static $status_d;	//展示图片 1 是；0否

	public static $isThumb_d;	//缩略图【1是 0否】

	public static $sort_d;	//排序


    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
    }
    /**
     * 获取商品图片
     * author 刘嘉强
     */
    public function getgoodImage($goodId){
        $where['goods_id'] = $goodId;
        $where['is_thumb'] = '0';
        $field = 'pic_url';
        $images = $this->where($where)->field($field)->limit(4)->select();
        return $images;
    }

    /**
     * 获取推荐商品的图片
     * author 刘嘉强
     */
    public function getgoodOneImage($goodId){
        $where['goods_id'] = $goodId;
        $where['is_thumb'] = '1';
        $field = 'pic_url';
        $images = $this->where($where)->field($field)->find();
        return $images;
    }
    /**
     * 获取推荐商品的图片
     * author 刘嘉强
     */
    public function getgoodImageByGoods($goods){
        foreach ($goods as $key => $value) {
            if ($value['p_id'] == 0) {
                $where['goods_id'] = $value['id'];
            }else{
                $where['goods_id'] = $value['p_id'];
            }
            
            $where['is_thumb'] = '1';
            $field = 'pic_url';
            $goods[$key]['pic_url'] = $this->where($where)->getField('pic_url');
        }  
        return $goods;
    }


}