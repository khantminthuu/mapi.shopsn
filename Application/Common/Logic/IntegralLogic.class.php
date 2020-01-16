<?php

namespace Common\Logic;

use Common\Model\IntegralUseModel;

/**
 * 用户积分逻辑处理层
 * @author 薛松
 * @updated 2018-01-05 18:27
 */
class IntegralLogic extends AbstractGetDataLogic
{
	/**
	 * 构造方法
	 *
	 * @param array $data
	 */
	public function __construct(array $data = [], $split = '')
	{
		$this->data = $data;
		$this->splitKey = $split;
		$this->modelObj = new IntegralUseModel();
	}
	
	public function getResult()
	{
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
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
            IntegralUseModel::$id_d,
		];
	}
    /**
     * @name 会员积分明细日志
     * 
     * @des 会员积分明细日志
     * @updated 2018-01-06
     */
    public function integralLog()
    {
        $userId = session('user_id');

        //#TODO 这里是查询条件
        $searchTemporary = [
            IntegralUseModel::$userId_d => $userId,
        ];

        //#TODO 这里是要查询的字段如果不传的话默认为表中的所有字段
        $searchField = "integral,order_id, FROM_UNIXTIME(trading_time,'%Y-%m-%d') as trading_time, remarks, type, status";

        //#TODO 这里是按照什么排序查询，如果不传默认为ID DESC排序
        $searchOrder = 'trading_time DESC';

        //#TODO 调用通用的获取列表的接口并返回数据  data=>['countTotal'=>2, 'records'=>[.....]]
        $page = empty($this->data['page'])?0:$this->data['page'];
        $reData['records'] = $this->modelObj->field($searchField)->where($searchTemporary)->page($page.',10')->order($searchOrder)->select();
        
        $count =  $this->modelObj->where($searchTemporary)->count();
        $Page = new \Think\Page($count,10);
        $show = $Page->show();
        $totalPages = $Page->totalPages;
        $reData['count'] = $count;
        $reData['totalPages'] = $totalPages;
        $reData['page_size'] = 10;
        return $reData;
    }
	/**
	 * @name 会员积分规则逻辑
	 * 
	 * @des 会员积分规则逻辑
	 * @updated 2018-01-05
	 */
	public function rule()
	{
//        $retData['details'] = '积分规则详情';
        $retData['img_url'] = C('img_url');
        $retData['rule']    = M('user_level')->where(['status'=>'1'])->select();
        return $retData;
	}

//    /**
//     * @name 会员积分统计逻辑
//     * 
//     * @des 会员积分统计逻辑
//     * @updated 2018-01-06
//     */
//    public function total()
//    {
//        $userDe = M('user')->where(['id'=>session('user_id')])->find();
//        //可用积分
//        $retData['integra'] = $userDe['integral'];
//
//        //已用积分
//        $retData['integra_use'] = $userDe['integral_use'];
//
//        //总计积分
//        $total_integra = bcadd($userDe['integral'], $userDe['integral_use']);
//        $retData['total_integra'] = $total_integra;
//
//        //当前等级
//        $userLevelRule = $this->rule()['rule'];//用户等级规则
//        $levelData = $this->scoreSearch($total_integra, $userLevelRule);//查找当前用户积分在哪个等级区间中
//        $retData['level_name']  =   $levelData[0]['level_name'];
//
//        //再积累多少积分
//        $retData['next_level_integral']  = bcsub($levelData[0]['integral_big'], $total_integra);
//
//        //下一个等级
//        $retData['next_level_name']  = $this->nextLevel($levelData[0]['level_name'], $userLevelRule);
//
//        return $retData;
//    }
//    /**
//     * 二分法查找
//     *
//     * @param int $score 积分
//     * @param array $filter 积分规则
//     *
//     * @return array $filter
//     */
//    public function scoreSearch($score, $filter)
//    {
//        $half = floor(count($filter) / 2); // 取出中间数
//
//        // 判断积分在哪个区间
//        if ($score <= $filter[$half - 1]['integral_big']) {
//            $filter = array_slice($filter, 0 , $half);
//        } else {
//            $filter = array_slice($filter, $half , count($filter));
//        }
//
//        // 继续递归直到只剩一个元素
//        if (count($filter) != 1) {
//            $filter = $this->scoreSearch($score, $filter);
//        }
//        return $filter;
//    }
//    /**
//     * 查找下一个等级
//     */
//    public function nextLevel($key, $array)
//    {
//      foreach ($array as $k => $value){
//          if($value['level_name'] == $key){
//              $next = $array[$k + 1];
//              return $next['level_name'];
//          }
//      }
//    }
}
