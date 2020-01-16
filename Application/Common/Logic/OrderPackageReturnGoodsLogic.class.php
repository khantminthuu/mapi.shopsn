<?php
namespace Common\Logic;
use Common\Model\OrderPackageReturnGoodsModel;
use Common\Model\GoodsModel;
use Common\Model\OrderPackageModel;
use Common\Model\OrderPackageGoodsModel;
use Think\SessionGet;
/**
 * 逻辑处理层
 *
 */
class OrderPackageReturnGoodsLogic extends AbstractGetDataLogic
{
    /**
     * 构造方法
     * @param array $data
     */
    public function __construct(array $data = [], $split = '')
    {
        $this->data = $data;
        $this->splitKey = $split;
        $this->modelObj = new OrderPackageReturnGoodsModel();
    }
    /**
     * 返回验证数据
     */
    public function getValidateByApply()
    {
        $message = [
            'order_id' => [
                'required' => '订单ID必填',
            ],
            'goods_id' => [
                'required' => '商品ID必填',
            ],
            'explain' => [
                'required' => '申请理由说明必填',
            ],
            'apply_img' => [
                'required' => '申请图片必填',
            ],
            'store_id' => [
                'required' => '店铺ID必填',
            ],
        ];
        return $message;
    }
    /**
     * 返回验证数据
     */
    public function getValidateByNumber()
    {
        $message = [
            'id' => [
                'required' => '售后申请ID必填',
            ],
            'express_id' => [
                'required' => '快递方式必填',
            ],
            'waybill_id' => [
                'required' => '运单号必填',
            ],  
        ];
        return $message;
    }
    /**
     * 返回验证数据
     */
    public function getValidateByQuery()
    {
        $message = [
            'id' => [
                'required' => '售后申请ID必填',
            ],
        ];
        return $message;
    }
    /**
     * 获取结果
     */
    public function getResult()
    {
    }
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
     */
    public function getModelClassName() :string
    {
        return OrderReturnGoodsModel::class;
    }
    //申请售后
    public function applyForAfterSale(){
    	$post = $this->data;
    	$post['user_id'] = SessionGet::getInstance('user_id')->get();
    	$post['create_time'] = time();
    	$post['apply_img'] = implode(",",$post['apply_img']);
    	$return =  $this->modelObj->field("id")->where(['order_id'=>$post['order_id'],"goods_id"=>$post['goods_id']])->find();
    	if (!empty($return)) {
    		return array("status"=>0,"message"=>"该商品已经申请过了!","data"=>"");
    	}
    	M()->startTrans();
    	$res = $this->modelObj->add($post);
        if (!$res) {
        	M()->rollback();
            return array("status"=>0,"message"=>"申请失败","data"=>"");
        }else{
        	$date['return_id'] = $res;
        	$date['status'] = 0;
        	$date['create_time'] = time();
        	$date['approval_content'] = "亲!您的申请正在等待管理员审核!";
        	$rest = M('order_package_review_progress')->add($date);
        	if (!$rest) {
        		M()->rollback();
            	return array("status"=>0,"message"=>"申请失败","data"=>"");
        	}
            $rest = M('OrderPackage')->where(['id'=>$post['order_id']])->save(['order_status'=>5]);
            if ($rest===false) {
                M()->rollback();
                return array("status"=>0,"message"=>"申请失败","data"=>"");
            }
        	$rest = M('OrderPackageGoods')->where(['order_id'=>$post['order_id'],"goods_id"=>$post['goods_id']])->save(['status'=>5]);
        	if (!$rest) {
        		M()->rollback();
            	return array("status"=>0,"message"=>"申请失败","data"=>"");
        	}
        	M()->commit();
            return array("status"=>1,"message"=>"申请成功","data"=>array("id"=>$res));
        }
    }
    //填写快递单号
    public function setCourierNumber(){
    	$post = $this->data;
    	$where['id'] = $post['id'];
    	$post['update_time'] = time();
    	$return =  M('OrderPackageReturnGoods')->where($where)->save($post);
    	if (!$return) {
    		return array("status"=>0,"message"=>"提交失败!","data"=>"");
    	}
    	return array("status"=>1,"message"=>"提交成功!","data"=>"");
    }
    //申请售后进度查询
    public function getProgressQuery(){
    	$where['user_id'] = SessionGet::getInstance('user_id')->get();    	
    	$field = "id,goods_id,status,create_time,number";
    	$return =  $this->modelObj->field($field)->where($where)->order("create_time DESC")->select();
    	if (empty($return)) {
    		return array("status"=>0,"message"=>"暂无数据!","data"=>"");
    	}
    	$goods = $this->goodsModel->getTitleByTwo($return);
    	return array("status"=>1,"message"=>"获取成功!","data"=>$goods);
    }
    //申请售后详情
    public function returnInfo(){
    	$post  = $this->data;
    	$where['id'] = $post['id'];    	
    	$field = "id,goods_id,status,order_id,price,explain,number";
    	$return =  $this->modelObj->field($field)->where($where)->find();
    	if (empty($return)) {
    		return array("status"=>0,"message"=>"暂无数据!","data"=>"");
    	}
    	$goods = $this->goodsModel->getTitleByOne($return);
    	$goods['message'] = M('order_package_review_progress')->where(["return_id"=>$goods['id']])->order("create_time DESC")->getField("approval_content");
    	$speed = M('order_package_review_progress')->field("id,create_time,approval_content,approval_id")->where(["return_id"=>$goods['id']])->order("create_time DESC")->select();
    	foreach ($speed as $key => $value) {
    		$speed[$key]['approval_name'] = M('store_seller')->where(['id'=>$value['approval_id']])->getField("seller_name");
    	}
    	$goods['speed'] = $speed;
    	return array("status"=>1,"message"=>"获取成功!","data"=>$goods);
    }
    /**
     * 返回验证数据--搜索
     */
    public function getValidateBySearch()
    {
        $message = [
            'keyWord' => [
                'required' => '搜索关键词必填',
            ],
        ];
        return $message;
    }
    //搜索
    public function getSearchOrder(){
        $this->order = new OrderPackageModel();
        $this->orderGoods = new OrderPackageGoodsModel();
        $keyWord = $this->data['keyWord'];
        $user_id = SessionGet::getInstance('user_id')->get();
        if (is_numeric($keyWord)) {
            $order = $this->order->field('id')->where(['order_sn_id'=>$keyWord,'user_id'=>$user_id])->select();
        }else{
            $where['title'] = array("like","%".$keyWord."%");
            $goods = $this->goodsModel->field("id")->where($where)->select();
            if (empty($goods)) {
                return array("status"=>0,"message"=>"暂无查询结果!","data"=>"");
            }
            $goods_id = array_column($goods, 'id');
            $o_where['goods_id'] = array("IN",$goods_id); 
            $orderGoods = $this->orderGoods->field("order_id")->where($o_where)->group("order_id")->select();
            if (empty($orderGoods)) {
                return array("status"=>0,"message"=>"暂无查询结果!","data"=>"");
            }
            $order_id = array_column($orderGoods, 'order_id');
            $g_where['id']      = array("IN",$order_id);
            $g_where['user_id'] = $user_id;
            $order = $this->order->field('id')->where($g_where)->select();
        } ;
        if (empty($order)) {
            return array("status"=>0,"message"=>"暂无查询结果!","data"=>"");
        }
        $orderID = array_column($order, 'id');
        $r_where['order_id'] = array("IN",$orderID);
        $r_where['user_id']  = $user_id;
        $field = "id,goods_id,status,create_time,number";
        $orderReturn = $this->modelObj->field($field)->where($r_where)->order("create_time DESC")->select();
        if (empty($orderReturn)) {
            return array("status"=>0,"message"=>"暂无数据!","data"=>"");
        }
        $return = $this->goodsModel->getTitleByTwo($orderReturn);
        return array("status"=>1,"message"=>"获取成功!","data"=>$return);
    }
}
