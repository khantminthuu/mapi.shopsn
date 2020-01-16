<?php
declare(strict_types = 1);

namespace Home\Controller;

use Common\TraitClass\InitControllerTrait;
use Validate\CheckParam;
use Common\Logic\GoodsCartLogic;
use Common\TraitClass\IsLoginTrait;
use Common\Logic\GoodsLogic;
use Common\Tool\Tool;
use Common\Logic\GoodsImagesLogic;
use Common\Logic\StoreLogic;
use Common\SessionParse\SessionManager;
use Common\Tool\Extend\ArrayChildren;

class GoodsCartController
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
    	
    	$this->_initUser();
        $this->objController->promptPjax(IS_POST, '请求无效'); 
       
        $this->logic = new GoodsCartLogic($args);
    }
    /**
     * 加入购物车
     */
    public function addGoodToCart()
    {
        //检测传值                  //检测方法
        $checkObj = new CheckParam($this->logic->getMessageNotice(), $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());

        //判断库存是否足够
        $goodsLogic = new GoodsLogic($this->args);
        
        $this->objController->promptPjax($goodsLogic->checkStockByGoodsDetail(), $goodsLogic->getErrorMessage());
        
        $ret = $this->logic->addCar(); 

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }

    /**
     * 显示购物车商品数量
     *
     */
    public function getCount(){
        $ret = $this->logic->getGoodsCount();

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret);
    }

    /***
     * 获取购物车购买商品的信息
     */
    public function userBuyCartGoods(){ 
        //检测传值                  //检测方法
        $checkObj = new CheckParam($this->logic->getUserBuyCarGoodsInfo(), $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());

        $ret = $this->logic->getGoodsCartInfoCache();
		
        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());
		
        //获取商品信息
        $goodsLogic = new GoodsLogic($ret, $this->logic->getSplitKeyByGoodsId());
        
        Tool::connect('parseString');
        
        $ret = $goodsLogic->getGoodsByOtherDataCache();
        
        $this->objController->promptPjax($ret, $goodsLogic->getErrorMessage());
        
        //获取图片
        $goodsImage = new GoodsImagesLogic($ret, $goodsLogic->getSplitKeyByPId());
        
        $ret = $goodsImage->getImageByArrayGoods();
        
        $this->objController->promptPjax($ret, $goodsImage->getErrorMessage());
        
        //获取店铺信息
        $storeLogic = new StoreLogic($ret, $this->logic->getSplitKeyByStoreId());
        
        $store = $storeLogic->getResult();
        
        $this->objController->promptPjax($ret, $goodsImage->getErrorMessage());
        
		$cartSession = new SessionManager($ret);
		
		$cartSession->sessionParse();
		
		//按店铺拆分
		$storeData = (new ArrayChildren($ret))->inTheSameState( $this->logic->getSplitKeyByStoreId());
		
		$returnData = [
			'shop_goods_info' => $storeData,
			'total_price' => $cartSession->getTotalPrice(),
			'store' => $store,
			'total_number' => $cartSession->getTotalNumber()
		];
		
		$this->objController->ajaxReturnData($returnData);
    }
    /**
     * 购物车列表
     *
     */
    public function cartGoodsList(){

        $ret = $this->logic->getCartGoodsList();  

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret);
    }
    /**
     * 购物车商品数量加
     * @author Lixiang
     */
    public function cartNumPlus(){

        $ret = $this->logic->getCartNumPlus();

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);

    }
    /**
     * 购物车商品数量减
     * @author Lixiang
     */
    public function cartNumReduce(){

        $ret = $this->logic->getCartNumReduce();

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);

    }
    /**
     * 购物车商品数修改
     * @author Lixiang
     */
    public function cartNumModify(){

        $ret = $this->logic->getCartNumModify();

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);

    }

    /**
     * 编辑购物车
     */
    public function delGoodsCart(){
        //检测传值         

    	$this->objController->promptPjax($this->logic->checkIdIsNumric(), $this->logic->getErrorMessage());

        $ret = $this->logic->goodsCartDelete();

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }

 
    //购物车移入收藏夹
    public function cartAddCollection(){
         //检测传值                  //检测方法
        // $checkObj = new CheckParam($this->logic->getCartIdValidateByCollection(), $this->args);

        // $status = $checkObj->checkParam(); 

        // $this->objController->promptPjax($status, $checkObj->getErrorMessage());
        $ret = $this->logic->AddCollection();

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
    
    /**
     * 加入购物车
     */
    public function addGoodsAll()
    {
        //检测传值                  //检测方法
        $checkObj = new CheckParam($this->logic->getMessageByAll(), $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());

        $ret = $this->logic->addCarAll(); 

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }

}