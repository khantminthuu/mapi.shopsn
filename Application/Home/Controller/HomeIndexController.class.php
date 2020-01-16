<?php
namespace Home\Controller;

use Common\TraitClass\InitControllerTrait;
use Common\Logic\IndexLogic;
use Common\Logic\AnnouncementLogic;
use Common\Logic\HotWordsLogic;
use Common\Logic\GoodsLogic;
use Validate\CheckParam;


 
class HomeIndexController
{
    use InitControllerTrait;
    /**
     * 架构方法
     * @param array
     * $args   传入的参数数组
     */
    public function __construct(array $args = [])
    {
        $this->args = $args;

        $this->init();
        
        $this->logic = new IndexLogic($args);

        $this->announcementlogic = new AnnouncementLogic($args);
    }
    /**
     * 首页数据
     *
     */
    public function home()
    {
        //检测传值                  //检测方法
//        $checkObj = new CheckParam($this->logic->getValidateByLogin(), $this->args);
//
//        $status = $checkObj->checkParam();
//
//        $this->objController->promptPjax($status, $checkObj->getErrorMessage());

        $ret = $this->logic->getHomeInfo();

        //#TODO 4. 首页公告
        $ret['announcement'] = (new AnnouncementLogic())->getShopAnnouncement();
        
        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret);
    }
    /**
     * 首页推荐商品分类接口
     */
    public function recommendGoodClass(){
        $checkObj = new CheckParam($this->logic->getValidateByLogin(), $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());

        $ret = $this->logic->getcommentGoods();

        $this->objController->promptPjax($ret, $this->announcementlogic->getErrorMessage());

        $this->objController->ajaxReturnData($ret);
    }

    /**
     * 首页导航栏数据
     *
     */
    public function indexNavLists(){
        $checkObj = new CheckParam($this->logic->getValidateOfType(), $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());

        $ret = $this->logic->index_nav_lists();

        $this->objController->promptPjax($ret, $this->announcementlogic->getErrorMessage());

        $this->objController->ajaxReturnData($ret);
    }

    /**
     * 获取首页推荐分类商品的ID
     */
    public function navClassId(){
        $checkObj = new CheckParam($this->logic->getValidateOfName(), $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());

        $ret = $this->logic->nav_name();

        $this->objController->promptPjax($ret, $this->announcementlogic->getErrorMessage());

        $this->objController->ajaxReturnData($ret);
    }
    /**
     * 首页推荐商品分类
     *
     */
    public function recommendClass(){

        $ret = $this->logic->recommend_class();

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret);
    }

 
    //搜索
    public function keyWordSearch()
    {   $goodsLogic = new GoodsLogic($this->args);
    
    	$goods = $goodsLogic->searchGoods();
        
    	$this->objController->promptPjax($goods, $goodsLogic->getErrorMessage());
        $this->objController->ajaxReturnData($goods['data'],$goods['status'],$goods['message']);
    }

    /**
     * 热门关键词搜索
     */
    public function hot_search(){   
        $this->hotWordsLogic = new HotWordsLogic($this->args);
        $res = $this->hotWordsLogic->hotWordSearch();
        $this->objController->promptPjax($res, $this->hotWordsLogic->getErrorMessage());
        $this->objController->ajaxReturnData($res,1,'操作成功');
    }

    /**
     * shopsn公告
     */
    public function announcement()
    {
        if ( IS_POST ) {

        	$ret = $this->announcementlogic->getShopAnnouncement();

            $this->objController->promptPjax($ret, $this->announcementlogic->getErrorMessage());

            $this->objController->ajaxReturnData($ret);
        }
    }

    /**
     * shopsn公告详情
     */
    public function announcement_list()
    {
        if ( IS_POST ) {

            $checkObj = new CheckParam($this->announcementlogic->getValidateByLogin(), $this->args);

            $status = $checkObj->checkParam();

            $this->objController->promptPjax($status, $checkObj->getErrorMessage());

            $ret = $this->announcementlogic->getOneShopAnnouncement();

            $this->objController->promptPjax($ret, $this->announcementlogic->getErrorMessage());

            $this->objController->ajaxReturnData($ret);

        }
    }
}