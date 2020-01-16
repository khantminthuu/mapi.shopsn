<?php
namespace Common\Model;
/**
 * Created by PhpStorm.获取Model实例
 * User: 刘嘉强
 * Date: 2018-01-25
 * Time: 9:36
 */
use Common\Controller\RegisterController;

class CommonModel {


    /**
     * 得到快递方式模型
     *
     */
    static function getExpressModel(){

        $key = 'expressModel';

        $model = RegisterController::get($key);

        if (!$model){

            $model = new \Common\Model\ExpressModel();

            RegisterController::set($key,$model);
        }
        return $model;
    }

    /**
     * 得到用户地址模型
     *
     */
    static function getUserAddressModel(){
        $key = 'userAddressModel';

        $model = RegisterController::get($key);

        if (!$model){

            $model = new \Common\Model\UserAddressModel();

            RegisterController::set($key,$model);
        }
        return $model;
    }
    /**
     * 中国地区表模型
     *
     */
    static function getRegion(){
        $key = 'region';

        $model = RegisterController::get($key);

        if (!$model){

            $model = new \Common\Model\RegionModel();

            RegisterController::set($key,$model);
        }
        return $model;
    }

    /**
     * 订单模型
     *
     */
    static function order(){
        $key = 'order';

        $model = RegisterController::get($key);

        if (!$model){

            $model = new \Common\Model\OrderModel();

            RegisterController::set($key,$model);
        }
        return $model;
    }

    /**
     *  用户关注店铺模型
     *
     */
    static function store_follow(){
        $key = 'store_follow';

        $model = RegisterController::get($key);

        if (!$model){

            $model = new \Common\Model\StoreFollowModel();

            RegisterController::set($key,$model);
        }
        return $model;
    }

    /**
     *  用户关注店铺模型
     *
     */
    static function store_jion_company(){
        $key = 'store_jion_company';

        $model = RegisterController::get($key);

        if (!$model){

            $model = new \Common\Model\StoreJoinCompanyModel();

            RegisterController::set($key,$model);
        }
        return $model;
    }

    /**
     *  个人入驻
     *
     */
    static function store_person(){
        $key = 'store_person';

        $model = RegisterController::get($key);

        if (!$model){

            $model = new \Common\Model\StorePersonModel();

            RegisterController::set($key,$model);
        }
        return $model;
    }
    /**
     *  店铺地址模型
     *
     */
    static function store_address(){
        $key = 'store_address';

        $model = RegisterController::get($key);

        if (!$model){

            $model = new \Common\Model\StoreAddressModel();

            RegisterController::set($key,$model);
        }
        return $model;
    }

    /**
     *  店铺模型
     *
     */
    static function store(){
        $key = 'store';

        $model = RegisterController::get($key);

        if (!$model){

            $model = new \Common\Model\StoreModel();

            RegisterController::set($key,$model);
        }
        return $model;
    }
    /**
     *  店铺评分模型
     *
     */
    static function store_evaluate(){
        $key = 'store_evaluate';

        $model = RegisterController::get($key);

        if (!$model){

            $model = new \Common\Model\StoreEvaluateModel();

            RegisterController::set($key,$model);
        }
        return $model;
    }

    /**
     *  商品模型
     *
     */
    static function good_model(){
        $key = 'goods_model';

        $model = RegisterController::get($key);

        if (!$model){

            $model = new \Common\Model\GoodsModel();

            RegisterController::set($key,$model);
        }
        return $model;
    }
    /**
     *  商品活动限购表
     *
     */
    static public function panic(){
        $key = 'panic';
        $model = RegisterController::get($key);

        if (!$model){

            $model = new \Common\Model\PanicModel();

            RegisterController::set($key,$model);
        }
        return $model;
    }

    /**
     *  快递公司信息模型
     *
     */
    static public function express(){
        $key = 'express';
        $model = RegisterController::get($key);

        if (!$model){

            $model = new \Common\Model\ExpressModel();

            RegisterController::set($key,$model);
        }
        return $model;
    }

    /**
     *  品牌列表模型
     *
     */
    static public function brand(){
        $key = 'brand';
        $model = RegisterController::get($key);

        if (!$model){

            $model = new \Common\Model\BrandModel();

            RegisterController::set($key,$model);
        }
        return $model;
    }
    /**
     *  商品分类模型
     *
     */
    static public function goods_class(){
        $key = 'goods_class';
        $model = RegisterController::get($key);

        if (!$model){

            $model = new \Common\Model\GoodsClassModel();

            RegisterController::set($key,$model);
        }
        return $model;
    }
    /**
     *  获取模型实例的公共方法
     *
     */
    static public function get_modle($str){
        $model_name = $str.'Model';
        $classObj = 'Common\\Model\\'.$model_name;
        $model = RegisterController::get($classObj);
        if (!$model){

            $model = new $classObj;

            RegisterController::set($classObj,$model);
        }
        return $model;
    }



}