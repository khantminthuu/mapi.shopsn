<?php
namespace Common\Logic;
use Common\Model\GoodsImagesModel;
use Common\Model\GoodsModel;
use Common\Model\StoreEvaluateModel;
use Common\Model\StoreFollowModel;
use Common\Model\StoreModel;
use Common\Model\UserModel;
use Common\Model\PanicModel;
use Common\Model\CommonModel;
use Think\Page;
use Think\Cache;

/**
 * 逻辑处理层
 *
 */
class PanicLogic extends AbstractGetDataLogic
{
    /**
     * 构造方法
     * @param array $data
     */
    public function __construct(array $data = [], $split = '')
    {
        $this->data = $data;
        $this->splitKey = $split;
        $this->modelObj = new PanicModel();
      
    }
    /**
     * 返回验证数据
     */
    public function getValidateByLogin()
    {
        $message = [
            'page' => [
                'required' => '必须输入分页信息',
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
        return PanicModel::class;
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
     * 得到抢购商品信息
     *
     */
    public function get_panic_goods(){
        $post = $this->data;
        $page = empty($post['page']) ? 1 : empty($post['page']);
        $imgModel = new GoodsImagesModel();
        $goodsModel = new GoodsModel();
        $where['end_time'] = array('EGT',time());
        $where['start_time'] = array('ELT',time());
        $where['status'] = 1;
        $where['panic_num'] = array('GT',0);
        $field = 'id,panic_title,panic_price,goods_id,panic_num,quantity_limit,already_num,store_id,start_time,end_time';
        $count = $this->modelObj->field($field)->where($where)->count();
        $Page = new Page($count,10);
        $Page->show();
        $totalPages = $Page->totalPages;
        $data = $this->modelObj->field($field)->where($where)->page($page,10)->select();
        if(!$data){
            return array("status"=>0,"message"=>"暂无数据","data"=>'');
        }
        foreach($data as $k => $v){
            $goods = $goodsModel->field('title,price_market,p_id')->where(['id'=>$v['goods_id']])->find();
            $img = $imgModel->field('pic_url')->where(['goods_id'=>$goods['p_id'],'is_thumb'=>1,'status'=>1])->find();
            $data[$k]['pic_url'] = $img['pic_url'];
            $data[$k]['title'] = $goods['title'];
            $data[$k]['price_market'] = $goods['price_market'];
        }
        $res['data'] = $data;
        $res['totalPages'] = $totalPages;
        $res['count'] = $count;
        return $res;
    }
    
    /**
     *
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getTableColum()
     */
    protected function getTableColum() :array
    {
    	return [
    		PanicModel::$panicNum_d,
    		PanicModel::$goodsId_d,
    		PanicModel::$startTime_d,
    		PanicModel::$endTime_d,
    		PanicModel::$storeId_d
    	];
    }

    /**
     * 抢购商品详情
     */
    public function getPanicDetail(){
        $post = $this->data;
        $data = $this->modelObj->alias('a')
            ->field('a.id,a.panic_price,a.end_time,a.quantity_limit,b.title,b.p_id,b.store_id,d.shop_name,d.store_logo,d.description')
            ->join('left join db_goods as b on a.goods_id = b.id')
            ->join('left join db_store as d on d.id = b.store_id')
            ->where(['a.goods_id'=>$post['goods_id']])
            ->find();
        if(!$data){
            $this->errorMessage = '数据异常';
            return false;
        }
        $data['goods_count'] = M('goods')->where(['store_id'=>$data['store_id'],'p_id'=>0])->count();
        $data['follow'] = M('storeFollow')->where(['id'=>$data['store_id']])->count();
        $data['img'] = M('goodsImages')->where(['goods_id'=>$data['p_id'],'is_thumb'=>0])->getField('pic_url',true);
        return $data;
    }

    public function getValidateByDetail() {
        $message = [
            'id' => [
                'number' => '商品Id参数必须'
            ],
            'goods_num' => [
                'number' => '商品数量参数必须'
            ],
        ];
        return $message;
    }

    //获取抢购商品详情
    public function getGoodsDetailByOrder() :array
    {
        $data = $this->getGoodsDetailCache();

        if (empty($data)) {
            return [];
        }

        unset($data[GoodsModel::$priceMarket_d]);

        return $data;
    }

    /**
     * 获取抢购商品详情
     * @return array
     */
    public function getGoodsDetailCache() :array
    {
        $key = 'goods_detail'.$this->data['id'];

        $cache = Cache::getInstance('', ['expire' => 60]);

        $data = $cache->get($key);

        if (!empty($data)) {
            return $data;
        }

        $data = $this->getGoodsDetailById();
        if (empty($data)) {
            $this->errorMessage = '没有找到这个商品任何信息';
            return [];
        }

        $cache->set($key, $data);

        return $data;
    }

    public function getGoodsDetailById(){
        $res = $this->modelObj->alias('a')
            ->field('a.id as panic_id,a.panic_price as price_member,b.id,b.title,b.p_id,b.store_id,b.stock,b.express_id,b.weight,b.description,b.price_market,b.comment_member,b.sales_sum,b.brand_id,b.class_two,b.shelves,b.pic_url')
            ->join('db_goods as b on b.id = a.goods_id')
            ->where(['a.id'=>$this->data['id']])
            ->find();
        return $res;
    }

    public function getSplitKeyByPId():string
    {
        return 'id';
    }

    public function getSplitKeyByStore()
    {
        return 'store_id';
    }

    /**
     * 修改抢购商品已购数量
     */
    public function changePanicNum(){
        $res = $this->modelObj->where(['id'=>$this->data['panic_id']])->setInc('already_num',$this->data['goods_num']);
        if (!$this->traceStation($res)) {
            $this->errorMessage .= '数据异常，生成订单失败';
            return false;
        }
        return true;
    }
}
