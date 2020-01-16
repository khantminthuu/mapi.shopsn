<?php
namespace Common\Logic;
use Common\Model\GoodsComboModel;
use Common\Model\UserModel;
use Common\Model\GoodsModel;
use Common\Model\GoodsImagesModel;
use Common\Model\SpecGoodsPriceModel;
use Common\TraitClass\SmsVerification;
use Think\SessionGet;
/**
 * 逻辑处理层
 *
 */
class GoodsComboLogic extends AbstractGetDataLogic
{   use SmsVerification;
    /**
     * 构造方法
     * @param array $data
     */
    public function __construct(array $data = [], $split = '')
    {
        $this->data = $data;
        $this->splitKey = $split;
        $this->modelObj = new GoodsComboModel();
        $this->goodModel = new GoodsModel();
        $this->goodsImagesModel = new GoodsImagesModel();
        $this->spec = new SpecGoodsPriceModel();
    }
    /**
     * 返回验证数据
     */
    public function getValidateByLogin()
    {
        $message = [
            'goods_id' => [
                'required' => '必须输入搭配商品ID',
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
        return GoodsComboModel::class;
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
     * 获取最佳组合
     *
     */
    public function getComboGoods(){
        $post = $this->data;
        $sub_ids = $this->modelObj->where(['goods_id'=>$post['goods_id']])->getField("sub_ids");
        if (empty($sub_ids)) {
            return array("status"=>0,"message"=>"暂无数据","data"=>"");
        }
        $where['id'] = array("IN",$sub_ids);
        if (empty(SessionGet::getInstance('user_id')->get())) {
            $field = "id,price_market as price_market,title,p_id";
        }else{
            $field = "id,price_member as price_market,title,p_id";
        }
        $goods = $this->goodModel->field($field)->where($where)->select();
        if (empty($goods)) {
            return array("status"=>0,"message"=>"暂无数据","data"=>"");
        }
        $data = $this->goodsImagesModel->getgoodImageByGoods($goods);
        foreach ($data as $key => $value) {
            $data[$key]['space'] = $this->spec->getGoodSpe($value['goods_id']);
        }
        return array("status"=>1,"message"=>"获取成功","data"=>$data);
    }
   
    /**
     * 得到搭配配件
     * @author 王波
     */
    public function getAccessories(){
        $post = $this->data;
        $sub_ids = M("GoodsAccessories")->where(['goods_id'=>$post['goods_id']])->getField("sub_ids");
        if (empty($sub_ids)) {
            return array("status"=>0,"message"=>"暂无数据","data"=>"");
        }

        $where['id'] = array("IN",$sub_ids);
        if (empty(SessionGet::getInstance('user_id')->get())) {
            $field = "id as goods_id,price_market as goods_price,title,p_id";
        }else{
            $field = "id as goods_id,price_member as goods_price,title,p_id";
        }
        $goods = $this->goodModel->field($field)->where($where)->select();
        if (empty($goods)) {
            return array("status"=>0,"message"=>"暂无数据","data"=>"");
        }
        $data = $this->goodsImagesModel->getgoodImageByGoods($goods);
        foreach ($data as $key => $value) {
            $data[$key]['space'] = $this->spec->getGoodSpe($value['goods_id']);
        }
        return array("status"=>1,"message"=>"获取成功","data"=>$data);
    }
}
