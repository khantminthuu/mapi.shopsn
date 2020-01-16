<?php
namespace Common\Pay\Component;

use Extend\Wxpay\WxPayConfPub;
use Extend\Wxpay\Pay\UnifiedOrderPub;
use Think\SessionGet;

/**
 * 支付公共组件
 * @author Administrator
 *
 */
trait PayGenialPublicTrait
{
	/**
	 * 微信订单号
	 * @var string
	 */
	private $orderSnId = '';
	
	/**
	 * 支付订单数据
	 * @var array
	 */
	private $orderData = [];
	
	/**
	 * 数据配置
	 * @var array
	 */
	private $config = [];
	
	/**
	 * 支付金额
	 * @var float
	 */
	private $priceSum = 0;	
	
	/**
	 * 商品描述
	 * @var string
	 */
	private $description = '';
	
	
	/**
	 * 支付组件
	 * @param float $money
	 * @return string[]|number[]|string[]|string[][]|number[][]|mixed[][]
	 */
	public function component()
	{
		$unifiedOrderPub = new UnifiedOrderPub();
		
		$urlNofity = WxPayConfPub::$NOTIFY_URL ;
		
		$token = $this->config['token'];
		
		$config = $this->config;
		
		unset($config['token']);
		
		SessionGet::getInstance('pay_config_by_user', $config)->set();
		
		$unifiedOrderPub->setParameter("body", $this->description); // 商品描述
		$unifiedOrderPub->setParameter("out_trade_no", $this->orderSnId); // 商户订单号
		$unifiedOrderPub->setParameter("total_fee", $this->priceSum * 100); // 总金额
		$unifiedOrderPub->setParameter("notify_url", $urlNofity); // 通知地址
		$unifiedOrderPub->setParameter("trade_type", "MWEB"); // 交易类型
		$unifiedOrderPub->setParameter("attach", $token);
		
		// 获取统一支付接口结果
		$unifiedOrderResult = $unifiedOrderPub->getResult();
		
		// 商户根据实际情况设置相应的处理流程
		if ($unifiedOrderResult["return_code"] == "FAIL") {
			// 商户自行增加处理流程
			return [
				'message' =>'通信出错：' . $unifiedOrderResult['return_msg'],
				'status'  => 0,
				'data'    => '',
			];
		} elseif ($unifiedOrderResult["result_code"] == "FAIL") {
			// 商户自行增加处理流程
			return [
				'message'=> '错误代码：' . $unifiedOrderResult['err_code'] . '错误代码描述：' . $unifiedOrderResult['err_code_des'],
				'status'=> 0,
				'data'=> ''
			];
		}
		return[
			'status'=> 1,
			'data'=> $unifiedOrderResult['mweb_url'].'&redirect_url='.urlencode($this->config['return_url'].'/'.$this->orderData['order_id']),
			'message'=> '成功'
		];
	}
}