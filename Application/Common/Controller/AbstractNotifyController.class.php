<?php
declare(strict_types = 1);
namespace Common\Controller;

use Think\Hook;
use Common\Behavior\Balance;
use Common\Behavior\AlipaySerialNumber;
use Common\Behavior\Decorate;
use Common\TraitClass\WxNofityTrait;
use Common\TraitClass\OrderNoticeTrait;
use Common\TraitClass\AlipayNotifyTrait;
use Common\TraitClass\WxListenResTrait;
use Common\TraitClass\InitControllerTrait;
use Common\TraitClass\GETConfigTrait;
use Common\TraitClass\PayTrait;
use Think\SessionGet;
/**
 * 通知抽象类
 * @author Administrator
 *
 */
abstract class AbstractNotifyController {
	use GETConfigTrait;
	use OrderNoticeTrait;
	use AlipayNotifyTrait;
	use InitControllerTrait;
	use WxNofityTrait;
	use WxListenResTrait;
	use PayTrait;
	
	
	
	/**
	 * pc 和 wap 回调
	 */
	public function wxNofity() :void
	{
		$this->returnData = file_get_contents('php://input');
		
		$this->args = $this->getTheCustomParamter();
		
		$this->sessionInit();
		
		$payConf = SessionGet::getInstance('pay_config_by_user')->get();
		
		$this->getPayConfig($payConf);
		
		$resource = $this->nofityWx();

		$this->msg($resource);
        $this->tradeNo = $this->args['trade_no'];
		Hook::add( 'aplipaySerial',Decorate::class );
		
		$this->getPayIntegral();
        $this->callBackOrderId = $this->args['out_trade_no'];
		$status = $this->orderWxNotice();
		
		$this->msg( $status );
		
		echo 'SUCCESS';
		die();
		
	}
	
	
	/**
	 * 异步通知
	 */
	public function alipayNotify() :void
	{
		$this->data = $_POST;
		
		$alipayConf = $this->parseResultConf();
		
		$this->msg($alipayConf);
		
		$this->args = $alipayConf;
		
		$this->sessionInit();
		
		$data = $this->alipayResultParse();
		
		$this->msg($data);
		
		$this->tradeNo = $this->data['trade_no'];
		
		Hook::add( 'aplipaySerial',AlipaySerialNumber::class );
		
		$this->getPayIntegral();
		
		$status = $this->orderNotice();
		
		$this->msg($status);
		
		echo "SUCCESS";
		die();
	}
	
	
	/**
	 * 余额支付通知
	 */
	public function balanceNofty() :void
	{
		$this->sessionInit();
		
		Hook::add( 'aplipaySerial', Balance::class );
		
		$this->getPayIntegral();
		
		$status = $this->orderBalanceNotice();
		
		$this->msg($status);
		
		echo 'SUCCESS';die();
	}
	
// 	/**
// 	 * 银联同步回调
// 	 */
// 	public function UnionSynchronous()
// 	{
// 		$this->redirect( 'Home/Order/order_details',[ 'id' => (int)\substr( I( 'orderId' ),24 ) ] );
// 	}
	
	
// 	/**
// 	 * 银联异步回调
// 	 */
// 	public function UnionAsynchronous()
// 	{
// 		$data = I( 'post.' );
// 		if( empty( $data ) ){
// 			E( '非法请求' );
// 		}
// 		if( $data[ 'respCode' ] != '00' && $data[ 'respCode' ] != 'A6' ){
// 			die;
// 		}
		
// 		$info = AcpService::validate( $data );
		
// 		if( !$info ){
// 			die( '验签失败' );
// 		}
// 		echo '验签成功';
// 		$orderId   = (int)\substr( $data[ 'orderId' ],24 );
// 		$OrderData = BaseModel::getInstance( OrderModel::class )->where( [ 'id' => $orderId ] )->getField( 'order_status' );
// 		if( $OrderData !== '0' ){
// 			E( '订单错误' );
// 		}
		
// 		$status = $this->orderNotice( $orderId );
		
// 		//将部分数据写入银联退款表
// 		$refundData                    = [];
// 		$refundData[ 'order_sn_id_r' ] = $data[ 'orderId' ];
// 		$refundData[ 'origQryId' ]     = $data[ 'queryId' ];
// 		$refundData[ 'money' ]         = (float)$data[ 'txnAmt' ] / 100;
// 		$status2                       = M( 'unionrefund' )->add( $refundData );
		
// 		if( !$status ){
// 			Log::write( '订单-' . $orderId . '-修改状态失败' );
// 		}
// 		if( !$status2 ){
// 			Log::write( '订单-' . $orderId . '-插入退款表失败' );
// 		}
// 		die;
// 	}
	
	
	/**
	 * 获取积分比例
	 */
	private function getPayIntegral() :void
	{
		$this->key         = 'integral';
		$payIntegral       = $this->getGroupConfig()[ 'integral_proportion' ];
		$this->payIntegral = $payIntegral;
	}
	
	/**
	 * 余额支付通知
	 */
	private function sendBalanceSms()
	{
		if( M( 'sms_check' )->where( [ 'check_title' => '余额支付提示' ] )->getField( 'status' ) ){
			$userPhone = M( 'user' )->where( [ 'id' => $_SESSION[ 'user_id' ] ] )->getField( 'mobile' );
			$sms       = new MsmFactory();
			$sms->factory( $userPhone,5 );//5为 余额通知的短信模板
		}
	}
	
	private function msg($status)
	{
		if (empty($status)) {
			echo 'ERROR';
			die();
		}
	}
	
	public function __destruct()
	{
		unset($this->data, $this->errorMessage, $this->orderGoodsParseClass, $this->payConfData, $this->payData, $this->payReturnData);
	}
}