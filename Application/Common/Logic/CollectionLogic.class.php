<?php
namespace Common\Logic;
use Common\Model\CommonModel;
use Common\Model\UserModel;
use Common\Model\CollectionModel;
use Common\Model\GoodsModel;
use Common\Model\StoreFollowModel;
use Think\SessionGet;
/**
 * 逻辑处理层
 *
 */
class CollectionLogic extends AbstractGetDataLogic
{
    /**
     * 构造方法
     * @param array $data
     */
    public function __construct(array $data = [], $split = '')
    {
        $this->data = $data;
        $this->splitKey = $split;
        $this->modelObj = new CollectionModel();
        $this->goodsModelObj = new GoodsModel();
      
    }
    /**
     * 返回验证数据
     */
    public function getValidateByLogin()
    {
        $message = [
            'goods_id' => [
                'required' => '必须传入商品ID',
            ],
            'type'     => [
                'required' => '必须传入收藏类型',
            ],
        ];
        return $message;
    }

    /**
     * 返回验证数据
     */
    public function getValidateByCancel()
    {
        $message = [
            'id' => [
                'required' => '必须传入ID',
                'number'   => 'ID必须是数字',
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
        return CollectionModel::class;
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
            CollectionModel::$id_d,
        ];
    }



    /**
     * 商品收藏或取消收藏
     *
     */
    public function collectGoods(){
        $userId = SessionGet::getInstance('user_id')->get();
        // 验证是否收藏过这个商品
        $where['goods_id'] = $this->data['goods_id'];
        $where['user_id']  = $userId;

        if ($this->data['type'] == 1){
            $ifExcict = $this->modelObj->where($where)->field('id')->find();
            if (!empty($ifExcict)){
                return array("status"=>0,"message"=>"已经收藏，请勿重复收藏","data"=>"");
            }
        }
        if ($this->data['type'] == 2){
            $ifExcict = $this->modelObj->where($where)->field('id')->find();
            if (empty($ifExcict)){
                return array("status"=>0,"message"=>"还没有收藏本商品，不能进行取消收藏","data"=>"");
            }
        } 
        $result = $this->modelObj->addCollection($this->data,$userId);
        return $result;
    }

    /**
     * 得到我的收藏
     *
     */
    public function get_my_collection(){
        $page = empty($this->data['page'])?0:$this->data['page'];
        $end_time = time(); 
        $start_time = date("Y-m-d H:i:s",strtotime("-1 month"));
        $where['add_time'] = ['between',[$start_time,$end_time]];
        $where['user_id'] = SessionGet::getInstance('user_id')->get();
        $field = 'id,goods_id,user_id,status';
        $data = $this->modelObj->field($field)->where($where)->page($page.",10")->order("add_time DESC")->select();
        $count =  $this->modelObj->where($g_where)->count();
        $Page = new \Think\Page($count,10);
        $show = $Page->show();
        $totalPages = $Page->totalPages;
        if (empty($data)) {
            return array("status"=>1,"message"=>"暂无数据","data"=>"");
        }
        $goods = $this->goodsModelObj->getTitleByTwo($data);
        $retDta['goods']=$goods;
        $retDta['count']=$count;
        $retDta['totalPages']=$totalPages;
        $retDta['page_size']=10;
        return array("status"=>1,"message"=>"获取成功","data"=>$retDta);
    }
    /**
     * 取消我收藏的商品
     *
     */
    public function cancel_user_collection(){
        $user_id  = session("user_id");
        $good_Id  = $this->data['id'];
        $result = CommonModel::get_modle("Collection")->delete_collect_good($user_id,$good_Id);
        return $result;
    }
    /**
     * 我收藏的店铺
     *
     */
    public function my_collection_shops(){
        $store_follow = new StoreFollowModel();
        $user_id = SessionGet::getInstance('user_id')->get();
    
        $data = $store_follow->get_collection_shops($user_id);
        return $data;
    }
}
