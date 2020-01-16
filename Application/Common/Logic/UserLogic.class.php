<?php

namespace Common\Logic;

use Common\Model\UserModel;
use Common\Model\UserHeaderModel;
use Common\Model\OrderModel;
use Validate\Children\CheckTelphone;
use Validate\Children\CheckEmail;
use Common\Tool\Extend\CURL;
use Think\Cache;
use Think\SessionGet;
/**
 * @name 用户逻辑处理层
 * 
 * @des 用户逻辑处理层
 * @updated 2017-12-23 11:15
 */
class UserLogic extends AbstractGetDataLogic
{
	/**
	 * 用户信息
	 * @var array
	 */
	private $acccountInfo = [];
	
	/**
	 * 是否已注册（绑定已注册账号）
	 * @return array
	 */
	public function getAcccountInfo()
	{
		return $this->acccountInfo;
	}
	/**
	 * 构造方法
	 *
	 * @param array $data
	 */
	public function __construct(array $data = [], $split = '')
	{
		$this->data = $data;
		
		$this->splitKey = $split;
		
		$this->modelObj = new UserModel();
	}
	
	public function getResult()
	{
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
	 */
	public function getModelClassName() :string
	{
		return UserModel::class;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::hideenComment()
	 */
	public function hideenComment() :array
	{
		return [
		
		];
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::likeSerachArray()
	 */
	public function likeSerachArray() :array
	{
		return [
			UserModel::$userName_d,
		];
	}
	
	/**
	 * 第三方登录验证
	 */
	public function getMessageByBindAccount()
	{
		return [
			UserModel::$mobile_d => [
				'required'      => '请输入手机号码',
				'checkTelphone' => '必须是手机号码',
			]
		];
	}
	
	/**
	 * 验证验证码
	 */
	public function checkPhoneNumber()
	{
		return [
			'verify' => [
				'number' => '必须是数字',
			],
			'mobile' => [
				'required'      => '请输入手机号码',
				'checkTelphone' => '必须是手机号码',
			],
		];
	}
	
	/**
	 * @name 用户登录验证规则
	 * 
	 * @des 用户登录验证规则
	 * @updated 2017-12-20
	 */
	public function getRuleByLogin()
	{
		$comment = $this->modelObj->getComment();
		
		$message = [
			'account'              => [
				'required'          => '请输入账号信息',
				'specialCharFilter' => '账号不能输入特殊字符',
			],
			UserModel::$password_d => [
				'required'          => '请输入' . $comment[ UserModel::$password_d ],
				'specialCharFilter' => $comment[ UserModel::$password_d ] . '不能输入特殊字符',
			],
		];
		
		return $message;
	}

	
	/**
	 * @name 用户登录逻辑
	 * 
	 * @des 用户登录逻辑
	 * @updated 2017-12-20
	 */
	public function userLogin()
	{
		//因为这里是多种情况登录 所以分开检测
		$checkTelObj = new CheckTelphone($this->data['account']);
		
		$isPhone = $checkTelObj->check();
		
		$checkEmail = new CheckEmail($this->data['account']);
		
		$isEmail = $checkEmail->check();
		
		
		
		if ((!$isEmail || !$isPhone) && !$this->data['account']) {
			$this->errorMessage = '用户名或者密码不正确';
			return [];
		}
		
		$args = [
			$this->data['account'],
			$this->data['account'],
			$this->data['account'],
		];
		
		$field = [
			UserModel::$id_d,
			UserModel::$userName_d,
			UserModel::$password_d,
			UserModel::$mobile_d,
		];
		
		$data = $this->modelObj
			->field($field)
			->where(UserModel::$mobile_d . '= "%s" or ' . UserModel::$userName_d . '="%s" or ' . UserModel::$email_d . '="%s"', $args)
			->find();
		
		if (empty($data)) {
			$this->errorMessage = '用户名或者密码不正确';
			return [];
		}
		
		if (($data[ UserModel::$password_d ]) !== md5($this->data[ UserModel::$password_d ])) {
			$this->errorMessage = '用户名或者密码不正确';
			return [];
		}
		SessionGet::getInstance('user_id', $data[ UserModel::$id_d ])->set();
		SessionGet::getInstance('user_name', $data[ UserModel::$userName_d ])->set();
		SessionGet::getInstance('mobile', $data[ UserModel::$mobile_d ])->set();
		//更新最后登录时间
		$this->saveData([
				UserModel::$lastLogon_time_d => time(),
				UserModel::$id_d             => $data[ UserModel::$id_d ],
			]
		);
		
		
		return [
			'token' => '',
		];
	}
	
	/**
	 * 获取用户信息
	 */
	public function getIdentify()
	{
		$data = $this->modelObj->field("user_name,recommendcode")->where(['id'=>SessionGet::getInstance('user_id')->get()])->find();
        $headerModel = new UserHeaderModel();
        $data['user_header'] = $headerModel->where(['user_id'=>SessionGet::getInstance('user_id')->get()])->getField('user_header');
		return $data;
	}
	//获取团队总人数
    public function getTeamCount(){
	    $list = $this->get_arr($_SESSION['user_id']);
	    session("distributionByList",$list);
	    $count = count($list);
	    return $count;
    }
    public function get_arr($id=0){
        $where['recommendcode'] = $id;
        $query = $this->modelObj->field("id")->where($where)->select();
        $arr = array();
        //如果有子节点
        if($query){
            //循环记录集
            foreach ($query as $key=>$row) {
                //调用函数，传入参数，继续查询子节点
                $list = $this->get_arr($row['id']);
                if(!empty($list)){
                    foreach($list as $k=>$v){
                        $arr[] = $v;
                    }
                }
                $arr[] = $row;
            }
            return $arr;
        }
    }
    /**
     * 获取用户信息
     */
    public function getUserByRecommendcode($data)
    {
        if(empty($data['recommendcode'])){
            return $data;
        }
        $user = $this->modelObj->field("user_name,recommendcode")->where(['id'=>$data['recommendcode']])->find();
        $data['recommend_name'] = $user['user_name'];
        return $data;
    }
	/**
	 * @name 注册发送验证码验证规则
	 * 
	 * @des 注册发送验证码验证规则
	 * @updated 2017-12-20
	 */
	public function getRuleByRegSendSms()
	{
		$message = [
			'user_name'          => [
				'required'          => '请输入用户名',
				'specialCharFilter' => '用户名不能输入特殊字符',
			],
			UserModel::$mobile_d => [
				'required'          => '请输入手机号码',
				'checkTelphone' => '手机号码不能输入特殊字符',
			],
		];
		return $message;
	}
	
	/**
	 * @name 注册发送验证码验证规则
	 * 
	 * @des 注册发送验证码验证规则
	 * @updated 2017-12-20
	 */
	public function getRuleByVerifySendSms()
	{
		$message = [
				UserModel::$mobile_d => [
					'required' => '请输入手机号码',
					'checkTelphone' => '手机号码不能输入特殊字符',
				],
		];
		return $message;
	}
	
	/**
	 * @name 注册发送验证码
	 * 
	 * @des 注册发送验证码
	 * @updated 2017-12-16
	 */
	public function checkUserMobileIsExits()
	{
		$args = [
			$this->data['mobile'],
			$this->data['user_name'],
		];
		
		$field = [
			UserModel::$id_d,
		];
		
		$data = $this->modelObj
			->field($field)
			->where(UserModel::$mobile_d . '= "%s" or ' . UserModel::$userName_d . '="%s"', $args)
			->find();
		if (!empty($data)) {
			$this->errorMessage = '手机号或者用户名称已经存在';
			return [];
		}
		return $this->data;
	}
	
	/**
	 * @name 注册发送验证码
	 * 
	 * @des 注册发送验证码
	 * @updated 2017-12-16
	 */
	public function checkUserMobileIsExitsBySendVerfityLogin()
	{
		$args = [
			$this->data['mobile'],
		];
		
		$field = [
						UserModel::$id_d,
		];
		
		$data = $this->modelObj
			->field($field)
			->where(UserModel::$mobile_d . '= %d', $args)
			->find();
		if (empty($data)) {
			$this->errorMessage = '您还没有注册';
			return [];
		}
		return $this->data;
	}
	
	/**
	 * @name 用户注册验证规则
	 * 
	 * @des 用户注册验证规则
	 * @updated 2017-12-20
	 */
	public function getRuleByMobileRegister()
	{
		$message = [
			UserModel::$mobile_d => [
				'required'          => '请输入手机号码',
				'specialCharFilter' => '手机号码不能输入特殊字符',
			],
			'verify'          => [
				'required'          => '请输入短信验证码',
				'specialCharFilter' => '短信验证码不能输入特殊字符',
			],
			'password'          => [
				'required'          => '请输入密码',
			],
		];
		return $message;
	}
	
	/**
	 * @name 账户注册
	 * 
	 * @des 账户注册
	 * @updated 2017-12-20
	 */
	public function userAccountRegister()
	{
		$checkEmail = new CheckEmail($this->data['email']);
		$isEmail = $checkEmail->check();
		
		if (!$isEmail) {
			$this->errorMessage = '邮箱格式不正确';
			return [];
		}
		$veri = new \Think\Verify(array('reset' => false));
		if ($veri->check($this->data['code']) == false) {
			$this->errorMessage = '图形验证码不正确';
			return [];
		}
		if ($this->data['password'] !== $this->data['re_password']) {
			$this->errorMessage = '两次密码设置不一致';
			return [];
		}
		
		$data = array(
			'user_name'   => $this->data['user_name'],
			'mobile'      => '',
			'email'       =>$this->data['email'],
			'password'    => md5($this->data['re_password']),
			'create_time' => time(),
			'update_time' => time(),
		);
		if (!M('user')->add($data)) {
			$this->errorMessage = '注册失败!';
			return [];
		}
		session('short_mobile', null);
		session('short_msg_code', null);
		return [
			'token'     => session_id(),
		];
	}
	/**
	 * @name 用户注册验证规则
	 * 
	 * @des 用户注册验证规则
	 * @updated 2017-12-20
	 */
	public function getRuleByAccountRegister()
	{
		$message = [
			'user_name'          => [
				'required'          => '请输入用户名',
				'specialCharFilter' => '用户名不能输入特殊字符',
			],
			'email'          => [
				'required'          => '请输入邮箱',
			],
			'password'          => [
				'required'          => '请输入密码',
			],
			're_password'          => [
				'required'          => '请输入确认密码',
			],
			'code'          => [
				'required'          => '请输入图形验证码',
				'specialCharFilter' => '图形验证码不能输入特殊字符',
			],
		];
		return $message;
	}
	/**
	 * @name 用户注册验证规则
	 * 
	 * @des 用户注册验证规则
	 * @updated 2017-12-20
	 */
	public function getRuleByUserRegister()
	{
		$message = [
			'mobile'          => [
				'required'          => '请输入手机号',
				'specialCharFilter' => '手机号不能输入特殊字符',
			],
			'user_name'          => [
				'required'          => '请输入用户名',
				'specialCharFilter' => '用户名不能输入特殊字符',
			],
			'verify'          => [
				'required'          => '请输入手机验证码',
				'specialCharFilter' => '手机验证码不能输入特殊字符',
			],
			'email'          => [
				'required'          => '请输入邮箱',
			],
			'password'          => [
				'required'          => '请输入密码',
			],
			're_password'          => [
				'required'          => '请输入确认密码',
			],
		];
		return $message;
	}
	/**
	 * @name 用户注册
	 * 
	 * @des 用户注册
	 * @updated 2017-12-20
	 */
	public function userRegister()
	{
		if ($this->data['password'] !== $this->data['re_password']) {
			$this->errorMessage = '两次密码设置不一致';
			return [];
		}
		$randObj = SessionGet::getInstance('rand_number');
		
		$randNumber = $randObj->get();
		
		if ( $randNumber != $this->data['verify']) {
			$this->errorMessage = '手机验证码已过期';
			return [];
		}
		
		$mobileObj = SessionGet::getInstance('mobile');
		
		$mobile = $mobileObj->get();
		
		if ($mobile != $this->data['mobile']) {
			$this->errorMessage = '请先获取验证码';
			return [];
		}
		
		$status = $this->addData();
		
		if (empty($status)) {
			return [];
		}
		
		$randObj->delete();
		
		$mobileObj->delete();
		
		return [
			'token'     => '',
		];
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getParseResultByAdd()
	 */
	protected function getParseResultByAdd() :array
	{
		$time = time();
		
		$data = array(
			'user_name'   => $this->data['user_name'],
			'mobile'      => $this->data['mobile'],
			'email'       =>$this->data['email'],
			'password'    => md5($this->data['re_password']),
			'recommendcode'=>$this->data['recommendcode'],
			'create_time' => $time,
			'update_time' =>$time,
		);
		
		return $data;
	}
	
	/**
	 * @name 用户短信登录-发送短信 验证规则
	 * 
	 * @des 用户短信登录-发送短信 验证规则
	 * @updated 2017-12-20
	 */
	public function getRuleBySendSmsLogin()
	{
		$message = [
			UserModel::$mobile_d => [
				'required'          => '请输入手机号码',
				'specialCharFilter' => '手机号码不能输入特殊字符',
			],
		];
		return $message;
	}
	
	
	/**
	 * @name 短信登录验证规则
	 * 
	 * @des 短信登录验证规则
	 * @updated 2017-12-20
	 */
	public function getRuleBySmsLogin()
	{
		$message = [
			UserModel::$mobile_d => [
				'required'          => '请输入手机号码',
				'specialCharFilter' => '手机号码不能输入特殊字符',
			],
			'verify'          => [
				'required'          => '请输入短信验证码',
				'specialCharFilter' => '短信验证码不能输入特殊字符',
			],
		];
		return $message;
	}
	/**
	 * @name 短信登录逻辑
	 * 
	 * @des 短信登录逻辑
	 * @updated 2017-12-20
	 */
	public function smsUserLogin()
	{
		
		$randSession = SessionGet::getInstance('rand_number');
		
		if ($randSession->get() != $this->data['verify']) {
			$this->errorMessage = '手机验证码不正确';
			return false;
		}
		
		$mobileSession = SessionGet::getInstance('mobile');
		
		$mobile = $mobileSession->get();
		
		if ($this->data['mobile'] != $mobile) {
			$this->errorMessage = '手机号码与接收验证码手机不匹配';
			return false;
		}
		
		$mobileSession->clear();
		
		$args = [
			$this->data['mobile'],
		];
		
		$field = [
			UserModel::$id_d,
			UserModel::$userName_d,
			UserModel::$mobile_d
		];
		
		$findUserDetails = $this->modelObj
			->field($field)
			->where(UserModel::$mobile_d . '= %d', $args)
			->find();
		if (empty($findUserDetails)) {
			
			$randSession->clear();
			
			$this->errorMessage = '没有找到用户数据';
			return false;
		}
			
		SessionGet::getInstance('user_id', $findUserDetails[ UserModel::$id_d ])->set();
		SessionGet::getInstance('user_name', $findUserDetails[ UserModel::$userName_d ])->set();
		SessionGet::getInstance('mobile', $findUserDetails[ UserModel::$mobile_d])->set();
		
		//更新最后登录时间
		$this->modelObj->save([
				UserModel::$lastLogon_time_d => time(),
				UserModel::$id_d             => $findUserDetails[ UserModel::$id_d ],
			]
		);
		$randSession->clear();
		return true;
	}
	
	/**
	 * @name 第三方登录验证规则
	 * 
	 * @des 第三方登录验证规则
	 * @updated 2017-12-20
	 */
	public function getRuleByOtherLogin()
	{
		$message = [
			'type'          => [
				'required'          => '请输入登录类型',
				'specialCharFilter' => '登录类型不能输入特殊字符',
			],
			'third_account'          => [
				'required'          => '请输入要登录的账号',
				'specialCharFilter' => '登录的账号不能输入特殊字符',
			],
		];
		return $message;
	}
	
	/**
	 * @name 第三方账号绑定验证规则
	 * 
	 * @des 第三方账号绑定验证规则
	 * @updated 2017-12-20
	 */
	public function getRuleBybindOther()
	{
		$message = [
			'type'          => [
				'required'          => '请输入登录类型',
				'specialCharFilter' => '登录类型不能输入特殊字符',
			],
			'third_account'          => [
				'required'          => '请输入第三方登录账号',
				'specialCharFilter' => '登录的账号不能输入特殊字符',
			],
			'account'          => [
				'required'          => '请输入要绑定的手机号',
				'specialCharFilter' => '手机号不能输入特殊字符',
			],
			'password'          => [
				'required'          => '请输入密码',
			],
		];
		return $message;
	}
	
	/**
	 * @name 找回密码验证规则
	 * 
	 * @des 找回密码验证规则
	 * @updated 2017-12-20
	 */
	public function getRuleByBackPwd()
	{
		$message = [
			UserModel::$mobile_d => [
				'required'          => '请输入手机号码',
				'specialCharFilter' => '手机号码不能输入特殊字符',
			],
			'verify'          => [
				'required'          => '请输入短信验证码',
				'specialCharFilter' => '短信验证码不能输入特殊字符',
			],
			'password'          => [
				'required'          => '请输入密码',
			],
			're_password'          => [
				'required'          => '请输入确认密码',
			],
		];
		return $message;
	}
	/**
	 * @name 找回密码逻辑
	 * 
	 * @des 找回密码逻辑
	 * @updated 2017-12-20
	 */
	public function backUserPwd()
	{

		if ($this->data['password'] !== $this->data['re_password']) {
			$this->errorMessage = '两次密码设置不一致';
			return [];
		}
		
		$randNumberSession = SessionGet::getInstance('rand_number');
		
		if ($randNumberSession->get() != $this->data['verify']) {
			$this->errorMessage = '手机验证码不正确或已过期';
			return false;
		}
		
		$mobileSession = SessionGet::getInstance('mobile');
		
		$mobile = $mobileSession->get();
		
		if ($this->data['mobile'] != $mobile) {
			$this->errorMessage = '手机号码与接收验证码手机不匹配';
			return false;
		}
		
		$mobileSession->clear();
		
		
		
		$field = [
			UserModel::$id_d,
		];
		$args = [
			$this->data['mobile'],
		];
		//#TODO 查询有没有该账号
		$data = $this->modelObj
			->field($field)
			->where(UserModel::$mobile_d . '= %d', $args)
			->find();
		if (empty($data)) {
			$randNumberSession->clear();
			$this->errorMessage = '手机号不正确';
			return false;
		}
		//#TODO 修改用户的密码
		$data['password'] = md5($this->data['re_password']);
		$ret = $this->modelObj
			->where(UserModel::$id_d . '= %d', [$data[UserModel::$id_d]])
			->save($data);
		if (false === $ret) {
			$randNumberSession->clear();
			$this->errorMessage = '找回失败，请重试!';
			return false;
		}
		
		$randNumberSession->clear();
		return true;
	}
	/**
	 * @name 获取个人信息逻辑
	 * 
	 * @des 获取个人信息逻辑
	 * @updated 2017-12-21
	 */
	public function getUserInfo()
	{
		$userId = session('user_id');
		if(empty($userId)){
			$this->errorMessage = '暂无该用户信息';
			return [];
		}
		$field = [
			UserModel::$id_d,
		];
		$args = [
			$userId
		];
		//#TODO 查询有没有该账号
		$data = $this->modelObj
			->field($field)
			->where(UserModel::$id_d . '= "%s"', $args)
			->find();
		if (empty($data)) {
			$this->errorMessage = '暂无该用户信息';
			return [];
		}
		$userData = $this->modelObj->userDetails($userId);
		
		if($userData === false){
			$this->errorMessage = '暂无该用户信息';
			return [];
		}
		
		$userData['birthday'] = $userData['birthday'] *1000;
		
        $total_integra  = bcadd($userData['integral'], $userData['integral_use']);

        $userData['img_url'] = C('img_url');
		return $userData;
	}
   
	/**
	 * @name 修改密码验证规则
	 * 
	 * @des 修改密码验证规则
	 * @updated 2017-12-21
	 */
	public function getRuleByModifyPassword()
	{
		$message = [
			'password'          => [
				'required'          => '请输入原密码',
			],
			'new_password1'          => [
				'required'          => '请输入新密码',
			],
			'new_password2'          => [
				'required'          => '请输入确认密码',
			],
		];
		return $message;
	}
	/**
	 * @name 修改密码逻辑
	 * 
	 * @des 修改密码逻辑
	 * @updated 2017-12-21
	 */
	public function modifyPassword()
	{
		if ($this->data['new_password1'] !== $this->data['new_password2']) {
			$this->errorMessage = '两次密码设置不一致';
			return [];
		}
		$userId = session('user_id');
		if(empty($userId)){
			$this->errorMessage = '修改失败,请重试!';
			return [];
		}
		//#TODO 查看该用户是否存在
		$ret = $this->modelObj->findUserExist($userId);
		if(false == $ret){
			$this->errorMessage = '修改失败,请重试!';
			return [];
		}
		$userData = $this->modelObj->editPassword($userId, $this->data['password'], $this->data['new_password2']);
		if(true !== $userData){
			$this->errorMessage = $userData;
			return [];
		}
		return [
			'token'     => '',
		];
	}
	/**
	 * @name 修改个人资料验证规则
	 * 
	 * @des 修改个人资料验证规则
	 * @updated 2017-12-22
	 */
	public function getRuleByEditUserInfo()
	{
		$message = [
			'nick_name' => [
				'required' => '请输入昵称',
				'specialCharFilter' => '请不要输入特殊字符',
			],
		];
		return $message;
	}
	/**
	 * @name 修改个人资料逻辑
	 * 
	 * @des 修改个人资料逻辑
	 * @updated 2017-12-22
	 */
	public function editUserInfo()
	{   
		$post = $this->data;
		$userId = SessionGet::getInstance('user_id')->get();
		if(!empty($post['email'])){
			$checkEmail = new CheckEmail($post['email']);
			$isEmail = $checkEmail->check();
			if (!$isEmail) { 
				return array("status"=>0,"message"=>'请输入正确的邮箱',"data"=>"");
			}
			//判断有没有用户绑定该邮箱了，如果绑定了就不能再绑定了
			$retData = $this->modelObj->where(['id'=>['neq'=>$userId], 'email'=>$post['email']])->field('id')->find();
			if(!empty($retData)){
				return array("status"=>0,"message"=>'该邮箱已经绑定过',"data"=>"");
			}
		}
		//#TODO 查看该用户是否存在
		$ret = $this->modelObj->findUserExist($userId);
		if(false == $ret){
			return array("status"=>0,"message"=>'该用户不存在',"data"=>"");
		}
		//#TODO 先修改资料，
		$post['update_time'] = time();
		$post['birthday'] = strtotime($post['birthday']);
		$where['id'] = $userId;
		
		$this->modelObj->startTrans();
		$res = $this->modelObj->userSave($where,$post);
		
		if ($res === false) {
			$this->modelObj->rollback();
			return array("status"=>0,"message"=>'修改失败',"data"=>"");
		}
		
		$user_header = null;
		
		if (!empty($post['img_new'])) {
            $user_header = $post['img_new'];  
        }
       
        if ($user_header === null) { 
        	
        	$this->modelObj->commit();
        	return array("status"=>1,"message"=>'修改成功',"data"=>"");
        }
       
        $header = M("UserHeader")->where(['user_id'=>$userId])->find();
        
        if (empty($header)) {
        	$res =  M("UserHeader")->add(['user_id'=>$userId,"user_header"=>$user_header]);
        }else{
        	$res =  M("UserHeader")->where(['user_id'=>$userId])->save(["user_header"=>$user_header]);
        }
        if (!$res) {
        	$this->modelObj->rollback();
            return array("status"=>0,"message"=>'修改失败',"data"=>"");
        }
        if (!empty($header)) {
            $curlFile = new CURL([
            	'fileName' => $header['user_header']
            ], C('unlink_image_no_thumb'));
            $curlFile->deleteFile();
        }
        $this->modelObj->commit();
		return array("status"=>1,"message"=>'修改成功',"data"=>"");
	}
	
	/**
	 * @name 找回密码发送短信
	 * 
	 * @des 找回密码发送短信
	 * @updated 2017-12-20
	 */
	public function editMobileSendSms()
	{
		//检测该用户手机号是否正确
		$checkTelObj = new CheckTelphone($this->data['mobile']);
		
		$isPhone = $checkTelObj->check();
		
		if (!$isPhone || !$this->data['mobile']) {
			$this->errorMessage = '手机号不正确';
			return [];
		}
		
		//#TODO 查看该手机号是否存在
		$ret = $this->modelObj->findMobileUserExist($this->data['mobile']);
		if(true == $ret){
			$this->errorMessage = '该手机号已绑定其他账号!';
			return [];
		}
		$data = $this->send_msg($this->data['mobile']);
		if (!$data) {
			$this->errorMessage = '发送失败,请重试';
			return [];
		}
		return [
			'test_code' => session('short_msg_code') . '，暂时使用,为了省短信',
			'token'     => session_id(),
		];
	}
	/**
	 * @name 用户短信登录发送短信
	 * 
	 * @des 用户短信登录发送短信
	 * @updated 2017-12-20
	 */
	public function getRuleByEditMobileSendSms()
	{
		//检测该用户手机号是否正确
		$checkTelObj = new CheckTelphone($this->data['mobile']);
		
		$isPhone = $checkTelObj->check();
		
		if (!$isPhone || !$this->data['mobile']) {
			$this->errorMessage = '手机号不正确';
			return [];
		}
		//#TODO 查看该手机号是否存在
		$ret = $this->modelObj->findMobileUserExist($this->data['mobile']);
		if(true == $ret){
			$this->errorMessage = '该手机号已绑定其他账号!';
			return [];
		}
		$data = $this->send_msg($this->data['mobile']);
		if (!$data) {
			$this->errorMessage = '发送失败,请重试';
			return [];
		}
		return [
			'test_code' => session('short_msg_code') . '，暂时使用,为了省短信',
			'token'     => session_id(),
		];
	}
	/**
	 * @name 修改手机号验证规则
	 * 
	 * @des 修改手机号验证规则
	 * @updated 2017-12-21
	 */
	public function getRuleByEditMobile()
	{
		$message = [
			UserModel::$mobile_d => [
				'required'          => '请输入手机号码',
				'specialCharFilter' => '手机号码不能输入特殊字符',
			],
			'verify'          => [
				'required'          => '请输入短信验证码',
				'specialCharFilter' => '短信验证码不能输入特殊字符',
			],
		];
		return $message;
	}
	/**
	 * @name 修改手机号逻辑
	 * 
	 * @des 修改手机号逻辑
	 * @updated 2017-12-21
	 */
	public function editUserBindMobile()
	{
		$userId = session('user_id');
		if(empty($userId)){
			$this->errorMessage = '修改失败,请重试!';
			return [];
		}
		if (empty(session('short_msg_code'))) {
			$this->errorMessage = '手机验证码已过期';
			return [];
		}
		if (session('short_mobile') != $this->data['mobile']) {
			$this->errorMessage = '请先获取验证码';
			return [];
		}
		if (session('short_msg_code') != $this->data['verify']) {
			$this->errorMessage = '手机验证码不正确';
			return [];
		}
		//#TODO 查看该手机号是否存在
		$ret = $this->modelObj->findMobileUserExist($this->data['mobile']);
		if(true == $ret){
			$this->errorMessage = '该手机号已绑定其他账号!';
			return [];
		}
		//#TODO 查看该用户是否存在
		$ret = $this->modelObj->findUserExist($userId);
		if(false == $ret){
			$this->errorMessage = '修改失败,请重试!';
			return [];
		}
		//#TODO 修改手机号
		$userData = $this->modelObj->editUserMobileLogin($userId, $this->data['mobile']);
		if(false === $userData){
			$this->errorMessage = '修改失败,请重试!';
			return [];
		}
		session('short_mobile', null);
		session('short_msg_code', null);
		return [
			'token'     => session_id(),
		];
	}
	
	/**
	 * @name 发送短信
	 * 
	 * @des 发送短信
	 * @updated 2017-12-20
	 */
	public function sendMsg()
	{		
	}
	
    
    /**
     * 上传图片验证
     * @return []
     */
    public function getMessageByPic()
    {
    	$message = [
    		'tmp_name' => [
    			'required' => '请上传图片',
    		],
    	];
    	return $message;
    }
    
    /**
     * 验证图片宽高度
     * @return bool
     */
    public function checkImageWidthAndHeight()
    {
    	
    	$field = "key,config_value";
    	
    	$header_min_width = M("system_config")->field($field)->where(['key'=>"header_min_width"])->getField("config_value");
    	$header_max_width = M("system_config")->field($field)->where(['key'=>"header_max_width"])->getField("config_value");
    	$header_max_height = M("system_config")->field($field)->where(['key'=>"header_max_height"])->getField("config_value");
    	$header_min_height = M("system_config")->field($field)->where(['key'=>"header_min_height"])->getField("config_value");
    	if (empty($header_min_height)) {
    		$this->errorMessage = '不存在 广告位配置';
    		return false;
    	}
    	
    	$imageInfo = getimagesize($_FILES['adv_content']['tmp_name']);
    	
    	$width = $imageInfo[0];
    	
    	$height = $imageInfo[1];
    	
    	if ($width > $header_max_width|| $width < $header_min_width) {
    		
    		$this->errorMessage = '宽度必须介于'.$header_min_width.'~'.$header_max_width.'之间，此图宽度'.$width;
    		
    		return false;
    	}
    	
    	if ($height > $header_max_height || $width < $header_min_height) {
    		
    		$this->errorMessage = '高度必须介于'.$header_min_height.'~'.$header_max_height.'之间，此图高度'.$height;
    		
    		return false;
    	}
    	return true;
    }
    
    /**
     * qq 授权登录
     */
    public function addUserByQQ()
    {
    	$randNumberSession = SessionGet::getInstance('rand_number');
    	$randNumber = $randNumberSession->get();
    	if ($randNumber != $this->data['verify']) {
    		$this->errorMessage = '手机验证码不正确';
    		return false;
    	}
    	
    	$randNumberSession->delete();
    	
    	$mobile = SessionGet::getInstance('mobile')->get();
    	
    	if (empty($mobile) || $mobile != $this->data['mobile']) {
    		$this->errorMessage = '手机号码不一致或数据错误';
    		return false;
    	}
    	
    	$userInfoQQ = SessionGet::getInstance('user_info_qq')->get();
    	
    	if (empty($userInfoQQ)) {
    		$this->errorMessage = '授权数据错误';
    		return false;
    	}
    	
    	$data = $this->getUserByMobile();
    	
    	$this->acccountInfo = $data;
    	
    	$flag = 0;
    	
    	$status = 0;
    	try {
	    	//未注册 绑定qq
	    	if (empty($data)) {
	    		
	    		
	    		$mobile = SessionGet::getInstance('mobile')->get();
	    		
	    		$data = $_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR'].time() . rand();
	    		
	    		$sha1 = md5(sha1($data, true));
	    		
	    		$userName = 'ys_'.substr($sha1, mt_rand(6, 20), 12);
	    		
	    		$add = [];
	    		
	    		$time = time();
	    		
	    		$add[UserModel::$userName_d] = $userName;
	    		
	    		$add[UserModel::$nickName_d] = 'ys_'.substr($mobile, 0, -5).substr($sha1, 29, 3);
	    		
	    		$add[UserModel::$email_d] = $mobile.'@qq.com';
	    		
	    		$add[UserModel::$mobile_d] = $mobile;
	    		
	    		$add[UserModel::$createTime_d] = $time;
	    		
	    		$add[UserModel::$password_d] = $sha1;
	    		
	    		$add[UserModel::$sex_d] = $userInfoQQ['gender'] === '男' ? 1 : 0;
	    		
	    		$add[UserModel::$updateTime_d] = $time;
	    		
	    		$this->modelObj->startTrans();
	    		
	    		$status = $this->modelObj->add($add);
	    		
	    		$flag = 1;
	    		
	    	} 
    	} catch (\Exception $e) {
    		$this->errorMessage = $e->getMessage();
    		$this->modelObj->rollback();
    		return false;
    	}
    	
    	if ($flag === 0) {
    		SessionGet::getInstance('user_id',$data['id'])->set();
    		return true;
    	}
    	
    	if (!empty($status) && $flag === 1) {
    		SessionGet::getInstance('user_id',$status)->set();
    		return true;
    	}
    	
    	$this->errorMessage = '绑定失败';
    	$this->modelObj->rollback();
    	
    	return false;
    }
    
    /**
     * 根据手机号获取用户信息
     */
    public function getUserByMobile()
    {
    	$mobile = $this->data['mobile'];
    	
    	$cache = Cache::getInstance('', ['expire' => 60]);
    	
    	$key = $mobile.'_sdf';
    	
    	$data = $cache->get($key);
    	
    	if (!empty($data)) {
    		return $data;
    	}
    	
    	$data = $this->modelObj->field(UserModel::$id_d)->where(UserModel::$mobile_d.'=:m_id')
	    	->bind([':m_id' => $mobile])
	    	->find();
    	
	    if (empty($data)) {
	    	return [];
	    }
	    
	    $cache->set($key, $data);
	    
	    return $data;
    }
    public function getTeamByUser(){
        //获取一级下属
        $one_where['recommendcode'] = $_SESSION['user_id'];
        $field = 'id,user_name,nick_name,FROM_UNIXTIME(create_time,\'%Y-%m-%d %H:%i:%s\') as create_time,recommendcode';
        $one = $this->modelObj->field($field)->where($one_where)->select();
        if (empty($one)) {
            $this->errorMessage = "暂无数据";
            return false;
        }
        $one_data = $this->number_of_calculations($one);
        $data['one_data'] = $one_data;
        //获取二级下属
        $two_where = $this->getWhere($one_data);
        $two = $this->modelObj->field($field)->where($two_where)->select();
        if (empty($two)) {
            return $data;
        }
        $two_data = $this->number_of_calculations($two);
        $data['two_data'] = $two_data;
        //获取三级下属
        $three_where = $this->getWhere($two_data);
        $three = $this->modelObj->field($field)->where($three_where)->select();
        if (empty($three)) {
            return $data;
        }
        $three_data = $this->number_of_calculations($three);
        $data['three_data'] = $three_data;
        //获取四级下属
        $four_where = $this->getWhere($three_data);
        $four = $this->modelObj->field($field)->where($four_where)->select();
        if (empty($four)) {
            return $data;
        }
        $four_data = $this->number_of_calculations($four);
        $data['four_data'] = $four_data;
        //获取五级下属
        $five_where = $this->getWhere($four_data);
        $five = $this->modelObj->field($field)->where($five_where)->select();
        if (empty($five)) {
            return $data;
        }
        $five_data = $this->number_of_calculations($five);
        $data['five_data'] = $five_data;
        return $data;
    }
    //组装where
    public function getWhere($arr){
        $ids = array_column($arr,'id');
        $where['recommendcode'] = array("IN",$ids);
        return $where;
    }
    //计算推广人数
    public function number_of_calculations($data){
        $orderModel = new OrderModel();
        $userHeaderModel = new UserHeaderModel();
        foreach($data as $key=>$value){
            $where['recommendcode'] = $value['id'];
            $data[$key]['extension'] = $this->modelObj->where($where)->count();
            $data[$key]['consumption'] = $orderModel->where(['user_id'=>$value['id']])->sum("price_sum");
            $data[$key]['order_count'] = $orderModel->where(['user_id'=>$value['id']])->count();
            $data[$key]['user_header'] = $userHeaderModel->where(['user_id'=>$value['id']])->getField('user_header');
        }
        return $data;
    }
}
