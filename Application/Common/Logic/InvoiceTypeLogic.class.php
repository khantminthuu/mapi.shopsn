<?php
namespace Common\Logic;
use Common\Model\UserModel;
use Common\Model\InvoiceTypeModel;
use Common\Model\InvoicesAreRaisedModel;
use Common\Model\InvoiceContentModel;
use Common\Model\OrderInvoiceModel;
use Think\SessionGet;
/**
 * 逻辑处理层
 *
 */
class InvoiceTypeLogic extends AbstractGetDataLogic
{
    /**
     * 构造方法
     * @param array $data
     */
    public function __construct(array $data = [], $split = '')
    {
        $this->data = $data;
        $this->splitKey = $split;
        $this->modelObj = new InvoiceTypeModel();
        $this->invoicesAreRaisedModel = new InvoicesAreRaisedModel();
        $this->invoiceContentModel = new InvoiceContentModel();
        $this->invoiceOrderModel = new OrderInvoiceModel();

    }
    /**
     * 返回验证数据
     */
    public function getValidateByLogin()
    {
        $message = [
            'page' => [
                'required' => "必须填写  信息",
            ],
        ];
        return $message;
    }
    public function getValidateByOrder() {
        $message = [ 
            'order_id' => [ 
                    'required' => '订单Id参数必须' 
            ] 
        ];
        return $message;
    }
    public function getValidateByAddAreRaised() {
        $message = [ 
            'name' => [ 
                    'required' => '发票抬头参数必须' 
            ] 
        ];
        return $message;
    }
    public function getValidateByDelAreRaised() {
        $message = [ 
            'id' => [ 
                    'required' => 'id必须' 
            ] 
        ];
        return $message;
    }
    public function getValidateByAddnvoices() {
        $message = [ 
            'raised_id' => [ 
                    'required' => '发票抬头id必须' 
            ], 
            'content_id' => [ 
                    'required' => '发票内容id必须' 
            ],
            'type_id' => [ 
                    'required' => '发票类型id必须' 
            ] 
        ];
        return $message;
    }
    public function getValidateBySaveIvoices() {
        $message = [ 
            'id' => [ 
                    'required' => '发票id必须' 
            ],
            'raised_id' => [ 
                    'required' => '发票抬头id必须' 
            ], 
            'content_id' => [ 
                    'required' => '发票内容id必须' 
            ],
            'type_id' => [ 
                    'required' => '发票类型id必须' 
            ] 
        ];
        return $message;
    }
     public function getValidateByAddCapita() {
        $message = [ 
            'company_name' => [ 
                    'required' => '公司名称必须' 
            ],
            'ein' => [ 
                    'required' => '税号必须' 
            ], 
            'opening_bank' => [ 
                    'required' => '开户行必须' 
            ],
            'bank_account' => [ 
                    'required' => '开户账号必须' 
            ],
            'prov_id' => [ 
                    'required' => '省份必须' 
            ],
            'city_id' => [ 
                    'required' => '市区必须' 
            ], 
            'dist_id' => [ 
                    'required' => '地区必须' 
            ],
            'register_address' => [ 
                    'required' => '注册地址必须' 
            ],
            'register_tel' => [ 
                    'required' => '注册电话必须' 
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
        return InvoiceTypeModel::class;
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

    public function getAllInvoiceInfo(){
        $post = $this->data;
        $data = [];
        //发票类型  普通和增值
        $data['invoiceType'] = $this->modelObj->getInvoiceType();
        //发票抬头  个人 和单位
        $where['user_id'] = SessionGet::getInstance('user_id')->get();
        $field = "id,name";
        $method = select; 
        $data['invoiceCompany'] = $this->invoicesAreRaisedModel->getInvoiceAreRaised($where,$field ,$method);
        // 发票内容
        $data['content'] = $this->invoiceContentModel->getInvoiceContent();
        return array("status"=>1,"message"=>"获取成功","data"=>$data);
    }
    //根据订单获取发票数据
    public function getInvoiceInfoByOrder(){
        $post = $this->data;
        $where['order_id'] = $post['order_id'];
        $field = "id,order_id,raised_id,content_id,type_id";
        $method = find;
        $data = $this->invoiceOrderModel->getInvoiceInfo($where,$field,$method); 
        if(empty($data)){
            return array("status"=>0,"message"=>"暂无数据","data"=>$data);
        }
        if ($data['type_id'] == 1) {
            $data['invoiceType'] = $this->modelObj->where(['id'=>$data['type_id']])->getField('name');
            $data['content'] = $this->invoiceContentModel->where(['id'=>$data['content_id']])->getField('name');
            $data['invoiceCompany'] = $this->invoicesAreRaisedModel->where(['id'=>$data['raised_id']])->getField('name');
        }
        return array("status"=>1,"message"=>"获取成功","data"=>$data);
    }
    //添加发票抬头
    public function getInvoicesAreRaisedAdd(){
        $post = $this->data;
        $post['user_id'] = SessionGet::getInstance('user_id')->get();
        
        $post['create_time'] = time();
        $res = $this->invoicesAreRaisedModel->add($post);
        if($res){
            return array("status"=>1,"message"=>"成功","data"=>$res);
        }
        return array("status"=>0,"message"=>"失败","data"=>"");
    }
    //删除发票抬头
    public function getInvoicesAreRaisedDel(){
        $post = $this->data;
        $where['id'] = $post['id'];  
        $res = $this->invoicesAreRaisedModel->where($where)->delete();
        if(!$res){
            return array("status"=>0,"message"=>"失败","data"=>"");
        }
        return array("status"=>1,"message"=>"成功","data"=>"");
    }
    //修改发票抬头
    public function getInvoicesAreRaisedSave(){
        $post = $this->data;
        $where['id'] = $post['id'];  
        $res = $this->invoicesAreRaisedModel->where($where)->save($post);
        if($res===false){
            return array("status"=>0,"message"=>"失败","data"=>"");
        }
        return array("status"=>1,"message"=>"成功","data"=>"");
    }
    //添加订单发票
    public function getInvoicesOrderAdd(){
        $post = $this->data;
        $post['create_time'] = $time = time();
        $post['update_time'] = $time;
        $post['user_id'] = SessionGet::getInstance('user_id')->get();
        $res = $this->invoiceOrderModel->add($post);
        if(!$res){
            return array("status"=>0,"message"=>"失败","data"=>"");
        }
        return array("status"=>1,"message"=>"成功","data"=>array("invoice_id"=>$res));
    }
     //修改订单发票
    public function getInvoicesOrderSave(){
        $post = $this->data;
        $where['id'] = $post['id'];
        $post['update_time'] = time();
        $res = $this->invoiceOrderModel->where($where)->save($post);
        if($res ===false){
            return array("status"=>0,"message"=>"失败","data"=>"");
        }
        return array("status"=>1,"message"=>"成功","data"=>array("invoice_id"=>$post['id']));
    }
    //我的发票
    public function getInvoices(){
        $post = $this->data;
        $where['user_id'] = SessionGet::getInstance('user_id')->get();
        if($post['type'] == 1){
            $data = $this->invoicesAreRaisedModel->field('id,name')->where($where)->select();
            if (empty($data)) {
               return array("status"=>0,"message"=>"暂无数据","data"=>"");
            }
           return array("status"=>1,"message"=>"获取成功","data"=>$data);
        }else{
            $data  = M("capita_invoice")->where($where)->select();
            if (empty($data)) {
               return array("status"=>0,"message"=>"暂无数据","data"=>"");
            }
            $regionDb = M('region');
            foreach ($data as $key => $value) {
                $arr = array($value['prov_id'], $value['city_id'], $value['dist_id']);//查询条件
                $conditon['id'] = array('in', $arr);//查询条件
                $citys = $regionDb->where($conditon)->select();//查询在这些ID内的城市列表
                $newKey = array_column($citys, 'id');//将城市们按照ID排序
                $data[$key]['prov_name'] = $citys[ array_search($value['prov_id'], $newKey)]['name'];//省  在数组中搜索为此建值的省份
                $data[$key]['city_name'] = $citys[ array_search($value['city_id'], $newKey)]['name'];//市  在数组中搜索为此建值的市
                $data[$key]['dist_name'] = $citys[ array_search($value['dist_id'], $newKey)]['name'];//街道 在数组中搜索为此建值的街道
                // $data[$key]['prov_name'] = M("region")->where(['id'=>$value['prov_id']])->getField("name");
                // $data[$key]['city_name'] = M("region")->where(['id'=>$value['city_id']])->getField("name");
                // $data[$key]['dist_name'] = M("region")->where(['id'=>$value['dist_id']])->getField("name");
            }
            return array("status"=>1,"message"=>"获取成功","data"=>$data);
        }
    }
     //我的发票--添加增值发票
    public function getCapitaAdd(){
        $post = $this->data;
        $post['create_time'] = time();
        $post['user_id'] = SessionGet::getInstance('user_id')->get();

        $res = M("capita_invoice")->add($post);
        if(!$res){
            return array("status"=>0,"message"=>"添加失败","data"=>"");
        }
        return array("status"=>1,"message"=>"添加成功","data"=>$res);
    }
     //我的发票--修改增值发票
    public function getCapitaSave(){
        $post = $this->data;
        $where['id'] = $post['id'];
        $post['update_time'] = time();
        
        $res = M("capita_invoice")->where($where)->save($post);
        if($res===false){
            return array("status"=>0,"message"=>"修改失败","data"=>"");
        }
        return array("status"=>1,"message"=>"修改成功","data"=>$res);
    }
     //我的发票--删除增值发票
    public function getCapitaDelete(){
        $post = $this->data;
        $where['id'] = $post['id'];
        $res = M("capita_invoice")->where($where)->delete();
        if(!$res){
            return array("status"=>0,"message"=>"删除失败","data"=>"");
        }
        return array("status"=>1,"message"=>"删除成功","data"=>$res);
    }
}
