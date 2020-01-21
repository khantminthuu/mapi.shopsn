<?php 
    public function goodInfo() :void
	{
		$this->objController->promptPjax ( $this->logic->checkIdIsNumric(), $this->logic->getErrorMessage () );
		
		$ret = $this->logic->getGoodsDetailCache();
		
		$this->objController->promptPjax ( $ret, $this->logic->getErrorMessage () );
		
		//获取商品图片
		$goodsImageLogic = new GoodsImagesLogic($ret, $this->logic->getSplitKeyByPId());
		
		$image = $goodsImageLogic->getGoodImageCache();
		
		// 未登录时猜你喜欢
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
 ?>