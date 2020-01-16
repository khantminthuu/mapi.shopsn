<?php
namespace Common\Logic;
use Common\Model\CommonModel;
use Common\Model\UserModel;
use Common\Model\BrandModel;
/**
 * 逻辑处理层
 *
 */
class BrandLogic extends AbstractGetDataLogic
{
    /**
     * 构造方法
     * @param array $data
     */
    public function __construct(array $data = [], $split = '')
    {
        $this->data = $data;
        $this->splitKey = $split;
        $this->modelObj = new BrandModel();
      
    }
    /**
     * 返回验证数据
     */
    public function getValidateByLogin()
    {
        $message = [
            'id' => [
                'required' => '必须输入品牌ID',
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
        return BrandModel::class;
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
     * 品牌列表逻辑
     *
     */
    public function getBrandList(){
        $brand_name = $this->data['brand_name'];
        $data = CommonModel::brand()->get_brand_list($brand_name);
        return $data;
    }
    /**
     * 获取品牌详情
     *
     */
    public function get_brand_info(){
        $brand_id = I('id');
        $page=I('page');
        if (I('sort')) $flag =I('sort');
        $brand = M('brand')->field('brand_name,brand_description,brand_logo,brand_banner')->where(['id'=>$brand_id])->find();
        if (!empty($flag)) {
            switch ($flag) {
                case 1:  //销量由高到低
                    $order = 'sales_sum DESC';
                    break;
                case 2:  //销量由低到高
                    $order = 'sales_sum ASC';
                    break;
                case 3:   //价格由高到低
                    $order = 'price_market DESC';
                    break;
                case 4:  //价格由低到高
                    $order = 'price_market ASC';
                    break;
                case 5:
                    $order = 'sales_sum DESC';
                    break;
            }
        } else {
//            $this->searchOrder = '';
            $order = '';
        }
        $page = empty($this->data['page'])?0:$this->data['page']; 
        $goods = M('goods')->where(['brand_id'=>$brand_id,'p_id'=>['NEQ',0]])->field('id,title,price_member as price_market,p_id')->order($order)->page($page.",10")->select();
        $count =  M('goods')->where(['brand_id'=>$brand_id,'p_id'=>['NEQ',0]])->count();
        $Page = new \Think\Page($count,10);
        $show = $Page->show();
        $totalPages = $Page->totalPages;
        $goods_images_model=M('goods_images');
        $order_comment_model=M('order_comment');
        $order_goods_model=M('order_goods');
        foreach ($goods as $k => $v) {
            $goods[$k]['pic_url'] =$goods_images_model->where(['goods_id'=>$v['p_id'],"is_thumb"=>1])->getField('pic_url');
            $goods[$k]['comment'] = $order_comment_model->where(['goods_id'=> $v['id']])->count();
            $goods[$k]['trade'] = $order_goods_model->where(['goods_id' => $v['id'], 'status' => 1])->count();
        }
        $data = array(
            'brand' => $brand,
            'goods' => $goods,
            'count' =>$count,
            'page_size'=>10,
            'totalPages'=>$totalPages
        );
        return $data;
    }



   
}
