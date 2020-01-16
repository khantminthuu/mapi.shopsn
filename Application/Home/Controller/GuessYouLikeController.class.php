<?php
declare(strict_types=1);
namespace Home\Controller;

use Common\TraitClass\InitControllerTrait;
use Common\TraitClass\IsLoginTrait;
use Common\Logic\GoodsLogic;
use Validate\CheckParam;
use Common\Logic\GoodsImagesLogic;
use Common\Tool\Tool;
use Common\Logic\OrderCommentLogic;
use Extend\Algorithm\Correlation;
use Extend\Algorithm\ScoreArray;
use Think\SessionGet;

/**
 * 猜你喜欢
 * @author Administrator
 */
class GuessYouLikeController
{
	use InitControllerTrait;
	use IsLoginTrait;
	
	/**
	 * 方法名称
	 * @var array
	 */
	private $data = [
		'guessLikeByTourist',
		'guessLikeByLogin'
	];
	
	private $messageCheck = [
		'page' => [
			'number' => '必须是数字'
		]
	];
	
	/**
	 * 架构方法
	 * @param array
	 * $args   传入的参数数组
	 */
	public function __construct(array $args = [])
	{
		$this->args = $args;
		
		$this->init();
	}
	
	/**
	 * 猜你喜欢
	 */
	public function guessYouLike() :void
	{
		$checkObj = new CheckParam($this->messageCheck, $this->args);
		
		$this->objController->promptPjax($checkObj->checkParam(), $checkObj->getErrorMessage());
		
		$userId = SessionGet::getInstance('user_id')->get();
		
		$index = $userId ? 1 : 0;
		
		$obj = new \ReflectionObject($this);
		
		$method = $obj->getMethod($this->data[$index]);
		
		$method->setAccessible(true);
		
		$data = $method->invoke($this);
		
		$this->objController->ajaxReturnData($data);
	}
	
	/**
	 * 游客
	 */
	private function guessLikeByTourist() :array
	{
		
		$goodsLogic = new GoodsLogic($this->args);
		
		$goodsData = $goodsLogic->getGuessLikeGoods();
		
		if (empty($goodsData)) {
			return [];
		}
		
		Tool::connect('parseString');
		
		$goodsImage = new GoodsImagesLogic($goodsData, $goodsLogic->getSplitKeyByPId());
		
		$goodsData = $goodsImage->getSlaveDataByMaster();
		
		return $goodsData;
	}
	
	/**
	 * 登录用户
	 */
	private function guessLikeByLogin() :array
	{
		$orderComment = new OrderCommentLogic($this->args);
		
		$score = $orderComment->getGoodsRecommend();
		
		if (count($score) === 0) {
			return [];
		}
		//计算相似度
		$goods = ScoreArray::score($score['me'], $score['otherPerson']);
		
		if (count($goods) === 0) {
			return [];
		}
		
		//筛选相似度高的
		$goods = ScoreArray::filter($goods);
		
		$goodsLogic = new GoodsLogic(['args' => $this->args, 'goods' => $goods]);
		
		$goods = $goodsLogic->getGoodsByScoreCache();
	
		if (empty($goods)) {
			return [];
		}
		
		Tool::connect('parseString');
		
		$goodsImage = new GoodsImagesLogic($goods, $goodsLogic->getSplitKeyByPId());
		
		$goodsData = $goodsImage->getSlaveDataByMaster();
		
		return $goodsData;
	}
}