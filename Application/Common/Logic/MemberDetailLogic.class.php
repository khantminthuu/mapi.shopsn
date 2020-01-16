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
use Common\Model\MemberDetailModel;
use Admin\Model\UserModel;
/**
 * 店铺个人分销详情逻辑
 * @author 王波
 */
class MemberDetailLogic extends AbstractGetDataLogic
{
    /**
     * 构造方法
     * @param array $data
     */
    public function __construct(array $data, $split= null)
    {
        $this->data = $data;

        $this->modelObj = new MemberDetailModel();

    }
    public function getResult()
    {

    }

    public function getModelClassName()
    {
        return MemberDetailModel::class;
    }

}