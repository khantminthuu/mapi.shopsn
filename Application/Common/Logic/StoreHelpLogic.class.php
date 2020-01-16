<?php
namespace Common\Logic;
use Common\Model\UserModel;
use Common\Model\StoreHelpModel;
use Common\Model\HelpTypeModel;
use Common\TraitClass\GETConfigTrait;
/**
 * 逻辑处理层
 *
 */
class StoreHelpLogic extends AbstractGetDataLogic
{
    use GETConfigTrait;
    /**
     * 构造方法
     * @param array $data
     */
    public function __construct(array $data = [], $split = '')
    {
        $this->data = $data;
        $this->splitKey = $split;
        $this->modelObj = new StoreHelpModel();
        $this->helpObj = new HelpTypeModel();
      
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
     * 返回验证数据
     */
    public function getMessageByClass()
    {
        $message = [
            'type_id' => [
                'required' => '必须输入分类id',
            ],
        ];
        return $message;
    }
    /**
     * 返回验证数据
     */
    public function getMessageById()
    {
        $message = [
            'id' => [
                'required' => '必须输入问题id',
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
//        return BrandModel::class;
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
//        return [
//            UserModel::$userName_d,
//        ];
    }

    public function enter_flow($title){
        $where[StoreHelpModel::$title_d] = $title;
        $where[StoreHelpModel::$status_d] = 1;
        $field = "id,title,info,help_url";
        $data = $this->modelObj->where($where)->field($field)->find();
        $data['info']  = htmlspecialchars_decode($data['info']);
        return $data;
    }
    public function getStoreHelp(){
        $title = $this->data['title'];
        $where["title"] = $title;
        $where["status"] = 1;
        $field = "id,title,info,help_url";
        $data = $this->modelObj->where($where)->field($field)->find();
        $data['info']  = htmlspecialchars_decode($data['info']);
        return $data;
    }
     //获取问题分类
    public function getHelpClass(){
        $where['page_show'] = 1;
        $where['p_id'] = 0;
        $data = $this->helpObj->field("id,name")->where($where)->order("sort")->select();
        foreach ($data as $key => $value) {
            $data[$key]['typeTwo'] = $this->helpObj->field("id,name")->where(['p_id'=>$value['id']])->order("sort")->select();
        }
        return array("status"=>1,"message"=>"获取成功","data"=>$data);
    }
    //获取问题查询列表
    public function getHelpList(){
        $post = $this->data;
        $where['type_id'] = $post['type_id'];
        $where['status'] = 1;
        $data = $this->modelObj->field("id,title")->where($where)->order("sort")->select();
        return array("status"=>1,"message"=>"获取成功","data"=>$data);
    }
     //获取问题查询详情
    public function getHelpInfo(){
        $post = $this->data;
        $where['id'] = $post['id'];
        $data = $this->modelObj->field("id,title,info,help_url,type_id")->where($where)->find();
        $a_where['id'] = array("NEQ",$post['id']);
        $a_where['type_id'] = $data['type_id'];
        $a_where['status'] = 1;
        $data['list'] = $this->modelObj->field("id,title")->where($a_where)->select();
        return array("status"=>1,"message"=>"获取成功","data"=>$data);
    }

    public function getCustomerUrl(){
        $ret = $this->getNoCacheConfig('customer_service_url');
        if(!$ret){
            return array("status"=>0,"message"=>"暂无数据","data"=>'');
        }
        $data['url'] = $ret;
        $arr = explode('=',$ret);
        $data['config_id'] = $arr[1];
        return array("status"=>1,"message"=>"获取成功","data"=>$data);
    }
}
