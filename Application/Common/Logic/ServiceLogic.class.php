<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/24 0024
 * Time: 14:02
 */

namespace Common\Logic;
use Common\Model\ServiceModel;
use Common\Model\UserModel;
use Common\Model\UserHeaderModel;
use Common\TraitClass\GETConfigTrait;
class ServiceLogic extends AbstractGetDataLogic
{
    use GETConfigTrait;
    /**
     * 构造方法
     *
     * @param array $data
     */
    public function __construct(array $data = [], $split = '') {
        $this->data = $data;
        $this->splitKey = $split;
        $this->modelObj = new ServiceModel ();
    }
    /**
     * 获取支付信息
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
        return ServiceModel::class;
    }
    //获取客服列表
    public function getServiceList(){
        $post = $this->data;
        if(!$post['store_id']){
            $this->errorMessage="参数错误";
            return false;
        }
        $config = $this->getNoCacheConfig('is_center_pattern');
        if($config == 1){
            $data = $this->modelObj->field('user_id as receiver_id')->where(['type'=>0,"store_id"=>$post['store_id']])->find();
        }else{
            $data = $this->modelObj->field('user_id as receiver_id')->where(['type'=>1])->find();
        }
        if(empty($data)){
            $this->errorMessage="暂无数据";
            return false;
        }
        $url = C('CHAT_URL');
        $newUrl = $url.'/mobile.php?receiver_id='. $data['receiver_id'].'&sender_id='.$_SESSION['user_id'];
        return $newUrl;
    }
}