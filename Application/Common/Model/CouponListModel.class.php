<?php
namespace Common\Model;

/**
 * 优惠券模型
 */
class CouponListModel extends BaseModel
{
	public static $id_d;	//表id

	public static $cId_d;	//优惠券 对应coupon表id

	public static $type_d;	//发放类型 1 按订单发放 2 注册 3 邀请 4 按用户发放

	public static $userId_d;	//用户id

	public static $orderId_d;	//订单id

	public static $useTime_d;	//使用时间

	public static $code_d;	//优惠券兑换码

	public static $sendTime_d;	//发放时间

	public static $status_d;	//0未使用1已使用


	public static function getInitnation()
	{
		$name = __CLASS__;
		return self::$obj = !(self::$obj instanceof $name) ? new self() : self::$obj;
	}
	/**
	 * @name 我的优惠券
	 * 
	 * @des 我的优惠券：未使用，已使用，已过期
	 * @updated 2017-12-16 22:14
	 */
	public function myCouponLists($params)
	{
		$condition['user_id'] = $params['user_id'];
		$time = time();
		$where['a.user_id'] = $params['user_id'];
		if (!empty($params['status']) && $params['status'] == 1) {//status=1为已使用
			$where['a.order_id'] = ['NEQ', '0'];
		}
		if (!empty($params['status']) && $params['status'] == 2) {//status=2已过期
			$where['a.order_id'] = ['EQ', '0'];
			$where['b.use_end_time'] = ['LT', $time];
		}
		if (!empty($params['status']) && $params['status'] == 3) {//status=3为未使用
			$where['a.order_id'] = ['EQ', '0'];
			$where['b.use_end_time'] = ['GT', $time];
		}
		$lists = $this->alias("a")
			->field('a.id, b.name, b.money, b.condition, b.use_start_time, b.use_end_time')
			->join('__COUPON__ as b on b.id = a.c_id', 'LEFT')
			->where($where)
			->page($params['page'])
			->select();
		//所有的优惠券统计
		$count = $this->alias("a")
			->join('__COUPON__ as b on b.id = a.c_id', 'LEFT')
			->where(['a.user_id' => $params['user_id']])
			->count();
		//已使用优惠券统计
		$used_count = $this->alias("a")
			->join('__COUPON__ as b on b.id = a.c_id', 'LEFT')
			->where(['a.user_id' => $params['user_id'], 'a.order_id' => ['NEQ', '0']])
			->count();
		//未使用优惠券统计
		$not_used_count = $this->alias("a")
			->join('__COUPON__ as b on b.id = a.c_id', 'LEFT')
			->where(['a.user_id' => $params['user_id'], 'a.order_id' => ['EQ', '0'], 'b.use_end_time' => ['GT', $time]])
			->count();
		//已过期优惠券统计
		$expired_count = $this->alias("a")
			->join('__COUPON__ as b on b.id = a.c_id', 'LEFT')
			->where(['a.user_id' => $params['user_id'], 'a.order_id' => ['EQ', '0'], 'b.use_end_time' => ['LT', $time]])
			->count();
		foreach ($lists as $key => $item) {
			$lists[$key]['use_start_time'] = date('Y.m.d', $item['use_start_time']);
			$lists[$key]['use_end_time'] = date('Y.m.d', $item['use_end_time']);
		}
		return [
			'countTotal' => [
				'count'    => $count,
				'not_used' => $not_used_count,
				'used'     => $used_count,
				'expired'  => $expired_count,
			],
			'records'    => $lists,
		];
	}

    /**
     * 使用优惠券
     *
     */
	public function useCoupon($cid ,$uid,$orderId){
        $coup['order_id']=$orderId;
        $coup['use_time']=time();
        $coup['status']=1;
        $result = $this->where(array('c_id'=>$cid,'user_id'=>$uid))->save($coup);
        if ($result){
            return true;
        }
        return false;
    }
}