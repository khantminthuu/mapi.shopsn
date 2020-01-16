<?php
namespace Common\Logic;
use Common\Model\OrderPackageCommentModel;
use Common\Model\UserModel;
use Common\Model\GoodsModel;
use Common\Model\SpecGoodsPriceModel;
use Think\SessionGet;
/**
 * 逻辑处理层
 *
 */
class OrderPackageCommentLogic extends AbstractGetDataLogic
{
    /**
     * 构造方法
     * @param array $data
     */
    public function __construct(array $data = [], $split = '')
    {
        $this->data = $data;
        $this->splitKey = $split;
        $this->modelObj = new OrderPackageCommentModel();
    }
   
    public function getValidatesByLogin(){
        $message = [
            'goods_id' => [
                'number' => '必须输入商品ID',
            ],
            'order_id' => [
                'required' => '必须输入订单ID',
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
    //添加评论
    public function getCommentsOrder(){
        $post = $this->data;
        M()->startTrans();
        if (!empty($post['img'])) {
            $post['have_pic'] = 1;
        }
        $post['store_id'] = M("OrderPackage")->where(['id'=>$post['order_id']])->getField("store_id");
        $post['user_id'] = SessionGet::getInstance('user_id')->get();
        $post['create_time'] = time();
        if ($post['score']<3) {
            $post['level']=0;
        }else if($post['score'] == 5){
            $post['level']=2;
        }else{
            $post['level']=1;
        }
        $rest = $this->modelObj->add($post);
        if(!$rest){
            M()->rollback();
            return array("status"=>0,"message"=>"失败","data"=>"");
        }
        $res = M("OrderPackage")->where(['id'=>$post['order_id']])->save(['comment_status'=>1]);
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
    
}
