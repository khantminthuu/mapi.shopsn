<?php

namespace Home\Controller;

use Common\Logic\MyHomeLogic;

class MyHomeController {

//    public function __construct()
//    {
//
//
//    }



    public function getHomeLists()

    {
        $ret =$this->getHomeLists();

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret);

        




    }

}





?>
