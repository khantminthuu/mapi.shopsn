<?php
namespace Home\Controller;
use Common\TraitClass\InitControllerTrait;
use Common\Logic\CategoryLogic;
use Validate\CheckParam;
use Common\Tool\Tool;

class CategoryController
{
	use InitControllerTrait;

	public function __construct(array $args=[])
	{
		$this->args = $args;

		$this->init();

		$this->logic = new CategoryLogic($args);
	}
	public function getCategory()
	{
		// $checkobj = new CheckParam($this->logic->getValidateByLogin(),$this->args);

		// $status = $checkobj->CheckParam();

		// $this->objController->promptPjax($status, $checkObj->getErrorMessage());

		$ret = $this->logic->getAllCategory();

		$this->objController->promptPjax($ret, $this->logic->getErrorMessage());

		$this->objController->ajaxReturnData($ret);
	}

	public function showCategory()
	{
		$checkobj = new CheckParam($this->logic->getValidateByLogin(),$this->args);

		$status = $checkobj->checkParam();

		$this->objController->promptPjax($status, $this->logic->getErrorMessage());

		$showData = $this->logic->saveShow();
	}
}