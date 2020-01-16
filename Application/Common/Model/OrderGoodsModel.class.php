<?php
namespace Common\Model;
use Think\Model;
use Common\Tool\Tool;
use Common\Model\SpecGoodsPriceModel;
use Think\SessionGet;
// +----------------------------------------------------------------------
// | 订单数量模型
// +----------------------------------------------------------------------
// | Another ：王强
// +----------------------------------------------------------------------

class OrderGoodsModel extends BaseModel
{
    private static $obj;

	public static $id_d;

	public static $orderId_d;

	public static $goodsId_d;

	public static $goodsNum_d;

	public static $goodsPrice_d;

	public static $status_d;

	public static $spaceId_d;

	public static $userId_d;

	public static $comment_d;

	public static $over_d;

	public static $wareId_d;

	public static $storeId_d;	//店铺【编号】


	public static $freightId_d;	//模板【编号】

    
    public static function getInitnation()
    {
        $name = __CLASS__;
        return self::$obj = !(self::$obj instanceof $name) ? new self() : self::$obj;
    }
    /**
     * 根据订单编号查询商品编号  
     */
    public function getGoodsIdByOrderId($orderId, $field = 'goods_id')
    {
        if (empty($orderId) || !is_numeric($orderId))
        {
            return array();
        }
        
        return $this->field($field)->where('order_id = %s', $orderId)->select();
    }
    
    /**
     * {@inheritDoc}
     * @see \Think\Model::add()
     */
    
    public function add($data='', $options=array(), $replace=false)
    {
        if (empty($data))
        {
            return false;
        }
        $data = $this->create($data);
        
        return parent::add($data, $options, $replace);
    }
    
    /**
     * 根据父类表信息查询数据 ，传递给商品表 
     */
    public function getGoodsInfoByOrder(array $data)
    {
        if (empty($data))
        {
            return array();
        }
        
        //整合编号
        $orderIds = Tool::characterJoin($data, 'order_id');
       
        $orderGoods = $this->field('order_id,goods_id,goods_num')->where('order_id in ('.$orderIds.')')->order('order_id DESC')->select();
       
        if (empty($orderGoods))
        {
            return array();
        }
        
        $parseOrder = array();
        
        foreach ($orderGoods as $value)
        {
            if (!isset($parseOrder[$value['order_id']]))
            {
                $parseOrder[$value['order_id']] = $value;
            }
            else
            {
                if (strpos($parseOrder[$value['order_id']]['goods_id'], ',') === false)
                {
                    $goodsId = $parseOrder[$value['order_id']]['goods_id'];
                }
                $parseOrder[$value['order_id']]['goods_id'] .= ','.$value['goods_id'];
                $parseOrder[$value['order_id']]['goods_num'] .= ','.$value['goods_id'].':'.$value['goods_num'];
            }
        }
        
        foreach ($parseOrder as $key => &$value)
        {
            if (strpos($value['goods_id'], ',') !== false)
            {
                $id = $value['goods_num']; 
                
                $newId = $goodsId.':'.$id;
                
                $value['goods_num'] = $newId;
            }
        }
        return $parseOrder;
    }
    
    /**
     * 获取商品编号 
     */
    public function getGoodsId($data, $field, $filter = FALSE)
    {
        if (empty($data['id']) || empty($field))
        {
            return array();
        }
        //整合编号
        return $orderGoods = $this->field($field, $filter)->where('order_id in ('.$data['id'].')')->order('order_id DESC')->select();
    }
    
    /**
     * 删除用户的购买记录 
     */
    public function deleteOrderGoodsByUserId(array $order, $id)
    {
        if (empty($order) || !is_array($order) ||empty($id)) {
            return false;
        }
        
        $ids = Tool::characterJoin($order, $id);
        
        if (empty($ids)) {
            return false;
        }
        
        return $this->delete(array(
                'where' => array(self::$orderId_d => array('in', $ids))
        ));
    }
    /**
     * 增加订单商品信息
     *
     */
    public function addGoodsInfo($data){
        return $this->add($data);
    }

    public function addOrderGoodInfo($data){
        return $this->add($data);
    }
    //获取订单商品
    public function getGoodsByOrder($order){
        $spec = new SpecGoodsPriceModel();
        foreach ($order as $key => $value) {
            $where['order_id'] = $value['id'];
            $count = $this->where($where)->sum("goods_num");
            $orderGoods = $this->field("id,goods_id,goods_num")->where($where)->select();
            foreach ($orderGoods as $k => $v) {
                $goods = M('Goods')->field("id,p_id,title")->where(['id'=>$v['goods_id']])->find();
                $orderGoods[$k]['pic_url'] = M('GoodsImages')->where(['goods_id'=>$goods['p_id']])->getField("pic_url");
                $orderGoods[$k]['spec'] = $spec->getGoodSpe($v['goods_id']);
                $orderGoods[$k]['title'] = $goods['title'];
            } 
            $order[$key]['goods'] = $orderGoods;
            $order[$key]['count'] = $count;
        }
        return $order;
    }
    //获取订单商品
    public function getGoodsByOrderOne($order){
        $where['order_id'] = $order['id'];
        $goodsCacheData = [];
        $orderGoods = $this->field("id,goods_id,goods_num,goods_price, status")->where($where)->select();
        foreach ($orderGoods as $k => $v) {
            $goods = M('Goods')->field("id,p_id,title")->where(['id'=>$v['goods_id']])->find();
            $orderGoods[$k]['pic_url'] = M('GoodsImages')->where(['goods_id'=>$goods['p_id']])->getField("pic_url");
            $orderGoods[$k]['title'] = $goods['title'];
            $goodsCacheData[$goods['id']] += $v ['goods_num'];
        }
        SessionGet::getInstance('goods_id_by_user',$goodsCacheData)->set();
        $order['goods'] = $orderGoods;
        return $order;
    }
    //获取订单商品
    public function getGoodsByOrderByWhere($where,$field){
        $spec = new SpecGoodsPriceModel();       
        $orderGoods = $this->field($field)->where($where)->find();
        $goods = M('Goods')->field("id,p_id,title")->where(['id'=>$orderGoods['goods_id']])->find();
        if($goods['p_id'] == 0){
            $orderGoods['pic_url'] = M('GoodsImages')->where(['goods_id'=>$goods['id'],"is_thumb"=>1])->getField("pic_url"); 
        }else{
            $orderGoods['pic_url'] = M('GoodsImages')->where(['goods_id'=>$goods['p_id'],"is_thumb"=>1])->getField("pic_url");
        }
        
        $orderGoods['spec'] = $spec->getGoodSpe($orderGoods['goods_id']);
        $orderGoods['title'] = $goods['title'];
        return $orderGoods;
    }
}