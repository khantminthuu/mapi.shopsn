<?php
namespace Common\Logic;
use Common\Model\AdModel;
use Common\Model\NavModel;
use Common\Model\AnnouncementModel;
use Common\Model\StoreModel;
use Common\Model\GoodsClassModel;
use Common\Model\ClassModel;
use Common\Model\CommonModel;
use Common\Logic\GoodsClassLogic;
/**
 * 首页逻辑处理层
 *
 */
class IndexLogic extends AbstractGetDataLogic
{
    /**
     * 构造方法
     * @param array $data
     */
    public function __construct(array $data = [], $split = '')
    {
        $this->data = $data;
        $this->splitKey = $split;
        $this->adModel = new AdModel();
        $this->navModel = new NavModel();
        $this->announcementModel = new AnnouncementModel();
        $this->storeModel = new StoreModel();
        $this->goodsClassModel = new GoodsClassModel();
        $this->classModel   = new ClassModel();
    }
    /**
     * 返回验证数据
     */
    public function getValidateByLogin()
    {
        $message = [
            'classId' => [
                'required' => '必须输入商品分类标识',
            ],
        ];
        return $message;
    }
    public function getValidateOfType()
    {
        $message = [
            'platformId' => [
                'required' => '必须输入平台类型ID',
                'number'   => '平台类型ID必须为数字',
            ],
        ];
        return $message;
    }
    public function getValidateOfName()
    {
        $message = [
            'navName' => [
                'required' => '必须输入导航类名称',
            ],
        ];
        return $message;
    }

    /**
     * 获取首页的信息
     *
     */
    public function getHomeInfo(){
        $data = [];
        //#TODO 1. 轮播图
        $data['banner'] = $this->adModel->getbanner();
        //#TODO 3. 首页导航
 
        $data['nav'] = $this->navModel->getAllNav();
        //#TODO 5. 店铺街
        $data['store'] = $this->storeModel->getAllstore();

       return $data;
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

    public function getcommentGoods(){
        $classSign = $this->data['classId'];
        switch ($classSign){
            case 4:
                $where[ 'class_name' ] = array( 'like','%' . '皇家御饮' . '%' );
                break;
            case 5:
                $where[ 'class_name' ] = array( 'like','%' . '御贡膳品' . '%' );
                break;
            case 6:
                $where[ 'class_name' ] = array( 'like','%' . '滋补养身' . '%' );
                break;
            case 7:
                $where[ 'class_name' ] = array( 'like','%' . '珠宝玉器' . '%' );
                break;
        }
        $field = 'id';
        $id = CommonModel::goods_class()->where($where)->field($field)->find()['id'];
        $data['class_id'] = $id;
        return $data;
//        $goods_class = new GoodsLogic();
//
//        $sort_field = $this->data['sort_field'];
//        $sort_type = $this->data['sort_type'];
//        $page = $this->data['page'];
//        $data = $goods_class->classGoods($id,$page,$sort_field ,$sort_type);
//        return $data;
    }
    /**
     * 获取首页导航菜单
     *
     */
    public function index_nav_lists(){
        return CommonModel::get_modle("Nav")->nav_list($this->data['platformId']);
    }
    /**
     * 获取首页导航菜单ID
     *
     */
    public function nav_name(){
        $vanName = $this->data['navName'];
        $where[ 'class_name' ] = array( 'like','%' . $vanName . '%' );
        $field = 'id';
        $id =  CommonModel::get_modle("GoodsClass")->where($where)->field($field)->find()['id'];
        $data['class_id'] = $id;

        return $data;
    }
    /**
     * 获取导航分类
     *
     */
    public function recommend_class(){
        $data = CommonModel::get_modle("GoodsClass")->get_show_nav();
        return $data;
    }

}
