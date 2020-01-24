<?php
// declare(strict_types=1);
namespace Common\Logic;
use Think\Cache;
use Think\Log;
use Think\SessionGet;
use Common\Model\ReviewModel;
use Common\Model\BrandModel;

class ReviewLogic extends AbstractGetDataLogic
{
	public function __construct(array $data = [], $split = '')
	{
		$this->data = $data;
		$this->splitKey = $split;
		$this->modelObj =new ReviewModel();	
	}
	public function getModelClassName() :string
    {
        return ReviewModel::class;
    }
    public function getResult() :array
    {
    	$ret = $modelObj->getModel();
        return [ ];
    }
    public function getUserReview()
    {
    	$data = $this->modelObj->getModel($this->data);
    }

}
