<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/25 0025
 * Time: 15:56
 */

namespace Common\Logic;

use Common\Logic\AbstractGetDataLogic;
use Common\Model\StoreWithdrawalModel;
use Common\Model\StoreModel;
use Common\Tool\Tool;
use Think\Cache;

/**
 * 系统配置
 * @author Administrator
 */
class StoreWithdrawalLogic extends AbstractGetDataLogic
{

    /**
     * 架构方法
     */
    public function __construct(array $data = [], $splitKey = null)
    {
        $this->data = $data;


        $this->modelObj = StoreWithdrawalModel::getInitnation();

        $this->splitKey = $splitKey;
    }
    /**
     * 获取分类
     *
     * {@inheritdoc}
     *
     * @see \Common\Logic\AbstractGetDataLogic::getResult()
     */
    public function getResult()
    {
    }


    /**
     * 返回模型类名
     *
     * @return string
     */
    public function getModelClassName(): string
    {
        return StoreWithdrawalModel::class;
    }
    /**
     * @name 申请提现验证规则
     *
     * @des 申请提现验证规则
     * @updated 2017-12-21
     */
    public function getRuleByAddApply()
    {
        $message = [
            'type'    => [
                'required'  => '请输入提现类型',
            ],
            'store_id'    => [
                'required'  => '请输入店铺id',
            ],
            'account' => [
                'required'  => '请输入提现账号',
            ],
            'money'   => [
                'required'  => '请输入提现金额',
            ],
        ];
        return $message;
    }
    public  function getWithdrawalList(){
        $post = $this->data;
        $storeModel = StoreModel::getInitnation();
        $field = "store_id,type,money,FROM_UNIXTIME(add_time,'%Y-%m-%d %H:%i:%s') as add_time,status";
        $where['user_id'] = $_SESSION['user_id'];
        if(isset($post['status'])){
            $where['status'] = $post['status'];
        }
        $list = $this->modelObj->field($field)->where($where)->order('add_time DESC')->select();
        if(empty($list)){
            $this->errorMessage = "暂无数据";
            return false;
        }
        foreach($list as $key=>$value){
            $list[$key]['shop_name'] = $storeModel->where(['id'=>$value['store_id']])->getField('shop_name');
        }
        return $list;
    }
    //申请提现
    public function getApplyWithdrawal(){
        $post = $this->data;
        $post['user_id'] = $_SESSION['user_id'];
        $post['add_time'] = time();
        $this->modelObj->startTrans();
        $res = $this->modelObj->add($post);
        if(!$res){
            $this->errorMessage = "操作失败";
            $this->modelObj->rollback();
            return false;
        }
        return $res;
    }
}