<?php

namespace Home\Controller;

use Common\Logic\FollowImageLogic;
use Common\TraitClass\InitControllerTrait;
use Validate\CheckParam;

class FollowImageController
{
    use InitControllerTrait;
    public function __construct(array $args=[])
    {
        $this ->args = $args;
        
        $this->_initUser();
        
        $this -> logic = new FollowImageLogic($args);
    }

    /*
     * khantminthu
     * */
    public function getAllUser()
    {

        $ret = $this->logic->getAllUser();
        
        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret);
    }

    public function addFollow()
    {
        $checkObj = new CheckParam($this->logic->getFollowId(), $this->args);
        
        $status = $checkObj->checkParam();
        
        $this->objController->promptPjax($status , $checkObj->getErrorMessage());
        
        $ret = $this->logic->addFollow();

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret['status'] , $ret['message'] , $ret['data']);
    }

}
