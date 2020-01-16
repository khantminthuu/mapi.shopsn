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
declare(strict_types = 1);
namespace Home\Controller;

use Common\TraitClass\InitControllerTrait;
use Common\Logic\GoodsLogic;
use Common\Logic\GoodsImagesLogic;
use Validate\CheckParam;
use Common\Tool\Tool;
use Common\Logic\StoreLogic;
use Common\SessionParse\SessionManager;

/**
 * 配件立即购买获取商品详情
 * @author Administrator
 */
class ImmediatePurchaseOfPartsController
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
		
		$this->_initUser();
		
		$this->logic = new GoodsLogic($args);
		
		SessionManager::REMOVE_ALL();
	}
	
	/**
     * 推荐配件立即购买商品获取详情
     */
    public function goodsComboBuyNow() :void
    {
    	$checkObj = new CheckParam($this->logic->getMessageByAccessories(), $this->args);
    	
    	$this->objController->promptPjax($checkObj->checkParam(), $checkObj->getErrorMessage());
    	
    	$data = $this->logic->bestAccessoriesImmediatePurchaseCache();
    	
    	$this->objController->promptPjax($data, $this->logic->getErrorMessage());
    	
    	//获取商品图片
    	$goodsImageLogic = new GoodsImagesLogic($data, $this->logic->getSplitKeyByPId());
    	
    	Tool::connect('parseString');
    	
    	$data = $goodsImageLogic->getImageByArrayGoods();
    	
    	//获取店铺信息
    	$storeLogic = new StoreLogic($data, $this->logic->getSplitKeyByStore());
    	
    	$store = $storeLogic->getStoreInfoByStoreIdStringCache();
    	
    	$this->objController->promptPjax($store, $storeLogic->getErrorMessage());
    	
    	$sessionOrder = new SessionManager($data);
    	
    	$sessionOrder->sessionParse();
    	
    	$this->objController->ajaxReturnData([
    		'goods' => $data,
    		'store' => $store,
    		'total_price' => $sessionOrder->getTotalPrice(),
    		'total_number' => $sessionOrder->getTotalNumber()
    	]);
    	
    }
}