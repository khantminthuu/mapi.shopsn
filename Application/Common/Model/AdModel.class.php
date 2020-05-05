<?php
namespace Common\Model;


/**
 * 广告模型
 */
class AdModel extends BaseModel
{

    private static $obj;

	public static $id_d;	//id编号

	public static $title_d;	//广告标题

	public static $adLink_d;	//广告链接

	public static $picUrl_d;	//图片路径

	public static $createTime_d;	//创建时间

	public static $sortNum_d;	//排序

	public static $adSpace_id_d;	//广告类型id

	public static $updateTime_d;	//修改时间

	public static $platform_d;	//显示在哪个平台:1.电脑 2.手机

	public static $colorVal_d;	//颜色值

	public static $type_d;	//1: pc端   2：旅游已经废弃  3：会员专区   4：合伙人专区

	public static $startTime_d;	//广告开始显示时间

	public static $endTime_d;	//广告结束显示时间

	public static $enabled_d;	//0, 不启用; 1,启用

	public static $hitNum_d;	//广告点击次数

	public static $page_d;     //分页
    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
    }
    /**
     * @name 获取商城公告
     * @author 山东重友汽车科技有限公司 刘嘉强 < QQ:547999455 >
     * @des 获取商城公告
     * @updated 2017-12-18
     */
    public function getShopAnnouncement(){
        $list = $this
            ->field( 'id,title' )
            ->select();
        if( false === $list){
            return false;
        }
        return $list;
    }
    /**
     * @name 获取商城公告详情
     * @author 山东重友汽车科技有限公司 刘嘉强 < QQ:547999455 >
     * @des 获取商城公告
     * @updated 2017-12-18
     */
    public function getOneShopAnnouncement($id){
        $where['id'] = $id;
        $list = $this
            ->field( 'id,title' )
            ->where($where)
            ->find();
        if( false === $list){
            return false;
        }
        return $list;
    }
    public function getbanner(){
        return $this
            ->field( 'id,title,pic_url,ad_link' )
            ->where( array( 'ad_space_id' => 51 ) )
            ->limit( 3 )
            ->select();
    }

/*မြွေဖြူရှင်မနှင့် လျှို့ဝှက်ဆန်းကြယ်တဲ့မိန်းကလေး*/

}
