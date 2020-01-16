<?php
namespace Common\Model;


use Think\SessionGet;

/**
 * 模型
 */
class GoodsCartModel extends BaseModel
{

    private static $obj;


	public static $id_d;	//id

	public static $userId_d;	//用户ID

	public static $goodsId_d;	//商品【id】

	public static $goodsNum_d;	//商品数量

	public static $attributeId_d;	//商品属性编号

	public static $priceNew_d;	//套餐价

	public static $integralRebate_d;	//返利积分

	public static $createTime_d;	//创建时间

	public static $updateTime_d;	//更新时间

	public static $wareId_d;	//发货仓库

	public static $storeId_d;	//店铺编号


    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
    }

    //用户通过购物车购买，在购买以后得删除购物车里的购物
    public function deleteGood($goodId ,$uid){
        $where['goods_id'] = $goodId;
        $where['user_id'] = $uid;
        $result = $this->where($where)->delete();
        if ($result){
            return true;
        }
        return false;
    }

    public function getCounts($userId){
        $where['user_id'] = $userId;
        return $this->where($where)->sum('goods_num');
    }
    /**
     * 更改购物车商品数量
     * @author 刘嘉强
     */
    public function changeNumber($uid,$goodsId,$number){
        $where['user_id'] = $uid;
        $where['goods_id'] = $goodsId;
        $result = $this->where($where)->setInc('goods_num',$number);
        return $result;
    }
    /**
     * {@inheritDoc}
     * @see \Think\Model::_before_insert()
     */
    protected function _before_insert(&$data, $options)
    {
        $data[static::$updateTime_d] = time();
        
        $data[static::$createTime_d] = time();
        
        return $data;
    }

    /**
     * {@inheritDoc}
     * @see \Think\Model::_before_update()
     */
    public function _before_update(&$data, $options)
    {
        $data[static::$updateTime_d] = time();
        
        return $data;
    }

    public function getCartByWhere($where,$field){
        $data = $this->field($field)->where($where)->order("create_time DESC")->select();
        if (!empty($data)) {
            foreach ($data as $k => $v) {
                $res[$v['store_id']]['store_id'] = $v['store_id'];
                $res[$v['store_id']]['goods'][] = $v;
            }
            $date = array_values($res);
            return $date;
        }
        return $data;
    }
    /**
     * 更改购物车商品数量(加1)
     * @author 刘嘉强
     */
    public function plusNumber($cart_id){
        $where['id'] = $cart_id;
        $car_num = $this->where($where)->getField('goods_num');
        $goods_id = $this->where($where)->getField('goods_id');
        $stock = M('Goods')->where(['id'=>$goods_id])->getField('stock');
        if ($stock - $car_num < 0 ) {
            return array("status"=>0,"message"=>"库存不足","data"=>"");
        }
        $result = $this->where($where)->setInc('goods_num',1);
        if (!$result) {
            return array("status"=>0,"message"=>"操作失败","data"=>"");
        }
        return array("status"=>1,"message"=>"操作成功","data"=>"");
    }
    /**
     * 更改购物车商品数量(减1)
     * @author 刘嘉强
     */
    public function reduceNumber($cart_id){
        $where['id'] = $cart_id;
        $result = $this->where($where)->setDec('goods_num',1);
        if (!$result) {
            return array("status"=>0,"message"=>"操作失败","data"=>"");
        }
        return array("status"=>1,"message"=>"操作成功","data"=>"");
    }
    /**
     * 更改购物车商品数量
     * @author 刘嘉强
     */
    public function modifyNumber($cart_id,$num){
        $where['id'] = $cart_id;
        $goods_id = $this->where($where)->getField('goods_id');
        $stock = M('Goods')->where(['id'=>$goods_id])->getField('stock');
        if ($num > $stock) {
            return array("status"=>0,"message"=>"库存不足","data"=>"");
        }
        $result = $this->where($where)->save(['goods_num'=>$num]);
        if ($result===false) {
            return array("status"=>0,"message"=>"操作失败","data"=>"");
        }
        return array("status"=>1,"message"=>"操作成功","data"=>"");
    }
    //添加购物车
    public function addCart($post){
        $post['user_id'] = SessionGet::getInstance('user_id')->get();
        $where['user_id'] = SessionGet::getInstance('user_id')->get();
        $where['goods_id'] = $post['goods_id'];
        M()->startTrans();
        $cart = $this->field('id,goods_num')->where($where)->find();
        if (empty($cart)) {
            $post['create_time'] = time();
            $res = $this->add($post);
            if (!$res) {
                M()->rollback();
                return array("status"=>0,"message"=>"添加失败","data"=>"");
            }
        }else{
            $post['update_time'] = time();
        
            $post['goods_num'] = $post['goods_num']+$cart['goods_num'];
            $res = $this->where(['id'=>$cart['id']])->save($post);
            if ($res === false) {
                M()->rollback();
                return array("status"=>0,"message"=>"添加失败","data"=>"");
            }
        }
        M()->commit();
        return array("status"=>1,"message"=>"添加成功","data"=>$res);
    }
    //添加购物车--多个
    public function addCartAll($post){ 
        
        $goods = $post['goods'];
        M()->startTrans();

        foreach ($goods as $key => $value) {
            $where['user_id'] = SessionGet::getInstance('user_id')->get();
            $where['goods_id'] = $value['goods_id'];
            $stock = M("Goods")->where(['id'=>$value['goods_id']])->getField("stock");
            if ($stock<$value['goods_num']) {
                M()->rollback();
                return array("status"=>0,"message"=>"商品库存不足!","data"=>"");
            }
            $cart = $this->field('id,goods_num')->where($where)->find();
            if (empty($cart)) {
                $data['user_id'] = SessionGet::getInstance('user_id')->get();
                $data['goods_id'] = $value['goods_id'];
                $data['goods_num'] = $value['goods_num'];
                $data['price_new'] = $value['price_new'];
                $data['store_id'] = $value['store_id'];
                $data['create_time'] = time();
                $res = $this->add($data);
                if (!$res) {
                    M()->rollback();
                    return array("status"=>0,"message"=>"添加失败1","data"=>"");
                }
            }else{
                $data['update_time'] = time();
                $data['goods_num'] = $value['goods_num']+$cart['goods_num'];
                $res = $this->where(['id'=>$cart['id']])->save($data);
                if ($res === false) {
                    M()->rollback();
                    return array("status"=>0,"message"=>"添加失败2","data"=>"");
                }
            }
        } 
        M()->commit();
        return array("status"=>1,"message"=>"添加成功","data"=>$res);
    }
}