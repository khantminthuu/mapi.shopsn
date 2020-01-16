<?php
namespace Common\Logic;
use Common\Model\UserModel;
use Common\Model\InvoiceTypeModel;
use Common\Model\InvoicesAreRaisedModel;
use Common\Model\InvoiceContentModel;
use Common\Model\OrderPackageInvoiceModel;
use Think\SessionGet;
/**
 * 逻辑处理层
 *
 */
class OrderPackageInvoiceLogic extends AbstractGetDataLogic
{
    /**
     * 构造方法
     * @param array $data
     */
    public function __construct(array $data = [], $split = '')
    {
        $this->data = $data;
        $this->splitKey = $split;
        $this->modelObj = new OrderPackageInvoiceModel();
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
    	$invoiceType = new InvoiceTypeModel();
        $invoicesAreRaisedModel = new InvoicesAreRaisedModel();
        $invoiceContentModel = new InvoiceContentModel();
        $post = $this->data;
        $data = [];
        //发票类型  普通和增值
        $data['invoiceType'] = $invoiceType->getInvoiceType();
        //发票抬头  个人 和单位
        $where['user_id'] = SessionGet::getInstance('user_id')->get();
        $field = "id,name";
        $method = select; 
        $data['invoiceCompany'] = $invoicesAreRaisedModel->getInvoiceAreRaised($where,$field ,$method);
        // 发票内容
        $data['content'] = $invoiceContentModel->getInvoiceContent();
        return array("status"=>1,"message"=>"获取成功","data"=>$data);
    }
    //根据订单获取发票数据
    public function getInvoiceInfoByOrder(){
    	$invoiceType = new InvoiceTypeModel();
        $invoicesAreRaisedModel = new InvoicesAreRaisedModel();
        $invoiceContentModel = new InvoiceContentModel();
        $post = $this->data;
        $where['order_id'] = $post['order_id'];
        $field = "id,order_id,raised_id,content_id,type_id";
        $data = $this->modelObj->field($field)->where($where)->$find(); 
        if(empty($data)){
            return array("status"=>0,"message"=>"暂无数据","data"=>$data);
        }
        if ($data['type_id'] == 1) {
            $data['invoiceType'] = $invoiceType->where(['id'=>$data['type_id']])->getField('name');
            $data['content'] = $invoiceContentModel->where(['id'=>$data['content_id']])->getField('name');
            $data['invoiceCompany'] = $invoicesAreRaisedModel->where(['id'=>$data['raised_id']])->getField('name');
        }
        return array("status"=>1,"message"=>"获取成功","data"=>$data);
    }
    //添加订单发票
    public function getInvoicesOrderAdd(){
        $post = $this->data;
        $post['create_time'] = time();
        $post['user_id'] = SessionGet::getInstance('user_id')->get();
        $res = $this->modelObj->add($post);
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
        $res = $this->modelObj->where($where)->save($post);
        if($res ===false){
            return array("status"=>0,"message"=>"失败","data"=>"");
        }
        return array("status"=>1,"message"=>"成功","data"=>array("invoice_id"=>$post['id']));
    }
    
    /**
     * 发票更新
     */
    public function updateInvoice() :bool
    {
    	$data = $this->data;
    	
    	if (empty($data)) {
    		return true;
    	}
    	
    	$sql = $this->buildUpdateSql();
    	
    	try {
    		$status = $this->modelObj->execute($sql);
    	} catch (\Exception $e) {
    		$this->errorMessage = $e->getMessage();
    		$this->modelObj->rollback();
    		return false;
    	}
    	
    	return true;
    }
    
    /**
     * 要更新的字段
     * @return array
     */
    protected function getColumToBeUpdated() :array
    {
    	return [
    		OrderPackageInvoiceModel::$orderId_d,
    		OrderPackageInvoiceModel::$updateTime_d
    	];
    }
    
    /**
     * 要更新的数据【已经解析好的】
     * @return array
     */
    protected function getDataToBeUpdated() :array
    {
    	//批量更新
    	$pasrseData = array();
    	$time = time();
    	foreach ($this->data as $key => $value)
    	{
    		$pasrseData[$value['id']][] = $value['order_id'];
    		
    		$pasrseData[$value['id']][] = $time;
    	}
    	
    	return $pasrseData;
    }
}
