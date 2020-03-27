<?php
namespace Home\Controller;

use Common\TraitClass\InitControllerTrait;
use Validate\CheckParam;
use Common\Logic\StoreLogic;
use Common\Logic\GoodsClassLogic;
use Common\Logic\GoodsLogic;
use Common\Logic\StoreFollowLogic;


class StoreController
{
    use InitControllerTrait;

    /**
     * 架构方法
     * @param array
     * $args   传入的参数数组
     */
    public function __construct(array $args = [])
    {
        $this->args = $args;
        $this->init();  
        $this->logic = new StoreLogic($args);
        $this->goodsClassLogic = new GoodsClassLogic($args);
        $this->goods = new GoodsLogic($args);
        $this->storeFollow = new StoreFollowLogic($args);
    }

    /**
     * 得到首页的店铺列表
     *
     */
    public function getStoreList()
    {
        $ret = $this->logic->getStoreList();
   
        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret);
    }

    /**
     * 店铺首页
     *
     */
    public function storeHome(){
        
        $checkObj = new CheckParam($this->logic->getValidateByLogin(), $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());

        $ret = $this->logic->getStoreInfo(); 

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);

    }

    /**
     * 店铺热门分类
     *
     */
    public function storeHotClass(){
        $checkObj = new CheckParam($this->goodsClassLogic->getValidateByShop(), $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());

        $ret = $this->goodsClassLogic->getHotCalssGoods();

        $this->objController->promptPjax($ret, $this->goodsClassLogic->getErrorMessage());

        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
    /**
     * 商品界面店铺列表
     *
     */
    public function goodsStoreList(){
        $ret = $this->logic->getGoodsStoreList();

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret);
    }
    /**
     * 关注店铺
     *
     */
    public function attenStore(){
        $this->_initUser();
        
        $checkObj = new CheckParam($this->storeFollow->getValidateByLogin(), $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());

        $ret = $this->storeFollow->attenStore();

        $this->objController->promptPjax($ret, $this->storeFollow->getErrorMessage());

        $this->objController->ajaxReturnData($ret);
    }
    /**
     * 取消关注店铺
     *
     */
    public function cancelAttenStore(){
        $checkObj = new CheckParam($this->storeFollow->getValidateByLogin(), $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());

        $ret = $this->storeFollow->cancelAttenStore();

        $this->objController->promptPjax($ret, $this->storeFollow->getErrorMessage());

        $this->objController->ajaxReturnData($ret);
    }
    /**
     * 店铺详情
     *
     */
    public function shopDetails(){

        $checkObj = new CheckParam($this->logic->getValidateByLogin(), $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());

        $ret = $this->logic->getShopDetails();
        
        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());
        
        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }

    /**
     * 获取店铺动态-最新上架的商品 
     * @deprecated
     */

    public function storeDynamic(){
        if (IS_POST ){

            $checkObj = new CheckParam($this->logic->getValidateByLogin(), $this->args);

            $status = $checkObj->checkParam();

            $this->objController->promptPjax($status, $checkObj->getErrorMessage());

            $ret = $this->logic->getStoreDynamic();

            $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

            $this->objController->ajaxReturnData($ret);
        }else{
            $this->objController->ajaxReturnData("","0","请求失败");
        }
    }
    /**
     * 获取店铺的粉丝数
     *
     */

    public function getfans(){
        if (IS_POST ){

            $checkObj = new CheckParam($this->logic->getValidateByLogin(), $this->args);

            $status = $checkObj->checkParam();

            $this->objController->promptPjax($status, $checkObj->getErrorMessage());

            $ret = $this->logic->get_store_fans();

            $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

            $this->objController->ajaxReturnData($ret);
        }else{
            $this->objController->ajaxReturnData("","0","请求失败");
        }
    }
    /**
     * 获取有某个商品分类的店铺
     *
     */
    public function classOfStore(){
        if (IS_POST ){

            $checkObj = new CheckParam($this->logic->getValidateByClassId(), $this->args);

            $status = $checkObj->checkParam();

            $this->objController->promptPjax($status, $checkObj->getErrorMessage());

            $ret = $this->logic->class_store();

            $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

            $this->objController->ajaxReturnData($ret);
        }else{
            $this->objController->ajaxReturnData("","0","请求失败");
        }
    }
    //获取店铺全部宝贝
    public function storeGoodsAll(){
        $checkObj = new CheckParam($this->logic->getValidateByLogin(), $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());

        $ret = $this->logic->getStoreGoodsAll();

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
	
    /**
     * 获取商品店铺信息
     *
     */
    public function shopInfo() 
    {
    	$checkObj = new CheckParam ( $this->logic->getValidateByLogin (), $this->args );
    		
    	$status = $checkObj->checkParam ();
    		
    	$this->objController->promptPjax ( $status, $checkObj->getErrorMessage () );
    		
    	$ret = $this->logic->getShopInfo ();

    	$this->objController->promptPjax ( $ret, $this->logic->getErrorMessage () );
    		
    	$this->objController->ajaxReturnData ( $ret );
    }





    /**
     * 获取店铺证照信息
     * @author 王波
     */
    public function shopLicense() 
    {
        $checkObj = new CheckParam ( $this->logic->getValidateByLogin (), $this->args );
            
        $status = $checkObj->checkParam ();
            
        $this->objController->promptPjax ( $status, $checkObj->getErrorMessage () );
            
        $ret = $this->logic->getShopLicense();
            
        $this->objController->promptPjax ( $ret, $this->logic->getErrorMessage () );
            
        $this->objController->ajaxReturnData ($ret['data'],$ret['status'],$ret['message']);
    }

    /*khantminthu*/

    public function getShop() //shopINfo to my own style
    {
        $checkObj = new checkParam( $this->logic->getValidateByLogin() , $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax ( $status, $checkObj->getErrorMessage () );

        $ret = $this->logic->getShopDetail();

        $this->objController->promptPjax ( $ret, $this->logic->getErrorMessage () );

        $this->objController->ajaxReturnData ( $ret );
    }


    /* ttpw */       //promoting the sale of goods

    public function getDiscount()
    {
        $checkObj = new checkParam( $this->logic->getValidateByLogin() , $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax ( $status, $checkObj->getErrorMessage () );

        $ret = $this->logic->getDiscountDetail();
        
        $this->objController->promptPjax($ret,$this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret);
    }



}
