<?php
namespace Common\Logic;
use Common\Model\GoodsRestrictionsModel;
/**
 * 逻辑处理层
 *
 */
class GoodsRestrictionsLogic extends AbstractGetDataLogic
{
    /**
     * 构造方法
     * @param array $data
     */
    public function __construct(array $data = [], $split = '')
    {
        $this->data = $data;
        $this->splitKey = $split;
        $this->goodsRestrictions = new GoodsRestrictionsModel();
    }
    /**
     * 返回验证数据
     */
    public function getValidateByLogin()
    {
        $message = [
            'id' => [
                'required' => '必须输入ID',
            ],
        ];
        return $message;
    }
    /**
     * 得到所有 的抢购商品包括首页广告图  商品图片 商品标题 活动抢购状态  抢购价格 原价 购买人数   设置提醒人数
     *
     */
    public function getAllRestrictGoods(){
        $data = $this->goodsRestrictions ->getShopsInfo();
        if (empty($data)){
            return false;
        }
        return $data;
    }

    /**
     * 获取结果
     */
    public function getResult()
    {
    }

    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
     */
    public function getModelClassName() :string
    {
        return BrandModel::class;
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

}
