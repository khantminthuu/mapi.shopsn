<?php
namespace Home\Controller;

use Common\TraitClass\InitControllerTrait;
use Common\TraitClass\GETConfigTrait;
use Common\Tool\Extend\CURL;
use Common\Logic\UserAuthLogic;
use Think\SessionGet;

/**
 * QQ 登录
 * @author 王强
 *
 */
class QQThirdPartyLoginController
{
	use InitControllerTrait;
	
	use GETConfigTrait;
	
	private $loginURL = 'https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id=';
	
	private $callBackURL = 'https://graph.qq.com/oauth2.0/token';
	
	private $graphURL = 'https://graph.qq.com/oauth2.0/me';
	
	private $userInfo = 'https://graph.qq.com/user/get_user_info';
	
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
	 * QQ登录
	 */
	public function qqLogin()
	{
		$this->key = 'qq_party_login';
		
		$config = $this->getGroupConfig();
		
		$state = md5(uniqid(rand(), TRUE)); //CSRF protection
		
		SessionGet::getInstance('state', $state)->set();
		
		$url = C('qq_redirect_url');
		
		SessionGet::getInstance('qq_url', $url)->set();
		
		$loginURL = $this->loginURL
				. $config['app_id'] . '&redirect_uri=' . urlencode($url)
				. '&state=' . $state
				. '&scope=get_user_info&display=mobile';
		
		$this->objController->ajaxReturnData($loginURL);
		
	}
	
	/**
	 * qq 回调
	 */
	public function qqLoginCallBack()
	{
	
		$state = SessionGet::getInstance('state')->get();
		
		if ($this->args['state'] !== $state) {
			header('Location:'.C('auth_error_url').'/'.urlencode('参数不合法'));die();
		}
		
		$param = $this->getCallBack();
		
		if (empty($param)) {
			header('Location:'.C('auth_error_url').'/'.urlencode('获取access_token失败'));die();
		}
		
		$data = $this->getOpenId($param);
		
		if (empty($data)) {
			header('Location:'.C('auth_error_url'));die();
		}
		
		$param['identity_type'] = '1';
		
		$param = array_merge($param, $data);
		
		$authLogic = new UserAuthLogic($param);
		
		$result = $authLogic->getResult();
		
		$refreshToken = [];
		
		//标记变量
		$flag = 0;
		
		$isLate = $authLogic->getIsLate();
		
		if (!empty($result) && $isLate === false) {
			SessionGet::getInstance('user_id', $result['user_id'])->set();
			header('Location:'.C('mobile').'?auth_token='.session_id());die();
		}
		if (!empty($result) && $isLate === true) {
			$flag = 1;
			//授权过期，重新授权
			$refreshToken = $this->refreshAuth($result);
		}
		
		if ($flag === 1 && empty($refreshToken)) {
			header('location:'.C('auth_error_url').'/'.urlencode('重新授权失败'));die();
		}
		
		$status = 0;
		
		$isUpdate = 0;
		
		if ($flag === 1 && !empty($refreshToken)) {
			
			$result['credential'] = $refreshToken['access_token'];
			
			$result['expires_in'] = $refreshToken['expires_in'];
			
			$result['refresh_token'] = $refreshToken['refresh_token'];
			
			$result['update_at'] = time();
			
			$authLogic->setData($result);
			
			$status = $authLogic->saveData();
			
			$isUpdate = 1;
		}
		
		if ($status === 0 && $isUpdate === 1) {
			header('location:'.C('auth_error_url').'/'.urlencode('保存授权信息失败'));die();
		}
		
		if ($status !== 0 && $isUpdate === 1) {
			SessionGet::getInstance('user_id', $result['user_id'])->set();
			header('Location:'.C('mobile'));die();
		}
		
		$userInfo = $this->getUeserInfo($param['access_token'], $data['openid']);
		
		if (empty($userInfo)) {
			header('location:'.C('auth_error_url').'/'.urlencode('获取open_id失败'));die();
		}
		
		SessionGet::getInstance('open_id_data', $data)->set();
		
		
		SessionGet::getInstance('user_info_qq', $userInfo)->set();
		
		
		SessionGet::getInstance('access_token', $param)->set();
		
		
		SessionGet::getInstance('identitifer', '1')->set();
		
		header('Location:'.C('add_user_info').'/111');die();
		
	}
	
	/**
	 * 获取callback
	 * @return number[]|array[]|NULL[]|array
	 */
	private function getCallBack()
	{
		if (empty($_SESSION['qq_url'])) {
			return [];
		}
		
		$this->key = 'qq_party_login';
		
		$config = $this->getGroupConfig();
		
		$param = [
			'grant_type' => 'authorization_code',
		 	'client_id' => $config['app_id'], 
			'redirect_uri'=> $_SESSION['qq_url'],
			'client_secret'=> $config['qq_key'], 
			'code' => $this->args['code'],
		];
		
		file_put_contents('./Log/qq/token_url_id.txt', print_r($param, true)."\r\n", FILE_APPEND);
		
		$response = (new CURL($param, $this->callBackURL))->curlByGet();
		
		file_put_contents('./Log/qq/qq_response_id.txt', print_r($response, true)."\r\n", FILE_APPEND);
		
		if (strpos($response, "callback") !== false)
		{
			$lpos = strpos($response, "(");
			$rpos = strrpos($response, ")");
			$response  = substr($response, $lpos + 1, $rpos - $lpos -1);
			$msg = json_decode($response);
			if (isset($msg->error))
			{
				return [];
			}
		}
		
		$params = array();
		parse_str($response, $params);
		
		file_put_contents('./Log/qq/qq_param_id.txt', print_r($params, true));
		
		return $params;
	}
	
	/**
	 * 获取open id
	 * @param array $data
	 */
	private function getOpenId(array $data)
	{
		$args = [
			'access_token' => $data['access_token']
		];
				
		$str  = (new CURL($args, $this->graphURL))->curlByGet();
		
		if (strpos($str, "callback") !== false)
		{
			$lpos = strpos($str, "(");
			$rpos = strrpos($str, ")");
			$str  = substr($str, $lpos + 1, $rpos - $lpos -1);
		}
		
		$user = json_decode($str, true);
		
		if (isset($user['error']))
		{
			return [];
		}
		
		return $user;
	}
	
	/**
	 * 获取用户数据
	 */
	private function getUeserInfo($accesToken, $openId)
	{
		$this->key = 'qq_party_login';
		
		$config = $this->getGroupConfig();
		
		$data = [
			'access_token' => $accesToken,
			'oauth_consumer_key' => $config['app_id'],
			'openid' => $openId
		];
		
		$json  = (new CURL($data, $this->userInfo))->curlByGet();
		
		$json = json_decode($json, true);
		
		return $json;
	}
	
	/**
	 * 重新授权
	 */
	private function refreshAuth(array $data)
	{
		
		$this->key = 'qq_party_login';
		
		$config = $this->getGroupConfig();
		
		$param = [
			'grant_type' => 'refresh_token',
			'client_id' => $config['app_id'],
			'client_secret'=> $config['qq_key'],
			'refresh_token' => $data['refresh_token'],
		];
		
		$response  = (new CURL($param, $this->callBackURL))->curlByGet();
		
		if (strpos($response, "callback") !== false) {
			return [];
		}
		
		$param = [];
		
		parse_str($response, $param);
		
		return $param;
	}
}