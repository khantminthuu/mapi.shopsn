<?php
/**
 * Created by PhpStorm.
 * User: 王波
 * Date: 2019/3/7 0007
 * Time: 13:43
 */

namespace User\Controller;
use Common\Logic\MemberMoneyLogic;
use Common\Logic\UserLogic;
use Common\Logic\StoreWithdrawalLogic;
use Common\Logic\OrderLogic;
use Common\Logic\UserHeaderLogic;
use Common\TraitClass\InitControllerTrait;
use Validate\CheckParam;

class DistributionController
{
    use InitControllerTrait;
    /**
     * 架构方法
     * @param array $args
     */
    public function __construct(array $args = [])
    {

        $this->args = $args;
        $this->_initUser();//#TODO 这里是需要用户必须登录时要初始化这个 否则初始化$this->init();

        $this->logic = new MemberMoneyLogic($args);
    }
    //分销中心 首页
    public function index(){
        //用户信息
        $userLogic = new UserLogic($this->args);
        $res  = $userLogic->getIdentify();
        $this->objController->promptPjax($res, $userLogic->getErrorMessage());
        $user  = $userLogic->getUserByRecommendcode($res);
        $this->objController->promptPjax($user, $userLogic->getErrorMessage());
        //获取可提现佣金
        $commission = $this->logic->getMoneyList();
        $money = $this->logic->getCommissionList();
        //我的团队总人数
        $team = $userLogic->getTeamCount();
        $date['user'] = $user;
        $date['commission'] = $commission;
        $date['no_mention'] = $money['no_mention'];
        $date['withdrawals'] = $money['withdrawals'];
        $date['total'] = $money['total'];
        $date['team'] = $team;
        $this->objController->ajaxReturnData($date);
    }
    //全部订单
    public function order_list(){
        $orderLogic = new OrderLogic($this->args);
        $user = session('distributionByList');
        $this->objController->promptPjax($user, $orderLogic->getErrorMessage());
        $ids = array_column($user,'id');
        $where['user_id'] = array("IN",$ids);
        if(isset($this->args['order_status'])){
            $where['order_status'] = $this->args['order_status'];
        }
        $order = $orderLogic->getOrder($where);
        $this->objController->promptPjax($order, $orderLogic->getErrorMessage());
        $this->objController->ajaxReturnData($order['data'],$order['status'],$order['message']);
    }
    //我的团队
    public function my_team(){
        $userLogic = new UserLogic($this->args);
        $res = $userLogic->getTeamByUser();
        $this->objController->promptPjax($res, $userLogic->getErrorMessage());
        $this->objController->ajaxReturnData($res);
    }
    //提现明细
    public function presentation_details(){
        $storeWithdrawalLogic = new StoreWithdrawalLogic($this->args);
        $res = $storeWithdrawalLogic->getWithdrawalList();
        $this->objController->promptPjax($res, $storeWithdrawalLogic->getErrorMessage());
        $this->objController->ajaxReturnData($res);
    }
    //分销佣金
    public function distribution_commission(){
        //获取可提现佣金
        $money = $this->logic->getMoneyListByStoreId();
        $this->objController->promptPjax($money, $this->logic->getErrorMessage());
        $this->objController->ajaxReturnData($money);
    }
   //申请提现
    public function apply_withdrawal(){
        //获取可提现佣金
        $storeWithdrawalLogic = new StoreWithdrawalLogic($this->args);
        $checkObj = new CheckParam($storeWithdrawalLogic->getRuleByAddApply(), $this->args);//检测参数, $this->logic->getMessageByRegSendSms() 里定义好的检测方法，类似jQuery Validate自动验证方法
        $status = $checkObj->checkParam();//检测参数，类似jQuery Validate自动验证方法
        $this->objController->promptPjax($status, $checkObj->getErrorMessage());//获取失败提示并返回
        $res = $storeWithdrawalLogic->getApplyWithdrawal();
        $this->objController->promptPjax($res, $storeWithdrawalLogic->getErrorMessage());
        $money = $this->logic->getSaveMoney();
        $this->objController->promptPjax($money, $this->logic->getErrorMessage());
        $this->objController->ajaxReturnData($money);
    }
}