<?php
namespace Home\Controller;
use Common\Logic\GoodsClassLogic;
use Common\Logic\GoodsLogic;
use Common\Logic\AdLogic;
use Common\TraitClass\InitControllerTrait;
use Validate\CheckParam;
use Common\Logic\GoodsImagesLogic;
use Common\Tool\Tool;



class GoodsClassController
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
        $this->logic = new GoodsClassLogic($args);
        $this->goodslogic = new GoodsLogic($args);
    }
    /**
     * 获取商品分类数据
     *
     */
    public function getOtherClass()
    {
        if (IS_GET){
            $checkObj = new CheckParam($this->logic->getValidateByLogin(), $this->args);

            $status = $checkObj->checkParam();

            $this->objController->promptPjax($status, $checkObj->getErrorMessage());

            $ret = $this->logic->getAllClassees();

            $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

            $this->objController->ajaxReturnData($ret);
        }else{
            $this->objController->ajaxReturnData("","0","请求失败");
        }}

        /*
        *khantminthu
        *get class
        */
        public function getClass(){
            if(IS_GET){
             $checkObj = new CheckParam($this->logic->getValidateByLogin(), $this->args);

             $status = $checkObj->checkParam();

             $this->objController->promptPjax($status, $checkObj->getErrorMessage());

             /*This is check for data exit*/

             $ret = $this->logic->getClass();
             echo "<pre>";
             print_r($ret);
             die;
             $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

             $this->objController->ajaxReturnData($ret);
         }else{
            $this->objController->ajaxReturnData("","0","请求失败");
        }
    }
    

    /**
     * 获取三级分类下面的商品
     *
     */
    public function getClassGoods(){ 

//        $checkObj = new CheckParam($this->goodslogic->getValidateByLogin(), $this->args);
//
//        $status = $checkObj->checkParam();
//
//        $this->objController->promptPjax($status, $checkObj->getErrorMessage());

        $ret = $this->goodslogic->getClassGoods();

        $this->objController->promptPjax($ret, $this->goodslogic->getErrorMessage());

        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
        
    }

    /**
     * 商品搜索
     *
     */
    public function search(){
        if (IS_GET ){
            $ret = $this->goodslogic->getSerchGoods();

            $this->objController->promptPjax($ret, $this->goodslogic->getErrorMessage());

            $this->objController->ajaxReturnData($ret);
        }else{
            $this->objController->ajaxReturnData("","0","请求失败");
        }
    }
    /**
     * 获取所有的一级分类Id
     *
     */
    public function getFirstId(){

        $ret = $this->logic->getFirstClassId();

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
        
    }
    /**
     * 获取下级分类Id
     *
     */
    public function nextClassId(){
        $checkObj = new CheckParam($this->logic->getValidateByLogin(), $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());

        $ret = $this->logic->getNextClassId();

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
        
    }
    /**
     * 获取所有的一级分类Id
     *
     */
    public function lookShopGoodClass(){
        if (IS_GET ){
            $checkObj = new CheckParam($this->logic->getValidateByShop(), $this->args);

            $status = $checkObj->checkParam();

            $this->objController->promptPjax($status, $checkObj->getErrorMessage());

            $ret = $this->logic->getShopGoodsClass();

            $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

            $this->objController->ajaxReturnData($ret);
        }else{
            $this->objController->ajaxReturnData("","0","请求失败");
        }
    }

    /**
     * 首页楼层数据
     */
    public function indexFloor()
    {
    	$checkObj = new CheckParam($this->logic->getValidateByClassPage(), $this->args);
    	
    	$this->objController->promptPjax($checkObj->checkParam(), $checkObj->getErrorMessage());
    	
    	$goodsClass = $this->logic->getClassByPage();
    	
    	$this->objController->promptPjax($goodsClass, '暂无分类');
        //获取分类广告图片
        $adLogic = new AdLogic($goodsClass);
        $class = $adLogic->getFloorAd();
    	//推荐分类的商品
        $goodsLogic = new GoodsLogic($goodsClass);

        $goods = $goodsLogic->getRecommend();

        $goodsImage = new GoodsImagesLogic($goods, $goodsLogic->getSplitKeyByPId(), $this->args);

        Tool::connect('parseString');

        $source = $goodsImage->getResult();

        $this->objController->ajaxReturnData(['goods' => $source, 'class' => $goodsClass,'ad'=>$class]);
    }

}