<?php
declare(strict_types = 1);
namespace Home\Controller;

use Common\TraitClass\InitControllerTrait;
use Validate\CheckParam;
use Common\Logic\GoodsPackageCartLogic;
use Common\TraitClass\IsLoginTrait;
use Common\Logic\GoodsPackageLogic;
use Common\Tool\Tool;
use Common\Logic\GoodsPackageSubLogic;
use Common\Logic\GoodsLogic;
use Common\Logic\GoodsImagesLogic;
use Common\Logic\StoreLogic;
use Common\SessionParse\SessionManager;
use Common\Tool\Extend\ArrayChildren;
//套餐购物车
class GoodsPackageCartController
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
        $this->logic = new GoodsPackageCartLogic($args);
    }
    /**
     * 购物车套餐列表
     * @author 王波
     */
    public function cartPackageList() :void
    {

        $ret = $this->logic->getCartGoodsList();  

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
    /**
     * 购物车套餐列表--删除
     * @author 王波
     */
    public function cartPackageDel() :void
    {
        $checkObj = new CheckParam($this->logic->getValidateByLogin(), $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());

        $ret = $this->logic->getCartGoodsDel();  

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
    /**
     * 购物车套餐数量加
     * @author 王波
     */
    public function cartNumPlus() :void
    {
        $checkObj = new CheckParam($this->logic->getValidateByLogin(), $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());
        $ret = $this->logic->getCartNumPlus();

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);

    }
    /**
     * 购物车套餐数量减
     * @author 王波
     */
    public function cartNumReduce() :void
    {
        $checkObj = new CheckParam($this->logic->getValidateByLogin(), $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());
        $ret = $this->logic->getCartNumReduce();

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);

    }
    /**
     * 购物车套餐数修改
     * @author 王波
     */
    public function cartNumModify() :void
    {
        $checkObj = new CheckParam($this->logic->getValidateByNumModify(), $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());
        $ret = $this->logic->getCartNumModify();

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);

    }
    /**
     * 套餐购物车--去结算
     */
    public function toSettleAccounts() :void
    {
        
    	$checkObj = new CheckParam($this->logic->getValidateByLogin(), $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());
       
        
        $packageCart = $this->logic->getPackageInfoByCartCache();

        $this->objController->promptPjax($packageCart, $this->logic->getErrorMessage());

        $goodsPackageLogic = new GoodsPackageLogic($packageCart, $this->logic->getSplitKeyByPackage());
        
        Tool::connect('parseString');
        
        $packageCart = $goodsPackageLogic->getPackageByPackageCartCache();
        
        $this->objController->promptPjax($packageCart, $goodsPackageLogic->getErrorMessage());
        
        $packageCart = (new ArrayChildren($packageCart))->convertIdByData($this->logic->getSplitKeyByPackage());
        
        $goodsPackageSubLogic = new GoodsPackageSubLogic($packageCart, $this->logic->getSplitKeyByPackage());
        
        // 套餐商品
        $goodsSubGoods = $goodsPackageSubLogic->getGoodsPackageSubDataByGoodsCart();
        
       
        $this->objController->promptPjax($goodsSubGoods, $goodsPackageSubLogic->getErrorMessage());
        
        $goodsLogic = new GoodsLogic($goodsSubGoods, $goodsPackageSubLogic->getSplitKeyByGoods());
        
        $goodsData = $goodsLogic->getParseDataCartByOrder();
        
      
        $this->objController->promptPjax($goodsData,$goodsLogic->getErrorMessage());
        
        $goodsImageLogic = new GoodsImagesLogic($goodsData, $goodsLogic->getSplitKeyByPId());
        
        $goodsData = $goodsImageLogic->getImageByArrayGoods();
        
        $this->objController->promptPjax($goodsData,$goodsImageLogic->getErrorMessage());
        
        $storeLogic = new StoreLogic($packageCart, $this->logic->getSplitKeyByStore());
        
        $store = $storeLogic->getStoreInfoByStoreIdStringCache();
        
        $sessionManager = new SessionManager($goodsData);
        
        $sessionManager->discountPackagePurchaseImmediately($packageCart);
        
        $this->objController->ajaxReturnData(['goods' => $goodsData, 'store' => $store]);

    }
    //添加购物车
    public function addCart() :void
    {
    	$checkObj = new CheckParam($this->logic->getValidateByAdd(), $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());
        
        $ret = $this->logic->addPackageToCart();

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);

    }
}