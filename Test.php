<?php
public function shopInfo() 
    {
        $checkObj = new CheckParam ( $this->logic->getValidateByLogin (), $this->args );
            
        $status = $checkObj->checkParam ();
            
        $this->objController->promptPjax ( $status, $checkObj->getErrorMessage () );
            
        $ret = $this->logic->getShopInfo ();
        echo "<pre>";
        print_r($ret);
        die;
        $this->objController->promptPjax ( $ret, $this->logic->getErrorMessage () );
            
        $this->objController->ajaxReturnData ( $ret );
    }


     public function getShopInfo(){
        $this->searchTemporary = [
            StoreModel::$id_d => $this->data['id'],
        ];
        $retData = parent::getFindOne();
        //获取店铺所有宝贝数量
        $retData['goodsNumber'] = $this->goodModel->getShopGoodNumber($this->data['id']);
        if (empty($retData)){
            $this->errorMessage = '暂无数据';
            return [];
        }
        return $retData;
    }