<?php
namespace Home\Controller;

use Common\TraitClass\InitControllerTrait;
use Validate\CheckParam;
use Common\Logic\PanicLogic;
use Common\Logic\GoodsImagesLogic;
use Common\Logic\StoreLogic;
use Common\SessionParse\SessionManager;

class PanicController
{
    use InitControllerTrait;

    /**
     * 架构方法
     * @param array
     * $args   传入的参数数组
     */
    public function __construct(array $args = [])
    {
        $this->init();

        $this->args = $args;

        $this->logic = new PanicLogic($args);
    }
    /**
     * 得到抢购商品信息数据
     *
     */
    public function getPanicGoods()
    {
        $ret = $this->logic->get_Panic_Goods();

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret);
    }

    /**
     * 抢购商品详情
     */
    public function panicGoodsDetail(){
        $ret = $this->logic->getPanicDetail();

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret);
    }

    /**
     * 抢购商品立即购买生成 session相关数据
     */
    public function panicCartGoodsDetail() :void
    {
        //检测传值                  //检测方法
        $checkObj = new CheckParam($this->logic->getValidateByDetail(), $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());

        $ret = $this->logic->getGoodsDetailByOrder();

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        //获取商品图片
        $goodsImageLogic = new GoodsImagesLogic($ret, $this->logic->getSplitKeyByPId());

        $image = $goodsImageLogic->getThumbImagesByGoodsId();

        //获取店铺信息
        $storeLogic = new StoreLogic($ret, $this->logic->getSplitKeyByStore());

        $store = $storeLogic->getInfo();

        $this->objController->promptPjax($store, $storeLogic->getErrorMessage());

        $ret['goods_num'] = $this->args['goods_num'];

        $sessionOrder = new SessionManager($ret);

        $sessionOrder->sessionBuyNow();

        $this->objController->ajaxReturnData([
            'goods'	=> $ret,
            'store' => $store,
            'image' => $image
        ]);
    }
}