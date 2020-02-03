<?php
namespace Common\Model;


/**
 * 用户地址模型
 */
class UserModel extends BaseModel
{

    private static $obj;


    public static $id_d;	//用户编号
    
    public static $mobile_d;	//电话号码
    
    public static $createTime_d;	//创建时间
    
    public static $status_d;	//账号状态【1正常   0禁用】
    
    public static $updateTime_d;	//更新时间
    
    public static $openId_d;	//openid是公众号的普通用户的一个唯一的标识
    
    public static $password_d;	//密码
    
    public static $userName_d;	//用户名
    
    public static $nickName_d;	//昵称
    
    public static $birthday_d;	//生日
    
    public static $idCard_d;	//身份证号码
    
    public static $email_d;	//邮箱
    
    public static $sex_d;	//性别【0女，1男】
    
    public static $lastLogon_time_d;	//上次登录时间
    
    public static $salt_d;	//加盐字段【： 和密码进行加密，增加密码强度】
    
    public static $recommendcode_d;	//推荐人编码
    
    public static $validateEmail_d;	//是否验证邮箱【0没有， 1已验证】
    
    public static $memberDiscount_d;	//折扣率
    
    public static $pId_d;	//父级会员编号

	public static $cardPositive_d;	//身份证正面

	public static $cardSide_d;	//身份证反面

	public static $personName_d;	//真实姓名

	public static $authentication_d;	//身份认证【0，待认证 1成功， 2失败】


	public static $uId_d;	//follow
    
    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
    }
	
	//获取用户的详细信息
	public function userDetails($userid){
		$where = array('a.id' => $userid);
		$ret = $this->alias("a")
			->field('a.nick_name,a.email,a.mobile,a.user_name,a.sex,a.birthday, IFNULL(b.user_header, "") as user_header')
			->join('__USER_HEADER__ as b on b.user_id = a.id', 'LEFT')
			->where($where)
			->find();
		if(false === $ret){
			return false;
		}
		return $ret;
	}
	/**
	 * @name 修改密码
	 * 
	 * @des 修改密码
	 * @updated 2017-12-16 18:41
	 */
	public function editPassword($userId, $password, $newPassword){
		//查询有没有该用户，然后对比一下密码
		$findUserOldPassword = $this
			->where(['id' => $userId])
			->getField('password');
		if(empty($findUserOldPassword)){
			return '原密码不正确';
		}
		if ($findUserOldPassword !== md5($password)){
			return '原密码不正确';
		}
		if ($findUserOldPassword == md5($newPassword)){
			return true;
		}
		//修改密码
		$chang_password = $this
			->where(['id'=> $userId])
			->save(['password'=>md5($newPassword)]);
		if(false === $chang_password){
			return false;
		}
		return true;
	}
	/**
	 * @name 修改手机号绑定
	 * 
	 * @des 修改手机号绑定
	 * @updated 2017-12-16 19:42
	 */
	public function editUserMobileLogin($userId, $mobile){
		$findUserOldMobile = $this
			->where(['id' => $userId])
			->getField('mobile');
		if(empty($findUserOldMobile)){
			return false;
		}
		if ($findUserOldMobile == $mobile){
			return true;
		}
		$ret = $this->where(['id'=>$userId])->save(['mobile'=>$mobile]);
		if(false === $ret){
			return false;
		}
		return true;
	}
	/**
	 * @name 按照用户ID查询是不是已经存在该用户了
	 * 
	 * @des 按照用户ID查询是不是已经存在该用户了
	 * @updated 2017-12-16 19:42
	 */
	public function findUserExist($userId){
		$finds = $this->where(UserModel::$id_d . '= "%s"', $userId)->getField('id');
		if (empty($finds)) {
			return false;
		}
		return true;
	}
	/**
	 * @name 查询该手机号是否已经注册账户了
	 * 
	 * @des查询该手机号是否已经注册账户了
	 * @updated 2017-12-16 21:42
	 */
	public function findMobileUserExist($mobile){
		$finds = $this->where(array('mobile' => $mobile))->getField('mobile');
		if (empty($finds)) {
			return false;
		}
		return true;
	}
    /**
     * 得到用户的姓名
     *
     */
	public function getUserName($userId,$anonymous){

	    $where = [
	        'status' =>['EQ','1'],
	        'id' =>['EQ',$userId],
        ];
	    $field = 'user_name';
	    $userName = $this->where($where)->field($field)->find()['user_name'];
	    if ($anonymous == 1){
	        return $this->getUserAnonymous($userName);
        }
        return $userName;
	}
    /**
     * 如果是匿名评价则对客户的姓名进行处理
     *
     */
    function getUserAnonymous($user_name){
        $strlen     = mb_strlen($user_name, 'utf-8');
        $firstStr     = mb_substr($user_name, 0, 1, 'utf-8');
        $lastStr     = mb_substr($user_name, -1, 1, 'utf-8');
        return $strlen == 2 ? $firstStr . str_repeat('*', mb_strlen($user_name, 'utf-8') - 1) : $firstStr . str_repeat("*", $strlen - 2) . $lastStr;
    }

    public function getUserIntegral($userId){
        $where['id']  = $userId;
        $field = 'integral';
        return    $this->where($where)->field($field)->find()['integral'];
    }
    //修改用户资料
    public function userSave($where,$data){
    	$res = $this->where($where)->save($data);
    	return $res;
    }
    
    /*
     * khantminthu
     * */
    ##UserDetail
    public function getUserDetail($id)
    {
        $where['u.id'] = $id;
        
        $join = "db_user_header as a ON a.user_id = u.id";
        
        $field = array('u.user_name','u.nick_name','a.user_header');
        
        return $getUserInfo = M('user as u')->join($join ,'LEFT')->where($where)->field($field)->find();
    }
}
