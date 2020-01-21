<?php
namespace Common\Logic;
use Common\Model\AnnouncementModel;
use Think\Cache;
/**
 * 逻辑处理层
 *
 */
class AnnouncementLogic extends AbstractGetDataLogic
{
    /**
     * 构造方法
     * @param array $data
     */
    public function __construct(array $data = [], $split = '')
    {
        $this->data = $data;
        $this->splitKey = $split;
        $this->modelObj = new AnnouncementModel();
    }
    /**
     * 返回验证数据
     */
    public function getValidateByLogin()
    {
        $message = [
          'id' => [
              'required' => '消息ID必传',
           ],
        ];

        return $message;
    }

    /**
     * @name 获取商城公告
     * @author 山东重友汽车科技有限公司 刘嘉强 < QQ:547999455 >
     * @des 获取商城公告
     * @updated 2017-12-18
     */
    public function getShopAnnouncement(){
    	
    	$cache = Cache::getInstance('', ['expire' => 160]);
    	
    	$key = 'announcement_key_d';
		
    	$data = $cache->get($key);
    	
    	if (!empty($data)) {
    		return $data;
    	}
    	
    	$list = $this->modelObj
	    	->field( 'id,title,create_time,update_time' )
	    	->where('status = 1')
	    	->select();
    	if(empty($list)){
    		return [];
    	}
    	
    	$cache->set($key, $data);
    	
    	return $list;
    }
	
    
    /**
     * @name 获取商城公告详情
     * @author 山东重友汽车科技有限公司 刘嘉强 < QQ:547999455 >
     * @des 获取商城公告
     * @updated 2017-12-18
     */
    public function getOneShopAnnouncement(){
    	
    	$cache = Cache::getInstance('', ['expire' => 160]);
    	
    	$key = 'announcement_key_d_single';
    	
    	$data = $cache->get($key);
    	
    	if (!empty($data)) {
    		return $data;
    	}
    	
    	$where['id'] = $this->data['id'];
    	$list = $this->modelObj
	    	->field( 'id,title,content,create_time, update_time' )
	    	->where($where)
	    	->find();
    	if( false === $list){
    		return [];
    	}
    	
    	$cache->set($key, $data);
    	
    	return $list;
    }








    /**
     * 获取店品牌数据
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
   
}
