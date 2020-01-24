<?php
namespace Home\Controller;
use Common\TraitClass\InitControllerTrait;
use Think\SessionGet;
use Common\Logic\ReviewLogic;

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
		
		$userId = SessionGet::getInstance('user_id')->get();

		$ret = $this->logic->getUserReview();

		$this->objController->promptPjax($ret , $this->logic->getErrorMessage());

		$this->objController->ajaxReturnData($ret);
	}

	public function mo()
	{
		$number = 100;
		for ($num=0; $num < $number ; $num++) { 
		}
	}
	
}