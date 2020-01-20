<?php


	public function getClassGoods(){
        //#TODO 这里是查询条件
        if (!empty($this->data['class_two'])){
            $where['class_two'] = $this->data['class_two'];
        }
        if (!empty($this->data['class_three'])){
            $where['class_three'] = $this->data['class_three'];
        }
        $where['approval_status'] = '1';
        $where['p_id'] = '0';
        $where['status'] = '0';
        $where['shelves'] = '1';
        // 获取福父id
//        $where = [
//            GoodsModel::$approvalStatus_d => '1',
//            GoodsModel::$pId_d => '0',
//        	GoodsModel::$status_d => '0',
//        ];
//        $p_id = $this->modelObj->where($where)->field("id")->select();
//        if (empty($p_id)) {
//            return array("status"=>0,"message"=>"暂无数据","data"=>"");
//        }
//        $p_where = array_column($p_id, 'id');
        
        $field = 'id,id as goods_id,title,price_member,comment_member,sales_sum,store_id,goods_type,p_id';
//        $g_where = [];
//        $g_where['p_id']  =array("IN",$p_where);
//        $g_where['approval_status']  = '1';
//        $g_where['status']  = '0';
//        $g_where[GoodsModel::$shelves_d] = '1';
        if (!empty($this->data['title'])) {
            $g_where['title']  =['like', '%' . $this->data["title"] . '%'];
        }
        if (!empty($this->data['sort_field'])){
            $fields = $this->data['sort_field'];
            if ($this->data['sort_type'] == "desc"){
                $Order = "$fields  DESC";
            }else{
                $Order = "$fields  ASC";
            }
        } 
        $page = empty($this->data['page'])?0:$this->data['page']; 
        $goods = $this->modelObj->field($field)->where($where)->page($page.",10")->order($Order)->select();
        if (empty($goods)) {
            return array("status"=>0,"message"=>"暂无数据","data"=>"");
        }
        $count =  $this->modelObj->where($where)->count();
        $Page = new \Think\Page($count,10);
        $show = $Page->show();
        $totalPages = $Page->totalPages;
        $retData = $this->goodsImagesModel->getgoodImageByGoods($goods);
        $data['records'] =  $retData;
        $data['count'] =  $count;
        $data['page_size'] =  10;
        $data['totalPages'] =  $totalPages;
        return array("status"=>1,"message"=>"获取成功!","data"=>$data);
    }