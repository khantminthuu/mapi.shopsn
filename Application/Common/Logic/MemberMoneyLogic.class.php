<?php
/**
 * Created by PhpStorm.
 * User: 王波
 * Date: 2019/1/16
 * Time: 14:14
 */

namespace Common\Logic;
use Common\Logic\AbstractGetDataLogic;
use Common\Tool\Tool;
use Think\ModelTrait\Select;
use Think\Cache;
use Common\Model\MemberMoneyModel;
use Common\Model\StoreModel;
use Admin\Model\UserModel;
/**
 * 店铺个人分销详情逻辑
 * @author 王波
 */
class MemberMoneyLogic extends AbstractGetDataLogic
{
    /**
     * 构造方法
     * @param array $data
     */
    public function __construct(array $data, $split= null)
    {
        $this->data = $data;

        $this->modelObj = new MemberMoneyModel();

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
        return MemberMoneyModel::class;
    }
    //获取可提现金额
    public function getMoneyList(){
        $post = $this->data;
        $data = $this->modelObj->field('store_id,SUM(money) as money')->where(['user_id'=>$_SESSION['user_id'],'status'=>0])->group('store_id')->select();
        $storeModel = new StoreModel();
        foreach($data as $key=>$value){
            $data[$key]['shop_name'] = $storeModel->where(['id'=>$value['store_id']])->getField('shop_name');
        }
        return $data;
    }
    //获取可提现金额
    public function getMoneyListByStoreId(){
        $post = $this->data;
        if(empty($post['store_id'])){
            $this->errorMessage = "参数错误";
            return false;
        }
        $where['user_id'] = $_SESSION['user_id'];
        $where['store_id'] = $post['store_id'];
        $no_mention = $this->modelObj->where(['user_id'=>$_SESSION['user_id'],'status'=>0,'store_id'=>$post['store_id']])->sum('money');
        $withdrawals = $this->modelObj->where(['user_id'=>$_SESSION['user_id'],'status'=>1,'store_id'=>$post['store_id']])->sum('money');
        $total = $this->modelObj->where($where)->sum('money');
        $data['no_mention'] = $no_mention;
        $data['withdrawals'] = $withdrawals;
        $data['total'] = $total;
        return $data;
    }
    //获取金额
    public function getCommissionList(){
        $post = $this->data;
        $no_mention = $this->modelObj->where(['user_id'=>$_SESSION['user_id'],'status'=>0])->sum('money');
        $withdrawals = $this->modelObj->where(['user_id'=>$_SESSION['user_id'],'status'=>1])->sum('money');
        $total = $this->modelObj->where(['user_id'=>$_SESSION['user_id']])->sum('money');
        $data['no_mention'] = $no_mention;
        $data['withdrawals'] = $withdrawals;
        $data['total'] = $total;
        return $data;
    }
    //修改状态
    public function getSaveMoney(){
        $post = $this->data;
        $where['user_id']  = $_SESSION['user_id'];
        $where['store_id'] = $post['store_id'];
        $where['status']   = 0;
        $data['status'] = 1;
        $res = $this->modelObj->where($where)->save($data);
        if($res === false){
            $this->errorMessage = "操作失败";
            $this->modelObj->rollback();
        }
        $this->modelObj->commit();
        return $res;
    }
}