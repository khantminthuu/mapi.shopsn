<?php

namespace Common\Logic;

use Common\Model\FollowImageModel;
use Common\Model\FootPrintModel;
use Common\Model\GoodsModel;
use Common\Model\UserModel;
use Think\SessionGet;

/**
 * 逻辑处理层
 */
class PcenterLogic extends AbstractGetDataLogic {
	/**
	 * 构造方法
	 * 
	 * @param array $data        	
	 */
	public function __construct(array $data = [], $split = '') {
		$this->data = $data;
		$this->splitKey = $split;
		$this->modelObj = new UserModel ();
		$this->modelFoot = new FootPrintModel ();
		$this->goodsModelObj = new GoodsModel ();
		$this->userModelObj = new UserModel();
		$this->followImageModelObj = new FollowImageModel();
	}
	/**
	 * 返回验证数据
	 */
	public function getValidateByLogin() {
		$message = [ 
						'page' => [ 
										'required' => '必须输入分页信息' 
						] 
		];
		return $message;
	}
	
	/**
	 * 获取结果
	 */
	public function getResult() {
	}
	
	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
	 */
	public function getModelClassName() :string {
		return UserModel::class;
	}
	
	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \Common\Logic\AbstractGetDataLogic::hideenComment()
	 */
	public function hideenComment() :array {
		return [ 
		];
	}
	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \Common\Logic\AbstractGetDataLogic::likeSerachArray()
	 */
	public function likeSerachArray() :array {
		return [ 
						UserModel::$userName_d 
		];
	}
	/**
	 *
	 * @return mixed 充值
	 */
	public function logrecharge() {
		$result = $this->modelObj->recharge ();
		
		if ($result) {
			return $result;
		} else {
			return false;
		}
	}
	
	/**
	 * 可提现金额
	 */
	public function logdistill() {
		return $this->modelObj->withdrawals_limit ();
	}
	
	/**
	 * 确认提现
	 */
	public function logMakeDistill() {
		$data = $this->data;
		
		$re = $this->modelObj->withdrawals ( $data );
		
		if ($re == 0) {
			$parms = array (
							'status' => '0',
							'message' => '转出金额超限',
							'data' => '' 
			);
			return $parms;
		} elseif ($re == 1) {
			$parms = array (
							'status' => '1',
							'message' => '申请成功',
							'data' => '' 
			);
			return $parms;
		} elseif ($re == - 1) {
			return array (
							'status' => '0',
							'message' => '参数错误',
							'data' => '' 
			);
		} else {
			$parms = array (
							'status' => '0',
							'message' => '等待审核中',
							'data' => '' 
			);
			return $parms;
		}
	}
	// 我的足迹
	public function getFoot() {
	    
	    $page = empty($this->data['page'])?0:$this->data['page']; 
		$where['uid'] = SessionGet::getInstance('user_id')->get();//用户id
        $field = 'gid as goods_id,create_time';
        $data = $this->modelFoot->where($where)->field($field)->page($page.",10")->order("create_time DESC")->select();
		if (empty ( $data )) {
			return array (
							"status" => 1,
							"message" => "暂无数据",
							"data" => "" 
			);
		}
		$count =  $this->modelObj->where($where)->count();
        $Page = new \Think\Page($count,10);
        $show = $Page->show();
        $totalPages = $Page->totalPages;
		foreach ( $data as $key => $value ) {
			$data [$key] ['create_time'] = date ( "Y年m月d日", $value ['create_time'] );
		}
		$goods = $this->goodsModelObj->getTitleByTwo ( $data );
		foreach ( $goods as $key => $info ) {
			
			$result [$info ['create_time']] ['goods'] [] = $info;
			
			$result [$info ['create_time']] ['create_time'] = $info ['create_time'];
		}
		$date = array_values ( $result );
		$retData['goods'] = $date;
		$retData['count'] = $count;
		$retData['totalPages'] = $totalPages;
		$retData['page_size'] = 10;
		return array (
						"status" => 1,
						"message" => "获取成功",
						"data" => $retData 
		);
	}
	// 我的足迹
	public function delFoot() {
		$user_id = SessionGet::getInstance('user_id')->get();
		$data = $this->modelFoot->delFootPrint ( $user_id );
		
		return $data;
	}
	
	//根据用户信息查询头像
	public function getUserHeaderByUser(){
		if(empty($data) ) {
			return [];
		}
		$id = SessionGet::getInstance('user_id')->get();
		
		$field = 'user_header';
		
		$img = M('user_header')->field($field)->where('id =:id')->bind([':id' => $id])->find();
		
		if (empty($img)) {
			return [];
		}
		
		return $img;
	}
	
	// 我的钱包
	public function myWallet() {
		$page = empty($this->data['page'])?0:$this->data['page']; 
		$id = SessionGet::getInstance('user_id')->get();
		// 头像
		$header = M('user_header')->field($field)->where('user_id =:id')->bind([':id' => $id])->find();
		
		$headerImg = isset($header['user_header']) ? $header['user_header'] : '';
		
		// 账户余额
		// $balance=sprintf("%.2f",M('user')->where(['id'=>$id])->getField('balance'));
		
		$id = SessionGet::getInstance('user_id')->get();
		
		$balance = M ( 'balance' )->field ( "account_balance" )->where ( [ 
						'user_id' => $id 
		] )->order ( "id DESC" )->find ();
		if (empty ( $balance ['account_balance'] )) {
			$balanceMoney = 0;
		} else {
			$balanceMoney = $balance ['account_balance'];
		}
		// 账户余额明细
		
		$balanceDetail = M ( 'balance' )->where ( array (
						'user_id' => $id 
		) )->field ( 'id as balance_id,account_balance,type,description,recharge_time,changes_balance' )->page($page.",10")->order ( "id DESC" )->select ();
		$count =  M('balance')->where(array('user_id'=>$id))->count();
		$Page = new \Think\Page($count,10);
		$show = $Page->show();
		$totalPages = $Page->totalPages;
		if (!empty($balanceDetail)) {
			foreach ( $balanceDetail as $k => & $v ) {
				if ($v ['type'] == 0) {
					$balanceDetail[$k] ['balance'] = '-' . $v ['changes_balance'];
				}else if($v ['type'] == 1) {
					$balanceDetail[$k] ['balance'] = '+' . $v ['changes_balance'];
				}else if($v ['type'] == 2) {
					$balanceDetail[$k] ['balance'] = '-' . $v ['changes_balance'];
				}else{
                    $balanceDetail[$k] ['balance'] = '+' . $v ['changes_balance'];
				}
				$v['time'] = date ( 'Y-m-d H:i:s', $v ['recharge_time'] );
				$v['date'] = date ('m',$v['recharge_time'] );
				unset ( $balanceDetail [$k] ['account_balance'] );
				unset ( $balanceDetail [$k] ['recharge_time'] );
				unset ( $balanceDetail [$k] ['changes_balance'] );
			}
			$result = [];
	     	foreach ($balanceDetail as $k1 => $v1) {
	            $result[$v1['date']]['list'][] = $v1;
	            $result[$v1['date']]['date'] = $v1['date'];

	        }
	        $cart = array_values($result);
		}
		$data = array (
			'balance'        => $balanceMoney,
			'header_img'     => $headerImg,
			'balance_detail' => $cart,
			'count'          => $count,
			'page_size'      => 10,
			'totalPages'     => $totalPages
		);
		return $data;
	}
	/*
	 * khantminthu userdetail
	 */
	public function getUserDetail()
    {
        $userId = $_SESSION['user_id'];
        
        $getUserInfo = $this->userModelObj->getUserDetail($userId);
        
        $getFollow = $this->followImageModelObj->getFollow($userId);
        
        $getFavourite = M('collection')->where(['user_id'=>$userId])->count();
        
        $getFollow['foot'] = $this->getFoot()['data']['count'];

        $getFollow['favourite'] = $getFavourite;
        
        return $arr = array(
            'userInfo' => $getUserInfo,
            'getFollow' => $getFollow
        );
    }
    
    public function test2()
    {
        $user_name = "khantminthu22@gmail.com";
        $strlen     = mb_strlen($user_name, 'utf-8');
        $firstStr     = mb_substr($user_name, 0, 1, 'utf-8');
        $lastStr     = mb_substr($user_name, -1, 1, 'utf-8');
        return $strlen == 2 ? $firstStr . str_repeat('*', mb_strlen($user_name, 'utf-8') - 1)
            : $firstStr . str_repeat("*", $strlen - 2) . $lastStr;
    }
    public function test()
    {
        $user_name = "khantminthu";
        $strlen = mb_strlen($user_name,'utf-8');
        $firstStr = mb_substr($user_name,0,1,'utf-8');
        $lastStr = mb_substr($user_name,-1,1,'utf-8');
        echo "https://www.youtube.com/watch?v=EDW0CMRPP2U";
        var_dump($lastStr);
        die;
    }
   
}

































































































