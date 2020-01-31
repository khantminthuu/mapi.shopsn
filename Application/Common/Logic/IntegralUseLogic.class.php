<?php
declare(strict_types = 1);
namespace Common\Logic;
use Common\Model\UserModel;
use Common\Model\OrderModel;
use Common\Model\UserHeaderModel;
use Common\Model\IntegralUseModel;
use Common\Model\IntegralDailyModel;
use Think\Cache;
use Think\SessionGet;
use Think\Log;
use Common\SessionParse\SessionManager;
/**
 * 逻辑处理层
 * 
 */
class IntegralUseLogic extends AbstractGetDataLogic
{
	
	/**
	 * 支出
	 * @var integer
	 */
	const SPENDING = 0;
	
	/**
	 * 收入
	 * @var integer
	 */
	const INCOME = 1;
	
	
	/**
	 *
	 * @var integer
	 */
	private $integralShopping = 0;
	
	/**
	 *
	 * @var integer
	 */
	private $totalIntegral = 0;
	
	public function getTotailIntegral() :int
	{
		return $this->totalIntegral;
	}
	
	public function setAlreadyIntegral(int $integral) :void
	{
		$this->integralShopping = $integral;
	}
    /**
     * 构造方法
     * @param array $data
     */
    public function __construct(array $data = [], $split = '')
    {
        $this->data = $data;
        $this->splitKey = $split;
        $this->modelObj = new IntegralUseModel();
        $this->integralObj = new IntegralDailyModel();

    }
    /**
     * 返回验证数据
     */
    public function getValidateByLogin() :array
    {
        return [
            'integral' => [
                'required' => '必须输入本次使用积分',
            ],
            'goods_id' => [
                'required' => '必须输入商品ID',
            ],
            'address_id' => [
                'required' => '请选择收货地址',
            ],
            'platform' => [
                'required' => '请写明下单平台',
            ],
            'store_id' => [
                'required' => '请标明商品所属店铺ID',
            ],
        ];
    }
    public function getValidateByOrder() :array
    {
        return [
            'orderId' => [
                'required' => '获取订单信息失败',
            ],
        ];
    }


    /*
    khantminthu
    */
     public function getValidateUserId()
    {
        return [
            'UserId' => [
                'required' => '需要用户登录',
            ],
        ];
    }

    public function getDailyBonus()
    {
        $userId = SessionGet::getInstance('user_id')->get();

        $where['user_id'] = $userId;

        return $arr = $this->integralObj->where($where)->find();
    }

    public function addUserIntegral()
    {
        $userId = SessionGet::getInstance('user_id')->get();

        $dailyIntegral = $this->integralObj->getDailyInte($userId);

        $userIntegral = $this->modelObj->getIntegral($userId);

       $userIntegral['integral'] = $userIntegral['integral']+ $dailyIntegral['integral'];

       $this->modelObj->save($userIntegral);
    }
    
    public function getDayInte()
    {
        $time = (date('H:i:s'));

        $userId = SessionGet::getInstance('user_id')->get();

        $getUser = $this->integralObj->where(['user_id'=>$userId])->field('user_id')->find();

         $time = $this->timeDelay();

        if(empty($getUser)){

            $this->integralObj->addDaily($this->NewUser($userId , $time));
            $this->addUserIntegral();
            
        }else{
            if($time < 24){
                return $this->getTimeDelay();
            }
            $this->integralObj->saveArr($this->IsSesUser($userId , $time) , $userId);
            $this->addUserIntegral();
        }
       
    }

    public function IsSesUser($id , $time)
    {
        $getUser = $this->integralObj->where(['user_id'=>$id])->find();
        $arr['user_id'] = $id;
        $arr['check_in'] = $getUser['check_in']+1;
        $integral = $getUser['integral'];
        switch($integral){
            case 0.1: $integral = 0.2 ; 
            break;
            case 0.2: $integral = 0.3;
            break;
            case 0.3: $integral = 0.4;
            break;
            case 0.4: $integral = 0.5;
            break;
            case 0.5: $integral = 0.6;
            break;
            case 0.6: $integral = 5.0;
            break;
        }
        $arr['integral'] = $integral;
        $arr['time'] = $time;
        return $arr;
    }

    public function NewUser($id , $time)
    {
        return $arr = array(
            'user_id' => $id,
            'check_in' => 1,
            'Integral' => 0.1,
            'time' => $time
        );
    }

    public function timeDelay()
    {
        $userId = SessionGet::getInstance('user_id')->get();

        $getTime = $this->integralObj->getUser($userId);
        
        $time = (date('H:i:s'));
        
        return $timestamp = $time-$getTime;
    }

    public function getTimeDelay()
    {
        $delay = ( $this->timeDelay()).'Hours remaining';

        return $delay;

    }
    /*End*/
    /**
     * 获取结果
     */
    public function getResult()
    {
    }

    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getModelClassName() :string
     */
    public function getModelClassName() :string
    {
        return IntegralUseModel::class;
    }

    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::hideenComment()
     */
    public function hideenComment() :array
    {
        return [

        ];
    }
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::likeSerachArray()
     */
    public function likeSerachArray() :array
    {
        return [
            UserModel::$userName_d,
        ];
    }
    
    /**
     * 获取积分列表
     */
    public function getIntegralList() :array
    {
    	$this->searchTemporary = [
    		IntegralUseModel::$userId_d => SessionGet::getInstance('user_id')->get(),
    	];
    	
    	$data = $this->getParseDataByList();
    	
    	return $data;
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getTableColum()
     */
    protected function getTableColum() :array
    {
    	return [
    		IntegralUseModel::$id_d,
    		IntegralUseModel::$orderId_d,
    		IntegralUseModel::$integral_d,
    		IntegralUseModel::$type_d,
    		IntegralUseModel::$tradingTime_d
    	];
    }
    
    /**
     * 获取积分列表
     */
    public function getIntegralListCache() :array
    {
    	$key = $this->data['p'].'integral_cache_list152';
    	
    	$cache = Cache::getInstance('', ['expire' => 60]);
    	
    	$data = $cache->get($key);
    	
    	if (!empty($data['data'])) {
    		return $data;
    	}
    	
    	$data = $this->getIntegralList();
    	
    	if (count($data['data']) === 0) {
    		$this->errorMessage = '暂无数据';
    		return [];
    	}
    	
    	$cache->set($key, $data);
    	
    	return $data;
    }
    
    /**
     * 获取商品关联字段
     * @return string
     */
    public function getSplitKeyByOrderId() :string
    {
    	return IntegralUseModel::$orderId_d;
    }
    
    /**
     * 添加积分记录
     * @return boolean
     */
    public function addIntegral(int $payIntegral):bool
    {
    	$data = $this->data;
    	
    	//增加积分
    	
    	$time = time();
    	
    	$integral_data = [];
    	
    	$integral = 0;
    	
    	$i = 0;
    	
    	$userId = SessionGet::getInstance('user_id')->get();
    	
    	$totalIntegral = 0;

    	foreach($data as $k  => $vo){

           $integral = (int)($vo['total_money'] / $payIntegral);
           if ($integral <= 0) {
             continue;
         }

         $userIntegral = $this->modelObj->where(['user_id'=>$userId])->order('id DESC')->getField('integral');
         if(!empty($userIntegral)){
            $integral_data = [
                'user_id' => $userId,
                'integral' => $integral+$userIntegral,
//                    'order_id'  => $vo['id'],        报错修改   meng
                'order_id'  => $vo['order_id'],
                'trading_time' =>$time,
                'remarks' => '订单积分',
                'type'  => 1,
                'changes_integral'=>$integral,
            ];
        }else{
            $integral_data = [
//                    'user_id' => $vo['user_id'],      报错修改   meng
                'user_id' => $userId,
                'integral' => $integral,
//                    'order_id'  => $vo['id'],       报错修改   meng
                'order_id'  => $vo['order_id'],
                'trading_time' =>$time,
                'remarks' => '订单积分',
                'type'  => 1,
                'changes_integral'=>$integral,
            ];
        }
        $status = $this->modelObj->add($integral_data);
        if(!$status){
            $day = date('y_m_d');

            Log::write('积分操作错误'.$time.' -- '.print_r($integral_data, true).'--'.$this->modelObj->getLastSql(), Log::ERR, '', './Log/order/aplipaySerial_'.$day.'.txt');

            $this->modelObj->rollback();

            return false;
        }
        $totalIntegral += $integral;
        $i++;
    }

    $this->totalIntegral = $totalIntegral;

    return true;
}
    /**
     * 添加积分记录
     * @return boolean
     */
    public function addIntegrals(int $payIntegral):bool
    {
        $data = $this->data;

        //增加积分

        $time = time();

        $integral_data = [];

        $integral = 0;

        $i = 0;
        $orderModel = new OrderModel();
        $where['id'] = array("IN",$data);
        $order = $orderModel->field('id,price_sum,user_id')->where($where)->select();
//        $userId = SessionGet::getInstance('user_id')->get();

        $totalIntegral = 0;

        foreach($order as $k  => $vo){

            $integral = (int)($vo['price_sum'] / $payIntegral);
            if ($integral <= 0) {
                continue;
            }
            $userIntegral = $this->modelObj->where(['user_id'=>$vo['user_id']])->order('id DESC')->getField('integral');
            if(!empty($userIntegral)){
                $integral_data = [
                    'user_id' => $vo['user_id'],
                    'integral' => $integral+$userIntegral,
                    'order_id'  => $vo['id'],
                    'trading_time' =>$time,
                    'remarks' => '订单积分',
                    'type'  => 1,
                    'changes_integral'=>$integral,
                ];
            }else{
                $integral_data = [
                    'user_id' => $vo['user_id'],
                    'integral' => $integral,
                    'order_id'  => $vo['id'],
                    'trading_time' =>$time,
                    'remarks' => '订单积分',
                    'type'  => 1,
                    'changes_integral'=>$integral,
                ];
            }
            $status = $this->modelObj->add($integral_data);
            if(!$status){
                $day = date('y_m_d');

                Log::write('积分操作错误'.$time.' -- '.print_r($integral_data, true).'--'.$this->modelObj->getLastSql(), Log::ERR, '', './Log/order/aplipaySerial_'.$day.'.txt');

                $this->modelObj->rollback();

                return false;
            }
            $totalIntegral += $integral;
            $i++;
        }
        $this->totalIntegral = $totalIntegral;
        return true;
    }
    /**
     * 获取积分字段
     * @return unknown
     */
    public function getSplitKeyByIntegral ()
    {
    	return IntegralUseModel::$integral_d;
    }
    /**
     * @return bool
     */
    public function addIntegralLog() :bool
    {
    	$status = $this->addData();
    	
    	if (!$this->traceStation($status)) {
    		$this->errorMessage = '积分';
    		return false;
    	}
    	
    	$this->modelObj->commit();
    	
    	return true;
    }
    
    
    /**
     *
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getParseResultByAdd()
     */
    protected function getParseResultByAdd() :array
    {
    	$data = [];
    	
    	$userId = SessionGet::getInstance('user_id')->get();
    	
    	
    	$orderData = SessionManager::GET_ORDER_DATA();
    	
    	$data[IntegralUseModel::$userId_d] = $userId;
    	
    	$data[IntegralUseModel::$integral_d] = $this->integralShopping;
    	
    	$data[IntegralUseModel::$orderId_d] = $orderData[0]['order_id'];
    	
    	$data[IntegralUseModel::$tradingTime_d] = time();
    	
    	$data[IntegralUseModel::$remarks_d] = "积分支付";
    	
    	$data[IntegralUseModel::$type_d] = self::SPENDING;
    	
    	$data[IntegralUseModel::$status_d] = 1;
    	
    	return $data;
    }
    
    
}
