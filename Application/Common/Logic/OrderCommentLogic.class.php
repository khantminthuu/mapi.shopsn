<?php
namespace Common\Logic;
use Common\Model\OrderCommentModel;
use Common\Model\UserModel;
use Common\Model\OrderGoodsModel;
use Common\Model\GoodsModel;
use Common\Model\SpecGoodsPriceModel;
use Think\Cache;
use Home\Controller\OrderCommentController;
use Think\SessionGet;
/**
 * 逻辑处理层
 *
 */
class OrderCommentLogic extends AbstractGetDataLogic
{
    /**
     * 构造方法
     * @param array $data
     */
    public function __construct(array $data = [], $split = '')
    {
        $this->data = $data;
        $this->splitKey = $split;
        $this->modelObj = new OrderCommentModel();
        $this->userModel = new UserModel();
        $this->goodsModel = new GoodsModel();
        $this->goodsSpecItemModel = new SpecGoodsPriceModel();

    }
    /**
     * 返回验证数据
     */
    public function getValidateByLogin()
    {
        $message = [
            'goods_id' => [
                'required' => '必须输入商品ID',
            ],
        ];
        return $message;
    }

    public function getValidatesByLogin(){
        $message = [
            'goods_id' => [
                'required' => '必须输入商品ID',
            ],
            'status' => [
                'required' => '必须输入查询的评论分类',
            ],

        ];
        return $message;
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
        return OrderGoodsModel::class;
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
     * 验证参数和得到商品所有的评论
     *
     */
    public function getGoodsAllComments(){

        $retData = $this->getCommentCounts($this->data[OrderCommentModel::$goodsId_d]);

        return $retData;
    }

    /**
     * 验证参数和得到商品所有的评论的数量
     *
     */
    public function getCommentCounts($goodId){
        $whereAllCount['goods_id'] = $goodId;
        $data = [];
        //全部
        $data['allcount'] = $this->modelObj->where(['goods_id'=>$goodId])->count();
        //好
        $data['nice'] = $this->getLevelCounts($goodId,2);
        // 中
        $data['height'] = $this->getLevelCounts($goodId,1);
        //低
        $data['bad'] = $this->getLevelCounts($goodId,0);
        //有图
        $data['image'] = $this->modelObj->where(['goods_id'=>$goodId,"have_pic"=>1])->count();
        return  $data;
    }
    /**
     * 得到商品所有的评论的数量
     *
     */
    public function getLevelCounts($goodId, int $level){
        $where['goods_id'] = $goodId;
       
        $where['level'] = $level;
        
        return $this->modelObj->where($where)->count();
    }
    
    /**
     * 验证参数和得到商品所有的评论内容
     *
     */
    public function getCommentsList(){
        $post = $this->data;
        $this->searchField = 'id,user_id,goods_id,order_id,content ,create_time,anonymous,level,score';
        if ($post['status'] == 4){
            $this->searchTemporary = [
                OrderCommentModel::$goodsId_d => $this->data[OrderCommentModel::$goodsId_d],
                OrderCommentModel::$status_d => '1',
            ];
        }else if ($post['status'] == 5){
            $this->searchTemporary = [
                OrderCommentModel::$goodsId_d => $this->data[OrderCommentModel::$goodsId_d],
                OrderCommentModel::$status_d => '1',
                OrderCommentModel::$level_d =>$post[OrderCommentModel::$level_d]
            ];
        }else{
            $this->searchTemporary = [
                OrderCommentModel::$goodsId_d => $this->data[OrderCommentModel::$goodsId_d],
                OrderCommentModel::$status_d => '1',
                OrderCommentModel::$havePic_d =>'1'
            ];
        }
        $this->searchOrder = 'create_time DESC';
        $retData = parent::getDataList();
        foreach ($retData['records'] as $key => $value){
            //获取用户的姓名
            $retData['records'][$key]['user_name'] = $this->userModel->getUserName($value['user_id'],$value['anonymous']);
            //得到商品的规格
            $retData['records'][$key]['goods_apace'] = $this->goodsSpecItemModel->getGoodSpe($value['goods_id']);
            $retData['records'][$key]['img'] = M("OrderCommentImg")->field("path")->where(['comment_id'=>$value['id']])->select();

        }
        return $retData;
    }
    //添加评论
    public function getCommentsOrder(){
        $post = $this->data;
        M()->startTrans();
        if (!empty($post['img'])) {
            $post['have_pic'] = 1;
        }
        $post['store_id'] = M("Order")->where(['id'=>$post['order_id']])->getField("store_id");
        $rest = $this->modelObj->commontAdd($post);
        if(!$rest){
            M()->rollback();
            return array("status"=>0,"message"=>"失败","data"=>"");
        }
        $res = M("Order")->where(['id'=>$post['order_id']])->save(['comment_status'=>1]);
        if($res===false){
            M()->rollback();
            return array("status"=>0,"message"=>"失败","data"=>"");
        }
        $res = M("OrderGoods")->where(['order_id'=>$post['order_id'],"goods_id"=>$post['goods_id']])->save(['comment'=>1]);
        if($res===false){
            M()->rollback();
            return array("status"=>0,"message"=>"失败","data"=>"");
        }
        foreach ($post['img'] as $k => $v) {
            $date[$k]['path'] = $v;
            $date[$k]['comment_id'] = $rest;
        }
        $rest = M('order_comment_img')->addALL($date);
        if (!$rest) {
            M()->rollback();
            return array("status"=>0,"message"=>"失败","data"=>"");
        }
        M()->commit();
        return array("status"=>1,"message"=>"成功","data"=>"");
    }
    //获取评论
    public function getMyCommont(){
        $post = $this->data;
        $user_id = SessionGet::getInstance('user_id')->get();
        $rest = $this->modelObj->getCommont($user_id);
        $goods = $this->goodsModel->getTitleByTwo($rest['data']);
        foreach ($goods as $key => $value) {
            $where['comment_id'] = $value['id'];
            $img = M('order_comment_img')->field("path")->where($where)->select();
            $goods[$key]['commont_img'] = $img;
        }
        $date = $goods;
        foreach ($date as $key => $value) {
            if (empty($value['commont_img'])) {
                unset($date[$key]);
            } 
        } 
        $date = array_values($date);
        $img_count = count($date);
        if ($post['type'] == 2) {
            return array("status"=>1,"message"=>"成功","data"=>array("data"=>$date,"countAll"=>$rest['countAll'],"img_count"=>$img_count));
        }else{
            return array("status"=>1,"message"=>"成功","data"=>array("data"=>$goods,"countAll"=>$rest['countAll'],"img_count"=>$img_count));
            
        }
    }
    
    /**
     * 根据评分推荐商品
     */
    public function getRecommend() :array
    {
    	$cache = Cache::getInstance('', ['expire' => 800]);
    	
    	$key = 'recommend-goods-list';
    	
    	$data = $cache->get($key);
    	
    	if (!empty($data)) {
    		return $data;
    	}
    		
    	$data = $this->modelObj->field([
	    		OrderCommentModel::$goodsId_d,
    			OrderCommentModel::$score_d,
    			OrderCommentModel::$userId_d
    		])
    	->order(OrderCommentModel::$createTime_d.' DESC ')
    	->limit(500)->select();
    	
    	if (empty($data)) {
    		return [];
    	}
    	
    	$cache->set($key, $data);
    	
    	return $data;
    }
    
    /**
     * 获取各个商品的评分
     */
    public function getGoodsRecommend() :array
    {
    	$score = [];
    	
    	$data = $this->getRecommend();
    	
    	if (count($data) === 0) {
    		return [];
    	}
    	
    	$meData = [];
    	
    	$otherUserData = [];
    	
    	$userId = SessionGet::getInstance('user_id')->get();
    	
    	//区分个人与其他用户
    	foreach ($data as $key => $value) {
    		
    		if ($value['user_id'] == $userId) {
    			
    			$meData[] = $value;
    			
    		} else {
    			$otherUserData[] = $value;
    		}
    	}
    	
    	//没有购买商品
    	if (count($meData) === 0 || count($otherUserData) === 0) {
    		return [];
    	}
    	
    	unset($data);
    	
    	$meDataParse = $this->parseArrayNumber($meData);
    	
    	$otherUserDataParse = $this->parseArrayNumber($otherUserData);
    	
    	$tmpDataParse = $meDataParse;
    
    	$tmpOther = $otherUserDataParse;
    	
    	$mLength = count(array_shift($tmpDataParse));
    	
    	$tmpOtherLength = count(array_shift($tmpOther));
    	
    	$i = 0;
    	
    	$tmp = [];
    	
    	// 两个数组 中的值【一维数组 数量必须一致】
    	if ($mLength < $tmpOtherLength) {
    	    
    	    for ($i = $mLength; $i < $tmpOtherLength; $i++) {
    	        $tmp[] = 0;
    	    }
    	    
    	    foreach ($meDataParse as $key => &$value) {
    	        
    	        $value = array_merge($value, $tmp);
    	    }
    	    
    	} else if ($mLength > $tmpOtherLength){
    	    
    	    for ($i = $tmpOtherLength; $i < $mLength; $i++) {
    	        $tmp[] = 0;
    	    }
    	    
    	    foreach ($otherUserDataParse as $key => &$value) {
    	        
    	        $value = array_merge($value, $tmp);
    	    }
    	}
    	
    	return [
    		'me' => $meDataParse,
    		'otherPerson' => $otherUserDataParse
    	];
    }
    
    /**
     * 处理数组个数
     */
    private function parseArrayNumber( array $meData) :array
    {
    	$meScore = [];
    	
    	foreach ($meData as $key => $value)
    	{
    		$meScore[$value[OrderCommentModel::$goodsId_d]][] = $value[OrderCommentModel::$score_d];
    	}
    	
    	
    	$meNumber = [];
    	
    	foreach ($meScore as $key => $value) {
    		$meNumber[$key] = count($value);
    	}
    	
    	//数组里个数的最大值
    	$max = max($meNumber);
    	
    	//填充数组达到个数一致
    	foreach ($meScore as $key => & $value) {
    		
    		if ($meNumber[$key] === $max) {
    			continue;
    		}
    		
    		for ($i = $meNumber[$key]; $i < $max; $i++) {
    			$value[$i] = 0;
    		}
    	}
    	
    	return $meScore;
    }
}
