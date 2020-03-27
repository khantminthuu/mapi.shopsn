<?php
// declare(strict_types=1);
namespace Common\Logic;

use Common\Model\UserModel;
use Common\Model\ReviewModel;
use Common\Model\BrandModel;
use Common\Model\UserHeaderModel;
use Think\Cache;
use Think\Log;
use Think\SessionGet;

class ReviewLogic extends AbstractGetDataLogic
{
	public function __construct(array $data = [], $split = '')
	{
		$this->data = $data;
		$this->splitKey = $split;
		$this->modelObj =new ReviewModel();	
		$this->userObj = new UserModel();
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
    
    public function getValidateByLogin()
    {
        $message =[
            'id'=>[
                'required'=> "必须输入品牌ID",
            ],
        ];
        return $message;
    }
    
    public function getUserReview()
    {
    	$where['goods_id'] = $this->data['id'];

    	$field = ['user_id','review','Timestamp','rating'];

    	$totalReviews = $this->modelObj->where($where)->count();

    	$review = $this->modelObj->where($where)->field($field)->select();

    	$reviewDetail = UserHeaderModel::getUserDetail($review);

    	$totalRating = $this->calRating($reviewDetail);
    	
    	$ret = array(
    		'totalReviews' => $totalReviews,
    		'totalRating' => $totalRating,
    		'reviewDetail' => $reviewDetail
    	);
    	return $ret;
    }

    public function calRating($arr)
    {
    	foreach ($arr as $key => $value) {
    		$rating[] = $value['rating'];
    	}
    	$count = count($rating , COUNT_NORMAL);
    	for($i=0;$i<=$count;$i++){
			if($rating[$i]==5){
				$star5 += 1;
			}else if($rating[$i]==4){
				$star4 += 1;
			}else if($rating[$i]==3){
				$star3 += 1;
			}else if($rating[$i]==2){
				$star2 += 1;
			}else if($rating[$i]==1){
				$star1 += 1;
			}
		}
		$total = $star5+$star4+$star3+$star2+$star1;
		$starPercent = (5*$star5+4*$star4+3*$star3+2*$star2+1*$star1)/$total;
		if($starPercent==5){
			$calPercent = 100 ;
		}else{
			$calPercent = ($starPercent/5)*100 ;
		}
		return $calPercent;
    }
}
