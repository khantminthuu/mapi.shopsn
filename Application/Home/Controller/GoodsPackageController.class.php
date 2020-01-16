<?php
declare(strict_types=1);
namespace Home\Controller;

use Common\TraitClass\InitControllerTrait;
use Validate\CheckParam;
use Common\Logic\GoodsPackageLogic;
use Common\TraitClass\IsLoginTrait;
use Common\Logic\GoodsPackageSubLogic;
use Common\Tool\Tool;
use Common\Logic\GoodsLogic;
use Common\Logic\GoodsImagesLogic;
use Common\Logic\StoreLogic;
use Common\SessionParse\SessionManager;
use Common\Tool\Extend\ArrayChildren;

//优惠套餐
class GoodsPackageController
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
        
    	$this->logic = new GoodsPackageLogic($args);
    }
    /**
     * 套餐立即购买--获取商品详情
     * @author 王波
     */
    public function cartPackageBuyNow(){
    	
    	$this->checkIsLogin();
    	
    	SessionManager::REMOVE_ALL();
    	
        //检测传值                  //检测方法
        $checkObj = new CheckParam($this->logic->getValidateByGoodsPackage(), $this->args);

        $status = $checkObj->checkParam();
		
        $this->objController->promptPjax($status, $checkObj->getErrorMessage());
        
        $ret = $this->logic->getPackageBuyNow();   
       
        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());
        
        $goodsSub = new GoodsPackageSubLogic($ret, $this->logic->getPrimaryKey());
        
        $packageSubData = $goodsSub->getResult();
       
        $this->objController->promptPjax($packageSubData, $this->logic->getErrorMessage());
        
        $goodsLogic = new GoodsLogic($packageSubData, $goodsSub->getSplitKeyByGoods());
        
        Tool::connect('parseString');
       
        $goodsData = $goodsLogic->getParseDataByOrder();
        
        $this->objController->promptPjax($goodsData, $goodsLogic->getErrorMessage());
        
        $goodsImageLogic = new GoodsImagesLogic($goodsData, $goodsLogic->getSplitKeyByPId());
        
        $goodsImage = $goodsImageLogic->getImageByArrayGoods();
       
        $this->objController->promptPjax($goodsImage, $goodsImageLogic->getErrorMessage());
        
        $storeLogic = new StoreLogic($ret, $this->logic->getSplitKeyByStore());
        
        $store = $storeLogic->getStoreInfoByStoreIdStringCache();
       
        $sessionOrderManager = new SessionManager($goodsData);
        
        $sessionOrderManager->discountPackagePurchaseImmediately((new ArrayChildren($ret))->convertIdByData($this->logic->getPrimaryKey()));
        
        $this->objController->ajaxReturnData(['goods' => $goodsImage, 'store' => $store, 'package' => $ret]);
    }
    
    /**
     * 商品套餐列表
     */
    public function packageList() :void
    {
    	$checkObj = new CheckParam($this->logic->getMessageNotice(), $this->args);
    	
    	$this->objController->promptPjax($checkObj->checkParam(), $checkObj->getErrorMessage());
    	
    	$packageList = $this->logic->getPackageListCache();
    	
    	$this->objController->promptPjax($packageList, $this->logic->getErrorMessage());
    	
    	$goodsSubLogic = new GoodsPackageSubLogic(['package' => $packageList, 'args' => $this->args], $this->logic->getPrimaryKey());
    	
    	$subData = $goodsSubLogic->parseGoodsIsInPackage();
    	
    	$this->objController->promptPjax($subData, $this->logic->getErrorMessage());
    	
    	$goodsLogic = new GoodsLogic($subData, $goodsSubLogic->getSplitKeyByGoods());
    	
    	Tool::connect('parseString');
    	
    	$goodsData = $goodsLogic->getGoodsByOtherDataCache();
    	
    	$this->objController->promptPjax($goodsData, $goodsLogic->getErrorMessage());
    	
    	$goodsImageLogic = new GoodsImagesLogic($goodsData, $goodsLogic->getSplitKeyByPId()); 
    	
    	$goodsData = $goodsImageLogic->getImageByArrayGoods();
    	
    	$this->objController->ajaxReturnData(['goods' => $goodsData, 'package' => $packageList]);
    }
}