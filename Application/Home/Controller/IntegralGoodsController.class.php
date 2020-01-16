<?php
namespace Home\Controller;

use Common\TraitClass\InitControllerTrait;
use Validate\CheckParam;
use Common\Logic\IntegralGoodsLogic;
use Common\Logic\GoodsLogic;
use Common\Logic\GoodsImagesLogic;
use Common\Logic\StoreNewsLogic;
use Common\Logic\OrderIntegralLogic;
use Common\Logic\OrderIntegralGoodsLogic;
use Common\TraitClass\IsLoginTrait;
use Common\SessionParse\SessionManager;


class IntegralGoodsController
{
    use InitControllerTrait;
    
    use IsLoginTrait;

    /**
     * 架构方法
     * @param array
     * $args   传入的参数数组
     */
    public function __construct(array $args = [])
    {
    	$this->args = $args;
    	
        $this->init();

        $this->logic = new IntegralGoodsLogic($args);
    }
    /**
     * 获取积分商品列表 
     */
    public function integralGoodsList()
    {
        $ret = $this->logic->getAllIntegralGoods();

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret);
    }
    
    /**
     * 获取积分商品详细信息
     */
    public function integralGoodInfo(){

    	$this->objController->promptPjax($this->logic->checkIdIsNumric(), $this->logic->getErrorMessage());

        $ret = $this->logic->getIntegralGoodInfo();
      
        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());
		
        //商品信息
        $goodsLogic = new GoodsLogic($ret, $this->logic->getSplitKeyByGoods());
        
        $goodsData = $goodsLogic->getGoodsInfoByGoodsId();
        
        $this->objController->promptPjax($goodsData, $this->logic->getErrorMessage());
        
        
        $goodsImageLogic = new GoodsImagesLogic($goodsData, $goodsLogic->getSplitKeyByPId());
        
        $data = $goodsImageLogic->getImagesByGoodsId();
        
        $goods = [
        	'goods' => array_merge($goodsData, $ret),
        	'images' => $data
        ];
        
        $this->objController->ajaxReturnData($goods);
    }
    /**
     * 确认订单页面 获取积分商品详细信息
     */
    public function integralGoodDetails(){

    	$this->isLogin();
    	
        $checkObj = new CheckParam($this->logic->getValidateBydetail(), $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());
		
        SessionManager::REMOVE_ALL();
        
        $ret = $this->logic->checkIsConvertibility();
        
       
        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());
        
        $integral = $this->logic->getIntegralData();
       
        $integral['goods_num'] = $this->args['goods_num'];
        
        //商品信息
        $goodsLogic = new GoodsLogic($integral, $this->logic->getSplitKeyByGoods());
        
        $goodsData = $goodsLogic->getGoodsInfoByGoodsId();
       	
        $this->objController->promptPjax($goodsData, $goodsLogic->getErrorMessage());
        
        
        $goodsImageLogic = new GoodsImagesLogic($goodsData, $goodsLogic->getSplitKeyByPId());
        
        $data = $goodsImageLogic->getThumbImagesByGoodsId();
      
        $storeLogic = new StoreNewsLogic($goodsData, $goodsLogic->getSplitKeyByStore());
        
        $storeInfo = $storeLogic->getStoreTitileAndPic();
        
        $goodsData['goods_num'] = $this->args['goods_num'];
        
        $goodsData['price_member'] = $integral['money'];
        
        $goodsData['integral'] = $integral['integral'];
        
        $sessionManager = new SessionManager($goodsData);
        
        $sessionManager->sessionBuyNowByIntegralGoods();
        
        $this->objController->ajaxReturnData(array_merge($storeInfo, $data, $goodsData, $integral));
    }
    
   
}