<?php
/**
 * Created by PhpStorm.
 * User: 王波
 * Date: 2019/1/16
 * Time: 14:14
 */

namespace Common\Logic;
use Common\Logic\AbstractGetDataLogic;
use Common\Model\StoreMemberModel;
use Common\Tool\Tool;
use Think\ModelTrait\Select;
use Think\Cache;
use Common\Model\MemberOrderModel;
use Common\Model\UserModel;
/**
 * 店铺个人分销订单统计逻辑
 * @author 王波
 */
class MemberOrderLogic extends AbstractGetDataLogic
{
    /**
     * 构造方法
     * @param array $data
     */
    public function __construct(array $data, $split= null)
    {
        $this->data = $data;

        $this->modelObj = new MemberOrderModel();

    }
    public function getResult()
    {

    }

    public function getModelClassName()
    {
        return MemberOrderModel::class;
    }

}