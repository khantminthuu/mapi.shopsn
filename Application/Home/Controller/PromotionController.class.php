<?php
// +----------------------------------------------------------------------
// | OnlineRetailers [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2003-2023 www.shopsn.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed 亿速网络（http://www.shopsn.net）
// +----------------------------------------------------------------------
// | Author: 王强 <opjklu@126.com>
// +----------------------------------------------------------------------
declare(strict_types = 1);
namespace Home\Controller;

use Common\TraitClass\InitControllerTrait;

class PromotionController
{
    use InitControllerTrait;

    /**
     * 架构方法
     * 
     * @param array $args
     *            传入的参数数组
     */
    public function __construct(array $args = [])
    {
        $this->init();
        
        $this->args = $args;
    }

    /**
     * 最新促销
     */
    public function promotions()
    {
        $adModel = M('ad');
        $goodsModel = M('goods');
        $goods_images = M('goods_images');
        // 头部广告图取3张
        $top_img = $adModel->field('id,ad_link,title,pic_url')
            ->where(array(
            'ad_space_id' => 4
        ))
            ->limit(3)
            ->select();
        // 读取配置里的的图片访问域名地址
        $img_url = C('img_url');
        // 广告图下两个产品一个广告图
        $top_goods = $goodsModel->field('id,title,price_market,description,p_id')
            ->where([
            'status' => 2,
            'p_id' => [
                'NEQ',
                0
            ]
        ])
            ->limit(2)
            ->select(); // 2表示最新促销
        $top_goods_img = $adModel->order('rand()')
            ->field('id,ad_link,title,pic_url')
            ->where(array(
            'ad_space_id' => 4
        ))
            ->limit(1)
            ->select();
        if (! empty($top_goods)) {
            $arr = array_column($top_goods, 'p_id');
            $condition['goods_id'] = array(
                'in',
                $arr
            );
            $img = $goods_images->field('pic_url')
                ->where($condition)
                ->limit(4)
                ->select();
            foreach ($img as $k => $v) {
                $top_goods[$k]['img'] = $v['pic_url'];
            }
        }
        
        // 推荐特卖
        $recommend_hot = $goodsModel->field('id,title,update_time,price_market,description,price_member,p_id')
            ->where([
            'status' => 2,
            'p_id' => [
                'NEQ',
                0
            ]
        ])
            ->limit(4)
            ->select(); // 2表示最新促销
        if (! empty($recommend_hot)) {
            foreach ($recommend_hot as $k => $v) {
                $img = $goods_images->field('pic_url')
                    ->where([
                    'goods_id' => $v['p_id']
                ])
                    ->find();
                $recommend_hot[$k]['img'] = $img['pic_url'];
                $recommend_hot[$k]['pic_url'] = $img['pic_url'];
            }
        }
        // 热卖促销
        $hot_promotion_img = $adModel->order('rand()')
            ->field('id,ad_link,title,pic_url')
            ->where(array(
            'ad_space_id' => 4
        ))
            ->limit(1)
            ->select();
        $hot_promotion = $goodsModel->order('rand()')
            ->field('id,title,price_market,price_member,description,p_id')
            ->where([
            'status' => 2,
            'p_id' => [
                'NEQ',
                0
            ]
        ])
            ->limit(4)
            ->select(); // 2表示最新促销
        foreach ($hot_promotion as $k => $v) {
            $image = $goods_images->field('pic_url')
                ->where([
                'goods_id=' => $v['p_id']
            ])
                ->find();
            $hot_promotion[$k]['image'] = $image['pic_url'];
        }
        // 广告图下商品分类---hide_status=1显示 fid=3办公用品，shoutui=0推荐
        $goodsClassModel = M('goods_class');
        $classes = $goodsClassModel->field('id,class_name,pic_url,description')
            ->where(array(
            'hide_status' => 1,
            'fid' => 0
        ))
            ->order('rand()')
            ->limit(3)
            ->select();
        // 第一个类型下的四个子类
        if (! empty($class)) {
            $children_class = $goodsClassModel->order('rand()')
                ->field('class_name')
                ->where([
                'fid' => $classes[0]['id']
            ])
                ->limit(4)
                ->select();
        }
        // 特卖促销
        $sale_promotion = $goodsModel->order('rand()')
            ->field('id,title,update_time,price_market,description,price_member,p_id')
            ->where([
            'status' => 2,
            'p_id' => [
                'NEQ',
                0
            ]
        ])
            ->limit(4)
            ->select(); // 2表示最新促销
        if (! empty($sale_promotion)) {
            $arr = array_column($sale_promotion, 'p_id');
            $condition['goods_id'] = array(
                'in',
                $arr
            );
            $img = $goods_images->field('pic_url')
                ->where($condition)
                ->limit(3)
                ->select();
            foreach ($img as $k => $v) {
                $sale_promotion[$k]['img'] = $v['pic_url'];
            }
        }
        $data = array(
            'top_img' => $top_img, // 头部广告图
            'top_goods' => $top_goods, // 广告图下2个产品
            'top_goods_img' => $top_goods_img, // 广告图下1个广告图
            'img_url' => $img_url, // 图片域名
            'hot_promotion_img' => $hot_promotion_img, // 热卖促销下广告图
            'hot_promotion' => $hot_promotion, // 热卖促销
            'recommend_hot' => $recommend_hot, // 推荐特卖
            'classes' => $classes, // 类型展示3个
            'children_class' => $children_class, // 子类
            'sale_promotion' => $sale_promotion // 特卖促销
        );
        $this->objController->ajaxReturnData($data);
    }
}