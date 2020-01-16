<?php
namespace Common\Model;


/**
 * 导航表逻辑处理
 */
class NavModel extends BaseModel
{


	public static $id_d;	//导航id

	public static $navTitile_d;	//导航菜单标题

	public static $status_d;	//显示状态：0,不显示 1显示

	public static $createTime_d;	//创建时间

	public static $updateTime_d;	//最后一次编辑时间

	public static $link_d;	//连接地址

	public static $sort_d;	//排序：默认10

	public static $type_d;	//导航类型：0默认 不选  1新


	public static $platform_d;	//平台【0 PC 1 WAP,Andriod,IOS】

	public static $pic_d;	//导航logo

    private static $obj;
    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
    }
    /**
     * @name 获取所有的分类模板  ---废弃
     * @author 山东重友汽车科技有限公司 刘嘉强 < QQ:547999455 >
     * @des 获取所有的分类模板
     * @updated 2017-12-19
     */
    public function getAllNav(){
        $where = [
            'status' => '1',
            'platform'=>'1',
        ];
        $list = $this
            ->field('id,nav_titile,link, pic')
            ->where($where)
            ->limit(10)
            ->order('sort ASC')
            ->select();
        if( false === $list){
            return false;
        }
        return $list;
    }
    /**
     * 获取首页导航菜单
     *
     */
    public function nav_list($planfromId){
        $where = [
            'platform' => $planfromId,
            'status'   => 1,
        ];
        $field = 'id,nav_titile,link,pic';
        $data = $this->where($where)->field($field)->limit(10)->select();
        return $data;
    }

    public function nav_name($navName){
        $where[ 'class_name' ] = array( 'like','%' . $navName . '%' );
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
    /**
     * @name 得到推荐的品牌
     * @author 山东重友汽车科技有限公司 刘嘉强 < QQ:547999455 >
     * @des 得到推荐的品牌
     * @updated 2017-12-20
     */

    public function getRecommendNav(){
        $where['status'] = '1';
        $list = $this
            ->field('nav_titile,link')
            ->where($where)
            ->select();
        if( false === $list){
            return false;
        }
        return $list;
    }



}