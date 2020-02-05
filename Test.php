<?php

public function getPanicDetail(){
    $post = $this->data;
    $data = $this->modelObj->alias('a')
        ->field('a.id,a.panic_price,a.end_time,a.quantity_limit,b.title,b.p_id,b.store_id,d.shop_name,d.store_logo,d.description')
        ->join('left join db_goods as b on a.goods_id = b.id')
        ->join('left join db_store as d on d.id = b.store_id')
        ->where(['a.goods_id'=>$post['goods_id']])
        ->find();
    if(!$data){
        $this->errorMessage = '数据异常';
        return false;
    }
    $data['goods_count'] = M('goods')->where(['store_id'=>$data['store_id'],'p_id'=>0])->count();
    $data['follow'] = M('storeFollow')->where(['id'=>$data['store_id']])->count();
    $data['img'] = M('goodsImages')->where(['goods_id'=>$data['p_id'],'is_thumb'=>0])->getField('pic_url',true);
    return $data;
}
