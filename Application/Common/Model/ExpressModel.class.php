<?php
namespace Common\Model;

/**
 * 快递公司模型 
 */
class ExpressModel extends BaseModel
{
    private static $obj;

	public static $id_d;

	public static $name_d;

	public static $status_d;

	public static $code_d;

	public static $letter_d;

	public static $order_d;

	public static $url_d;

	public static $ztState_d;


	public static $tel_d;	//客服电话

	public static $discount_d;	//折扣

    
    public static function getInitnation()
    {
        $name = __CLASS__;
        return self::$obj = !(self::$obj instanceof $name) ? new self() : self::$obj;
    }
    public function getExpress($id){
        $where['id'] = $id;
        $field = 'name';
        $name = $this->where($where)->field($field)->find()['name'];
        return $name;
    }

    public function getExpressInfo($id){
        $where['id'] = $id;
        $field = 'code';
        $code = $this->where($where)->field($field)->find();
        return $code;
    }

    /**
     *  获取订单物流信息
     *
     */
    public function getExpresss($com,$nu) { //子类以及子类的子类可以访问
        // $id         = C('kuaidi_key');
        // $kuaidi_api = C('kuaidi_api');
        $url = 'http://www.kuaidi100.com/query'.'?type='.$com.'&postid='.$nu;
        // $url ='http://api.kuaidi100.com/api?id='.$id.'&com='.$com.'&nu='.$nu.'&show=2&muti=1&order=asc';
        $result = json_decode(file_get_contents($url),true);
        return $result;
    }
}