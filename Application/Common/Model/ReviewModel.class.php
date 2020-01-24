<?php
namespace Common\Model;
use Common\Model\User;

class ReviewModel extends  BaseModel
{
	private static $obj;

	public static $id_d;		//id

	public static $userId_d;	//user_id

	public static $review_d;	//review from user

	public static $rating_d;	//rating (1,2,3,4,5)

	public static $goodsId_d;	//goods id

	public static $Timestamp_d;	//timestamp


	public static function getInitnation()
	{
		$class = __CLASS__;
		return self::$obj = empty(self::$obj instanceOf $class)? new self() :self::$obj;
	}
	public function getModel($arr)
	{
		$where['goods_id'] = $arr['id'];

		$totalReviews = $this->where($where)->count();

		$reviews = $this->where($where)->field('rating')->select();

		$ratingCalc = $this->calRating($reviews);
	}

	public function calRating1($data)
	{
		foreach ($data as $key => $value) {
			switch ($value['rating']) {
				case '5':
				$arr['star5'] = 1;
				break;
				case '4':
				$star4= 1;
				break;
				case '3':
				$star3= 1;
				break;
				case '2':
				$star2= 1;
				break;
				case '1':
				$star1= 1;
				break;
				default:
				# code...
				break;
			}		
		}
	}
	public function calRating($arr)
	{
		$count =  count($arr,COUNT_NORMAL);
		for($i=0;$i<=$count;$i++){
			if($arr[$i]['rating']==5){
				$star5 += 1;
			}else if($arr[$i]['rating']==4){
				$star4 += 1;
			}else if($arr[$i]['rating']==3){
				$star3 += 1;
			}else if($arr[$i]['rating']==2){
				$star2 += 1;
			}else if($arr[$i]['rating']==1){
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
		return $starPercent;
	}
	

}