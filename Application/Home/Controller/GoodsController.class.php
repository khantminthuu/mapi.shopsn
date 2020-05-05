<?php
// +----------------------------------------------------------------------
// | OnlineRetailers [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2003-2023 www.shopsn.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed 亿速网络（http://www.shopsn.net）
// +----------------------------------------------------------------------
// | Author: 王强 <13052079525>
// +----------------------------------------------------------------------
// |简单与丰富！
// +----------------------------------------------------------------------
// |让外表简单一点，内涵就会更丰富一点。
// +----------------------------------------------------------------------
// |让需求简单一点，心灵就会更丰富一点。
// +----------------------------------------------------------------------
// |让言语简单一点，沟通就会更丰富一点。
// +----------------------------------------------------------------------
// |让私心简单一点，友情就会更丰富一点。
// +----------------------------------------------------------------------
// |让情绪简单一点，人生就会更丰富一点。
// +----------------------------------------------------------------------
// |让环境简单一点，空间就会更丰富一点。
// +----------------------------------------------------------------------
// |让爱情简单一点，幸福就会更丰富一点。
// +----------------------------------------------------------------------
declare(strict_types=1);
namespace Home\Controller;

use Common\Logic\GoodsLogic;
use Common\Logic\GoodsSpecLogic;
use Common\Logic\HotWordsLogic;
use Common\Logic\SpecGoodsPriceLogic;
use Common\TraitClass\InitControllerTrait;
use Validate\CheckParam;
use Common\Tool\Tool;
Use Common\Logic\GoodsSpecItemLogic;
use Common\Logic\GoodsImagesLogic;
use Common\Logic\FootPrintLogic;
use Common\Logic\StoreLogic;
use Common\SessionParse\SessionManager;
use Think\SessionGet;

/**
 * 商品控制器
 */
class GoodsController {
	
	use InitControllerTrait;
	
	/**
	 * 架构方法
	 * @param array $args 传入的参数数组
	 */
	public function __construct(array $args = []) {
		
		$this->args = $args;
		$this->init ();
		$this->logic = new GoodsLogic ( $args );
		
		//移除全部与订单相关的session
		SessionManager::REMOVE_ALL();
	}


	/**
	 * Get product details
	 */
	public function goodInfo() :void
	{
		$this->objController->promptPjax ( $this->logic->checkIdIsNumric(), $this->logic->getErrorMessage () );
		
		$ret = $this->logic->getGoodsDetailCache();

		$this->objController->promptPjax ( $ret, $this->logic->getErrorMessage () );


		//Get product image
		$goodsImageLogic = new GoodsImagesLogic($ret, $this->logic->getSplitKeyByPId());
		
		$image = $goodsImageLogic->getGoodImageCache();
		
		// Guess you like when not logged in
		$brandId = isset($_COOKIE['brand_id']) ?  json_decode($_COOKIE['brand_id'], true) : [];
		
		$classId = isset($_COOKIE['class_id']) ?  json_decode($_COOKIE['class_id'], true) : [] ;
		
		$classId[] = $ret['class_two'];
		
		$brandId[]=  $ret['brand_id'];
		
		$time = time()  + 3600 * 4;
		
		$cookieDomin = C('COOKIE_DOMAIN');
		
		setcookie('brand_id', json_encode(array_unique($brandId), JSON_UNESCAPED_UNICODE), $time, '/', $cookieDomin);
		
		setcookie('class_id', json_encode(array_unique($classId), JSON_UNESCAPED_UNICODE), $time, '/', $cookieDomin);
		
		//是否登录
		$userId = SessionGet::getInstance('user_id')->get();
		if ($userId) {
			
			$footPrint = new FootPrintLogic($ret);
			
			$footPrint->addData();
		}
		
		$this->objController->ajaxReturnData ( [
			'goods' => $ret,
			'images' => $image
		] );


	}


	
	/*
		#khantminthu
	*/
	public function goodsInfo()	:void
	{
		$this->objController -> promptPjax( $this->logic->checkIdIsNumric() ,$this->logic->getErrorMessage());	//get array is numeric?

		$ret = $this->logic->getGoodDetail();
  
		$this->objController->promptPjax($ret , $this->logic->getErrorMessage());

		$goodsImageLogic = new GoodsImagesLogic($ret , $this->logic->getSplitKeyByPId());
		# $this->logic->getSplitKeyByPId() = str id;

		$image = $goodsImageLogic->getGoodsImage();

		$brandId = isset($_COOKIE['brand_id'])?json_decode($_COOKIE['brand_id'],true) :[];

		$classId = isset($_COOKIE['class_id'])?json_decode($_COOKIE['class_id'],true):[];

		$classId[] = $ret['class_two'];

		$classId[] = $ret['brand_id'];

		$time = time()+3600*4;		//time is 4 days;

		$cookieDomin = C('COOKIE_DOMAIN');		//from config

		setcookie('brand_id' , json_encode(array_unique($brandId),JSON_UNESCAPED_UNICODE),$time,'/',$cookieDomin);
		
		setcookie('class_id' , json_encode(array_unique($classId),JSON_UNESCAPED_UNICODE),$time,'/',$cookieDomin);

		$userId = SessionGet::getInstance('user_id')->get();

		if($userId){

			$footPrint = new FootPrintLogic($ret);

			$footPrint->addData();
		}

		$this->objController->ajaxReturnData ( [
			'goods' => $ret,
			'images' => $image
		] );
	}
	
	/**
	 * 获取商品子类数据
	 * 
	 * @author 王强
	 */
	public function goodSpecsByGoodsChildren() :void
	{
		$this->objController->promptPjax ( IS_POST === true, '请求失败' );
		$checkObj = new CheckParam ( $this->logic->getAttrValidateByLogin (), $this->args );
		
		$status = $checkObj->checkParam ();
		
		$this->objController->promptPjax ( $status, $checkObj->getErrorMessage () );
		
		$goodsChildren = $this->logic->getChildrenGoods ();
		
		$this->objController->promptPjax ( $goodsChildren, '无规格' );
		
		$specPriceLogic = new SpecGoodsPriceLogic ( $goodsChildren, $this->logic->getPrimaryKey () );
		
		Tool::connect ( 'parseString' );
		
		$ret = $specPriceLogic->getSpecByGoods ();
		
		$this->objController->promptPjax ( $ret, '无规格');
		
		$specItemLogic = new GoodsSpecItemLogic ( $ret, $specPriceLogic->getSplitKeyByGoods () );
		// 获取规格项
		$specItemArray = $specItemLogic->getSpecItemName ();
		
		$this->objController->promptPjax ( $specItemArray, '无规格项，数据异常' );
		// 获取主规格
		$specialLogic = new GoodsSpecLogic ( $specItemArray, $specItemLogic->getSplitKeyBySpecial () );
		
		$specialArray = $specialLogic->getGoodSpecial ();
		
		$this->objController->promptPjax ( $ret, $this->logic->getErrorMessage () );
		
		$this->objController->ajaxReturnData ( [ 
			'goods' => $ret,
			'spec' => $specialArray 
		] );
	}
	
	/**
	 * 获取商品属性
	 * 
	 * @author 王强
	 */
	public function goodAttr() :void
	{
		$this->objController->promptPjax ( IS_POST === true, '请求失败' );
		$checkObj = new CheckParam ( $this->logic->getAttrValidateByLogin (), $this->args );
		
		$status = $checkObj->checkParam ();
		
		$this->objController->promptPjax ( $status, $checkObj->getErrorMessage () );
		
		// 商品集合
		$ret = $this->logic->getChildrenGoodsByAttr ();
		$this->objController->promptPjax ( $ret, $this->logic->getErrorMessage () );
		
		$this->objController->ajaxReturnData ( $ret ['data'], $ret ['status'], $ret ['message'] );
	}
	
	
	/**
	 * 热门搜索
	 */
	public function hotSearch() :void
	{
		if (IS_POST) {
			
			$hotWordsLogic = new HotWordsLogic ( $this->args );
			
			$ret = $hotWordsLogic->hotWordSearch ();
			
			$this->objController->promptPjax ( $ret, $hotWordsLogic->getErrorMessage () );
			
			$this->objController->ajaxReturnData ( $ret );
		} else {
			$this->objController->ajaxReturnData ( "", "0", "请求失败" );
		}
	}
	
	
	/**
	 * 获取店铺动态-最新上架的商品
	 *  @deprecated
	 */
	public function storeDynamic() :void{
		if (IS_POST) {
			
			$checkObj = new CheckParam ( $this->logic->getValidateByShop (), $this->args );
			
			$status = $checkObj->checkParam ();
			
			$this->objController->promptPjax ( $status, $checkObj->getErrorMessage () );
			
			$ret = $this->logic->getStoreDynamic ();
			
			$this->objController->promptPjax ( $ret, $this->logic->getErrorMessage () );
			
			$this->objController->ajaxReturnData ( $ret );
		} else {
			$this->objController->ajaxReturnData ( "", "0", "请求失败" );
		}
	}
	
	/**
	 * *
	 * 获取商品规格详情
	 */
	public function getGoodInfo() :void
	{
		$checkObj = new CheckParam ( $this->logic->getValidateByGoods (), $this->args );
		
		$status = $checkObj->checkParam ();
		
		$this->objController->promptPjax ( $status, $checkObj->getErrorMessage () );
		
		$ret = $this->logic->getGoodSpeInfo ();
		
		$this->objController->promptPjax ( $ret, $this->logic->getErrorMessage () );
		
		$this->objController->ajaxReturnData ( $ret );
	}
	
	/**
     * 立即购买生成 session相关数据
     * @author 王强
     */
	public function cartGoodsDetail() :void
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
    
    /**
     * 获取一级分类下面的商品
     * @author 王波
     */
    public function oneClassGoods() :void
    {
    	$checkObj = new CheckParam($this->logic->getValidateByGoods(), $this->args);
    	
    	$status = $checkObj->checkParam();
    	
    	$this->objController->promptPjax($status, $checkObj->getErrorMessage());
    	
    	$ret = $this->logic->getClassGoodsByOneClass();
    	
    	$this->objController->promptPjax($ret, $this->logic->getErrorMessage());
    	
    	$this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
    
    /**
     * 获取店铺推荐商品
     */
    public function getRecommondGoods() :void
    {
    	$checkObj = new CheckParam($this->logic->getMessageByStoreRecommendGoods(), $this->args);
    	
    	$status = $checkObj->checkParam();
    	
    	$this->objController->promptPjax($status, $checkObj->getErrorMessage());
    	
    	$goods = $this->logic->getStoreGoodsByRecommend();
    	
    	$this->objController->promptPjax($goods, $this->logic->getErrorMessage());
    	
    	$goodsImage = new GoodsImagesLogic($goods, $this->logic->getSplitKeyByPId(), ['page' => $this->args['store_id']]);
    	
    	Tool::connect('parseString');
    	
    	$source = $goodsImage->getResult();
    	
    	$this->objController->ajaxReturnData($source);
    }
}
