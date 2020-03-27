<?php
namespace Home\Controller;
use Common\TraitClass\InitControllerTrait;
use Think\SessionGet;
use Common\Logic\ReviewLogic;
use Validate\CheckParam;

class ReviewController
{
	use InitControllerTrait;

	// use IsLoginTrait;

	public function __construct(array $args = [])
	{
		$this->args = $args;

		$this->init();

		$this->logic = new ReviewLogic($args);
	}
	public function getUserReview()
	{	
		$checkObj = new CheckParam($this->logic->getValidateByLogin(),$this->args);
		
		$status  = $checkObj->checkParam();
        
        $this->objController->promptPjax($status, $checkObj->getErrorMessage());
        
		$ret = $this->logic->getUserReview();

		$this->objController->promptPjax($ret , $this->logic->getErrorMessage());

		$this->objController->ajaxReturnData($ret);
	}
	
}
